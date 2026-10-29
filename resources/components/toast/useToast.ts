import { router } from '@inertiajs/vue3'
import { type App, createApp, markRaw } from 'vue'
import { toast as vueSoonerToast } from 'vue-sonner'
import {
    Toast,
    type ToastActionPayload,
    type ToastActionType,
    type ToastOptions,
    type ToastPayload,
    type ToastPromiseMessages,
    ToastProvider,
    type ToastType,
} from './'

const toastTypes: ToastType[] = ['error', 'success', 'warning', 'info']
const toastActionTypes: ToastActionType[] = ['copy', 'redirect', 'visit']

/** Errors and warnings stay up longer, so there is time to read them. */
export const defaultDurations = {
    success: 4000,
    info: 4000,
    warning: 6000,
    error: 8000,
} satisfies Record<ToastType, number>

let unregisterRouterFlash: (() => void) | null = null
let toasterApp: App | null = null
let toasterContainer: HTMLElement | null = null
let isToasterRegistered = false
let nextToastId = 0

const isRecord = (value: unknown): value is Record<string, unknown> => typeof value === 'object' && value !== null

const isOptional = (value: unknown, type: 'string' | 'number' | 'boolean'): boolean => value === undefined || typeof value === type

const isValidDuration = (value: unknown): boolean => value === undefined || (typeof value === 'number' && Number.isFinite(value) && value >= 0)

const isToastActionPayload = (value: unknown): value is ToastActionPayload => {
    if (!isRecord(value)) {
        return false
    }

    const { type, payload, label } = value

    return (
        typeof type === 'string' &&
        toastActionTypes.includes(type as ToastActionType) &&
        typeof payload === 'string' &&
        (label === null || label === undefined || typeof label === 'string')
    )
}

const isToastPayload = (value: unknown): value is ToastPayload => {
    if (!isRecord(value)) {
        return false
    }

    const { type, message, description, actions, duration, dismissible } = value

    return (
        typeof type === 'string' &&
        toastTypes.includes(type as ToastType) &&
        typeof message === 'string' &&
        isOptional(description, 'string') &&
        (actions === undefined || (Array.isArray(actions) && actions.every(isToastActionPayload))) &&
        isValidDuration(duration) &&
        isOptional(dismissible, 'boolean')
    )
}

const flashedToasts = (rawToasts: unknown): ToastPayload[] => (Array.isArray(rawToasts) ? rawToasts.filter(isToastPayload) : [])

const durationFor = (toast: ToastPayload): number => {
    const duration = toast.duration ?? defaultDurations[toast.type]

    return duration === 0 ? Number.POSITIVE_INFINITY : duration
}

const toastOptions = (id: string | number, toast: ToastPayload) => ({
    id,
    componentProps: { toast, toastId: id },
    classes: { content: 'flex-1' },
    duration: durationFor(toast),
    closeButton: toast.dismissible ?? true,
    dismissible: toast.dismissible ?? true,
})

/**
 * Show a toast and return its id. Passing an id that is already on screen replaces that toast.
 */
export const pushToast = (toast: ToastPayload, id: string | number = `toast-${++nextToastId}`): string | number => {
    if (!isValidDuration(toast.duration)) {
        throw new RangeError('Expected a finite, non-negative number of milliseconds.')
    }

    vueSoonerToast[toast.type](markRaw(Toast), toastOptions(id, toast))

    return id
}

const pushTyped =
    (type: ToastType) =>
    (message: string, { id, ...options }: ToastOptions = {}): string | number =>
        pushToast({ ...options, type, message }, id)

export const dismissToast = (id?: string | number): void => {
    vueSoonerToast.dismiss(id)
}

/**
 * Show a loading toast while the promise runs, then swap it for a success or error toast.
 * Resolves or rejects with the promise's own result, so callers can still await or catch it.
 */
export const promiseToast = async <T>(promise: Promise<T> | (() => Promise<T>), messages: ToastPromiseMessages<T>): Promise<T> => {
    const loadingId = `toast-${++nextToastId}`
    const loading: ToastPayload = { type: 'info', message: messages.loading, dismissible: false }

    vueSoonerToast.loading(markRaw(Toast), toastOptions(loadingId, loading))

    let result: { success: true; data: T } | { success: false; error: unknown }

    try {
        result = { success: true, data: await (typeof promise === 'function' ? promise() : promise) }
    } catch (error) {
        result = { success: false, error }
    }

    // vue-sonner reads a toast's time left only when it mounts, so the result gets a fresh toast instead of updating the loading one.
    dismissToast(loadingId)

    try {
        if (result.success) {
            pushToast({ type: 'success', message: typeof messages.success === 'function' ? messages.success(result.data) : messages.success })
        } else {
            pushToast({ type: 'error', message: typeof messages.error === 'function' ? messages.error(result.error) : messages.error })
        }
    } catch {
        // A toast formatter must not change the promise's result.
    }

    if (result.success) {
        return result.data
    }

    throw result.error
}

const mountToaster = (): void => {
    // A single Toaster outside the page tree survives layout changes, so no toast is dropped between layouts.
    toasterContainer = document.createElement('div')
    document.body.appendChild(toasterContainer)
    toasterApp = createApp(ToastProvider)
    toasterApp.mount(toasterContainer)
}

export const unregisterToaster = (): void => {
    unregisterRouterFlash?.()
    unregisterRouterFlash = null
    toasterApp?.unmount()
    toasterApp = null
    toasterContainer?.remove()
    toasterContainer = null
    isToasterRegistered = false
}

export const registerToaster = () => {
    if (isToasterRegistered) {
        return unregisterToaster
    }

    mountToaster()

    // Inertia fires `flash` for the initial page and every visit that carries flash data.
    const removeListener = router.on('flash', (event) => flashedToasts(event.detail.flash.toasts).forEach((toast) => pushToast(toast)))

    if (typeof removeListener === 'function') {
        unregisterRouterFlash = removeListener
    }

    isToasterRegistered = true

    return unregisterToaster
}

if (import.meta.hot) {
    import.meta.hot.dispose(() => unregisterToaster())
}

export const useToast = () => ({
    register: registerToaster,
    unregister: unregisterToaster,
    push: pushToast,
    success: pushTyped('success'),
    error: pushTyped('error'),
    warning: pushTyped('warning'),
    info: pushTyped('info'),
    promise: promiseToast,
    dismiss: dismissToast,
})
