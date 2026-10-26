import { router } from '@inertiajs/vue3'
import { markRaw } from 'vue'
import { toast as vueSoonerToast } from 'vue-sonner'
import { Toast, type ToastActionPayload, type ToastActionType, type ToastPayload, type ToastType } from './'

const toastTypes: ToastType[] = ['error', 'success', 'warning', 'info']
const toastActionTypes: ToastActionType[] = ['copy', 'redirect']

let unregisterRouterFlash: (() => void) | null = null
let isToasterRegistered = false

const isRecord = (value: unknown): value is Record<string, unknown> => typeof value === 'object' && value !== null

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

    const { type, message, actions, duration, dismissible } = value

    if (typeof type !== 'string' || !toastTypes.includes(type as ToastType) || typeof message !== 'string') {
        return false
    }

    if (actions === undefined) {
        return (duration === undefined || typeof duration === 'number') && (dismissible === undefined || typeof dismissible === 'boolean')
    }

    return (
        Array.isArray(actions) &&
        actions.every(isToastActionPayload) &&
        (duration === undefined || typeof duration === 'number') &&
        (dismissible === undefined || typeof dismissible === 'boolean')
    )
}

const flashedToasts = (rawToasts: unknown): ToastPayload[] => (Array.isArray(rawToasts) ? rawToasts.filter(isToastPayload) : [])

export const pushToast = (toast: ToastPayload) =>
    vueSoonerToast[toast.type](markRaw(Toast), {
        componentProps: { toast },
        classes: { content: 'flex-1' },
        duration: toast.duration,
        closeButton: toast.dismissible,
    })

export const unregisterToaster = (): void => {
    unregisterRouterFlash?.()
    unregisterRouterFlash = null
    isToasterRegistered = false
}

export const registerToaster = () => {
    if (isToasterRegistered) {
        return unregisterToaster
    }

    // Inertia fires `flash` for the initial page and every visit that carries flash data.
    const removeListener = router.on('flash', (event) => flashedToasts(event.detail.flash.toasts).forEach(pushToast))

    if (typeof removeListener === 'function') {
        unregisterRouterFlash = removeListener
    }

    isToasterRegistered = true

    return unregisterToaster
}

if (import.meta.hot) {
    import.meta.hot.dispose(() => unregisterToaster())
}

export const useToast = () => ({ register: registerToaster, unregister: unregisterToaster, push: pushToast })
