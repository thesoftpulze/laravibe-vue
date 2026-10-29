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

export type UserSortColumn = 'name' | 'created_at'
export type SortDirection = 'asc' | 'desc'

export interface UserFilters {
    search: Nullable<string>
    role: Nullable<'admin' | 'user'>
    verification: Nullable<'verified' | 'unverified'>
    sort: Nullable<UserSortColumn>
    direction: Nullable<SortDirection>
}

export interface UserStatistics {
    total: number
    administrators: number
    unverified: number
    joinedThisWeek: number
}

export interface GeneralSettings {
    siteName: Nullable<string>
    tagline: Nullable<string>
    supportEmail: Nullable<string>
}
