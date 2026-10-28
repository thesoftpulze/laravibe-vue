<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner'
import { useAppearance } from '@/composables/useAppearance'
import { useMediaQuery } from '@vueuse/core'
import { computed } from 'vue'
import type { ToasterProps } from 'vue-sonner'
import 'vue-sonner/style.css'

/** Change these two to move toasts. */
const desktopPosition: ToasterProps['position'] = 'bottom-right'
const mobilePosition: ToasterProps['position'] = 'top-center'

// Matches vue-sonner's own mobile breakpoint, where toasts go full width.
const isMobile = useMediaQuery('(max-width: 600px)')
const position = computed(() => (isMobile.value ? mobilePosition : desktopPosition))

const { appearance } = useAppearance()

// The type shows as a colored icon on the neutral, brand-styled card.
const toastOptions: ToasterProps['toastOptions'] = {
    classes: {
        toast: 'shadow-lg! font-sans',
        icon: 'self-start mt-0.5',
        closeButton: 'bg-popover! text-muted-foreground! border-border! hover:text-foreground!',
        success: '[&_[data-icon]]:text-emerald-600 dark:[&_[data-icon]]:text-emerald-400',
        info: '[&_[data-icon]]:text-chart-2',
        warning: '[&_[data-icon]]:text-amber-600 dark:[&_[data-icon]]:text-amber-400',
        error: '[&_[data-icon]]:text-destructive',
        loading: '[&_[data-icon]]:text-muted-foreground',
    },
}
</script>

<template>
    <Toaster :position="position" :visibleToasts="5" :theme="appearance" :toastOptions="toastOptions" closeButton />
</template>
