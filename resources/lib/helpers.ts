import AdministrationLayout from '@/layouts/AdministrationLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import GuestLayout from '@/layouts/GuestLayout.vue'
import type { Callable, NavItem, VoidCallable } from '@/types'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { DefineComponent } from 'vue'

export const resolveLayout = (name: string) => {
    if (name.startsWith('administration')) return AdministrationLayout
    if (name.startsWith('auth')) return AuthLayout
    return GuestLayout
}

export const resolveView = (name: string) => resolvePageComponent(`../views/${name}.vue`, import.meta.glob<DefineComponent>('../views/**/*.vue'))

export const isCallable = (value: unknown): value is Callable | VoidCallable => typeof value === 'function'

export const isNavItemActive = (item: NavItem): boolean => (isCallable(item.isActive) ? item.isActive() : !!item.isActive)

/**
 * Format an API date-time in the viewer's locale and timezone.
 */
export const formatDate = (value: string, options: Intl.DateTimeFormatOptions = { dateStyle: 'medium' }): string =>
    new Intl.DateTimeFormat(undefined, options).format(new Date(value))

/**
 * Describe an API date-time relative to now, such as "3 days ago".
 */
export const formatRelativeDate = (value: string): string => {
    const seconds = Math.round((new Date(value).getTime() - Date.now()) / 1000)
    const units: [Intl.RelativeTimeFormatUnit, number][] = [
        ['year', 31_536_000],
        ['month', 2_592_000],
        ['week', 604_800],
        ['day', 86_400],
        ['hour', 3_600],
        ['minute', 60],
    ]
    const [unit, size] = units.find(([, size]) => Math.abs(seconds) >= size) ?? ['second', 1]

    return new Intl.RelativeTimeFormat(undefined, { numeric: 'auto' }).format(Math.round(seconds / size), unit)
}
