import type { PageMeta } from '@/types/data'
import type { LucideIcon } from '@lucide/vue'
import type { User } from './models'

export type Nullable<T> = T | null
export type Optional<T> = T | undefined
export type Callable<T = void, U extends any[] = []> = (...args: U) => T
export type VoidCallable<U extends any[] = []> = Callable<void, U>

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

export interface NavGroup {
    label?: string
    items: NavItem[]
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string
    auth: Auth
    canRegister: boolean
    sidebarOpen: boolean
    meta?: PageMeta
}
