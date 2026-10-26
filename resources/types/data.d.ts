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
