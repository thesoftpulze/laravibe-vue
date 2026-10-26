import type { AppPageProps } from '@/types'
import type { User } from '@/types/models'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export const useAuth = () => {
    const page = usePage<AppPageProps>()

    /** Nullable computed — safe for public pages. */
    const user = computed(() => page.props.auth.user)

    /** `true` when a user session is active. */
    const isAuthenticated = computed(() => page.props.auth.user !== null)

    /**
     * Check a UI-relevant ability exposed via shared props.
     * Returns `false` for unknown ability keys.
     */
    const can = (ability: string): boolean => page.props.auth.can[ability] ?? false

    /**
     * Returns the authenticated user or throws.
     * Use on pages that are always behind an auth middleware.
     */
    const requireUser = (): User => {
        const authUser = page.props.auth.user

        if (authUser === null) {
            throw new Error('requireUser: no authenticated user found.')
        }

        return authUser
    }

    return { user, isAuthenticated, can, requireUser }
}
