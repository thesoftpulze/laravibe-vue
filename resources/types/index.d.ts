import type { PageMeta } from '@/types/data'
import type { LucideIcon } from '@lucide/vue'
import type { User } from './models'

// Utility Types
export type Nullable<T> = T | null
export type Optional<T> = T | undefined
export type Callable<T = void, U extends any[] = []> = (...args: U) => T
export type VoidCallable<U extends any[] = []> = Callable<void, U>

// Application Specific Types
export interface Auth {
    user: User | null
    can: Record<string, boolean>
}

export interface NavItem {
    title: string
    href: string
    icon?: LucideIcon
    isActive?: boolean | Callable<boolean>
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string
    quote: { message: string; author: string }
    auth: Auth
    sidebarOpen: boolean
    meta?: PageMeta
}
