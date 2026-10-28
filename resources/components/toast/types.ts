export type ToastActionType = 'copy' | 'redirect' | 'visit'

export type ToastType = 'error' | 'success' | 'warning' | 'info'

export interface ToastActionPayload {
    type: ToastActionType
    payload: string
    label?: string | null
}

export interface ToastPayload {
    type: ToastType
    message: string
    description?: string
    actions?: ToastActionPayload[]
    /** Milliseconds the toast stays open. 0 keeps it open until it is closed. */
    duration?: number
    /** Whether the user can close the toast before it times out. */
    dismissible?: boolean
}

export type ToastOptions = Omit<ToastPayload, 'type' | 'message'> & {
    /** A known id, so the toast can be dismissed later. Reusing an id on screen replaces it but keeps its timer. */
    id?: string | number
}

export interface ToastPromiseMessages<T> {
    loading: string
    success: string | ((data: T) => string)
    error: string | ((error: unknown) => string)
}
