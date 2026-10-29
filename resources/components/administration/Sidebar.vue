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
    useSidebar,
} from '@/components/ui/sidebar'
import { cn } from '@/lib/utils'
import { dashboard as administrationDashboard } from '@/routes/administration'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import NavFooter from './NavFooter.vue'
import NavMain from './NavMain.vue'
import NavUser from './NavUser.vue'
import { useAdministrationNavigation } from './navigation'

const { mainNavGroups, footerNavItems } = useAdministrationNavigation()

const { state } = useSidebar()
const expanded = computed(() => state.value === 'expanded')
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child :class="cn('items-center', !expanded && 'justify-center')">
                        <Link :href="administrationDashboard.url()">
                            <AppLogo :icon-only="!expanded" :class="expanded ? 'text-lg' : 'text-xl'" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :groups="mainNavGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
