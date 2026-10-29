<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { isNavItemActive } from '@/lib/helpers'
import type { NavGroup } from '@/types'
import { Link } from '@inertiajs/vue3'

defineProps<{
    groups: NavGroup[]
}>()
</script>

<template>
    <SidebarGroup v-for="(group, index) in groups" :key="group.label ?? index" class="px-2 py-0">
        <SidebarGroupLabel v-if="group.label">{{ group.label }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in group.items" :key="item.href">
                <SidebarMenuButton as-child :is-active="isNavItemActive(item)" :tooltip="item.title">
                    <Link :href="item.href" prefetch>
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
