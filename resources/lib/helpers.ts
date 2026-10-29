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
