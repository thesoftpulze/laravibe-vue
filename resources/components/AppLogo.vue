<script setup lang="ts">
import { cn } from '@/lib/utils'
import type { AppPageProps } from '@/types'
import { usePage } from '@inertiajs/vue3'
import { computed, type HTMLAttributes } from 'vue'

interface Props {
    class?: HTMLAttributes['class']
    iconOnly?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    iconOnly: false,
})

const page = usePage<AppPageProps>()

/**
 * The default "Laravibe" name gets its two-tone brand treatment; any other app name renders as plain text.
 */
const isDefaultName = computed(() => page.props.name === 'Laravibe')
</script>

<template>
    <span :class="cn('inline-flex items-center gap-[0.5em] leading-none', props.class)">
        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="size-[1.6em] shrink-0" aria-hidden="true">
            <rect x="1" y="1" width="62" height="62" rx="16" class="fill-logo stroke-logo-border" stroke-width="2" />
            <path
                d="M18.5 16v26a6 6 0 0 0 6 6h3c4 0 5-8 9-8s5 8 9 8"
                class="stroke-primary"
                stroke-width="5.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>
        <span :class="cn('font-display font-semibold tracking-tight whitespace-nowrap', iconOnly && 'sr-only')">
            <template v-if="isDefaultName">Lara<span class="text-primary">vibe</span></template>
            <template v-else>{{ page.props.name }}</template>
        </span>
    </span>
</template>
