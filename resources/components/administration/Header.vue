<script setup lang="ts">
import AppearanceToggle from '@/components/AppearanceToggle.vue'
import Breadcrumbs from '@/components/Breadcrumbs.vue'
import { Button } from '@/components/ui/button'
import { Kbd, KbdGroup } from '@/components/ui/kbd'
import { Separator } from '@/components/ui/separator'
import { SidebarTrigger } from '@/components/ui/sidebar'
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip'
import { usePageMeta } from '@/composables/usePageMeta'
import { home } from '@/routes'
import type { Breadcrumb } from '@/types/data'
import { Link } from '@inertiajs/vue3'
import { House, Search } from '@lucide/vue'
import { computed, onMounted, ref } from 'vue'
import { useAdministrationNavigation } from './navigation'

const emit = defineEmits<{
    search: []
}>()

const meta = usePageMeta()
const { activeItem } = useAdministrationNavigation()

// Pages without their own breadcrumbs show the navigation item they belong to.
const breadcrumbs = computed<Breadcrumb[]>(() => {
    if (meta.value.breadcrumbs?.length) {
        return meta.value.breadcrumbs
    }

    return activeItem.value ? [{ label: activeItem.value.title, href: null }] : []
})

const isMac = ref(false)

onMounted(() => {
    isMac.value = /Mac|iPhone|iPad/.test(navigator.userAgent)
})
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-peeking/sidebar-wrapper:h-12 group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex min-w-0 items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs.length > 0">
                <Separator orientation="vertical" class="mr-1 h-4!" />
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="flex items-center gap-1">
            <Button
                variant="outline"
                size="sm"
                class="size-8 px-0 text-muted-foreground sm:w-56 sm:justify-between sm:px-3"
                aria-label="Search"
                @click="emit('search')"
            >
                <span class="flex items-center gap-2">
                    <Search />
                    <span class="hidden font-normal sm:inline">Search…</span>
                </span>
                <KbdGroup class="hidden sm:inline-flex">
                    <Kbd>{{ isMac ? '⌘' : 'Ctrl' }}</Kbd>
                    <Kbd>K</Kbd>
                </KbdGroup>
            </Button>

            <AppearanceToggle />

            <Tooltip>
                <TooltipTrigger as-child>
                    <Button variant="ghost" size="icon" class="size-8" as-child>
                        <Link :href="home.url()">
                            <House />
                            <span class="sr-only">View site</span>
                        </Link>
                    </Button>
                </TooltipTrigger>
                <TooltipContent side="bottom">View site</TooltipContent>
            </Tooltip>
        </div>
    </header>
</template>
