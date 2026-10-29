import './css/app.css'

import { registerToaster } from '@/components/toast'
import { initializeTheme } from '@/composables/useAppearance'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolveLayout, resolveView } from './lib/helpers'

const appName = import.meta.env.VITE_APP_NAME || 'Laravibe'

void createInertiaApp({
    // The shared `name` follows the site name setting; the build-time name covers pages rendered without props.
    title: (title, page) => {
        const name = typeof page?.props.name === 'string' ? page.props.name : appName

        return title ? `${title} - ${name}` : name
    },
    resolve: (name: string) => resolveView(name),
    // Pages that declare their own `layout` keep it; everything else gets the default by name.
    layout: (name: string) => resolveLayout(name),
    withApp(_app, { ssr }) {
        if (!ssr) {
            registerToaster()
        }
    },
    progress: {
        color: '#4B5563',
    },
})

if (!import.meta.env.SSR) {
    // This sets light / dark mode on initial page load.
    initializeTheme()
}
