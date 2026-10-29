<script setup lang="ts">
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import { isNavItemActive } from '@/lib/helpers'
import administrationSettings from '@/routes/administration/settings'
import type { NavItem } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'
import { SlidersHorizontal } from '@lucide/vue'
import { computed } from 'vue'

const page = usePage()
const currentPath = computed(() => new URL(page.url, 'http://localhost').pathname)

const isCurrent = (href: string) => () => currentPath.value === href

// Add an item here for each settings group with its own page.
const sections: NavItem[] = [
    {
        title: 'General',
        href: administrationSettings.general.edit.url(),
        icon: SlidersHorizontal,
        isActive: isCurrent(administrationSettings.general.edit.url()),
    },
]
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <Heading title="Settings" description="Change how the application looks and behaves for everyone." class-names="mb-0" />

        <div class="flex flex-col gap-6 lg:flex-row lg:gap-10">
            <nav aria-label="Settings" class="flex gap-1 overflow-x-auto lg:w-48 lg:shrink-0 lg:flex-col">
                <Button
                    v-for="item in sections"
                    :key="item.href"
                    variant="ghost"
                    size="sm"
                    :class="['justify-start', { 'bg-accent text-accent-foreground': isNavItemActive(item) }]"
                    as-child
                >
                    <Link :href="item.href" :aria-current="isNavItemActive(item) ? 'page' : undefined">
                        <component v-if="item.icon" :is="item.icon" />
                        {{ item.title }}
                    </Link>
                </Button>
            </nav>

            <section class="min-w-0 flex-1 lg:max-w-2xl">
                <slot />
            </section>
        </div>
    </div>
</template>
