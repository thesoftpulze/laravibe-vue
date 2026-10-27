import { Nullable } from '.'

export interface Breadcrumb {
    label: string
    href: Nullable<string>
}

export interface PageMeta {
    heading?: string
    subheading?: string

    title?: string

    breadcrumbs?: Breadcrumb[]
}

export interface TwoFactorStatus {
    enabled: boolean
    pendingConfirmation: boolean
}

export interface BrowserSession {
    platform: string
    browser: string
    isDesktop: boolean
    isCurrentDevice: boolean
    lastActive: string
    ipAddress: Nullable<string>
}

export interface Paginated<T> {
    data: T[]
    links: {
        first: Nullable<string>
        last: Nullable<string>
        prev: Nullable<string>
        next: Nullable<string>
    }
    meta: {
        current_page: number
        from: Nullable<number>
        last_page: number
        path: string
        per_page: number
        to: Nullable<number>
        total: number
    }
}
