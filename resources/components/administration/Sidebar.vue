<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue'
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarRail,
    useSidebar,
} from '@/components/ui/sidebar'
import { cn } from '@/lib/utils'
import { type NavItem } from '@/types'
import { dashboard as administrationDashboard } from '@/routes/administration'
import administrationUsers from '@/routes/administration/users'
import { Link } from '@inertiajs/vue3'
import { CircleGauge, FolderGit, Users } from '@lucide/vue'
import NavFooter from './NavFooter.vue'
import NavMain from './NavMain.vue'
import NavUser from './NavUser.vue'

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: administrationDashboard.url(),
        icon: CircleGauge,
    },
    {
        title: 'Users',
        href: administrationUsers.index.url(),
        icon: Users,
    },
]

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/thesoftpulze/laravibe-vue',
        icon: FolderGit,
    },
]

const { open } = useSidebar()
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child :class="cn('items-center', !open && 'justify-center')">
                        <Link :href="administrationDashboard.url()">
                            <AppLogo :icon-only="!open" :class="open ? 'text-lg' : 'text-xl'" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>

        <SidebarRail />
    </Sidebar>
    <slot />
</template>
