<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue'
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu'
import type { User } from '@/types/models'
import { account, logout } from '@/routes'
import { dashboard as administrationDashboard } from '@/routes/administration'
import { useAuth } from '@/composables/useAuth'
import { Link, router, usePage } from '@inertiajs/vue3'
import { CircleGauge, LogOut, User as UserIcon } from '@lucide/vue'

interface Props {
    user: User
}

const handleLogout = () => {
    router.flushAll()
}

const { can } = useAuth()

const isInAdministration = () => usePage().url.startsWith('/administration')

defineProps<Props>()
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem v-if="can('accessAdministration') && !isInAdministration()" as-child>
            <Link class="block w-full" :href="administrationDashboard.url()" prefetch as="button">
                <CircleGauge class="mr-2 h-4 w-4" />
                Administration
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem as-child>
            <Link class="block w-full" :href="account.url()" prefetch as="button">
                <UserIcon class="mr-2 h-4 w-4" />
                Account
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem as-child>
        <Link class="block w-full" method="post" :href="logout.url()" @click="handleLogout" as="button">
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
