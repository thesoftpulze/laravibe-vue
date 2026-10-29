<script setup lang="ts">
import { LayoutContent, LayoutShell } from './'

interface Props {
    initiallyIsSidebarOpen?: boolean
}

withDefaults(defineProps<Props>(), { initiallyIsSidebarOpen: true })
</script>

<template>
    <LayoutShell variant="sidebar" :initiallyIsSidebarOpen="initiallyIsSidebarOpen">
        <slot name="sidebar" />

        <!-- The content scrolls inside its own frame, so the header can stay at the top. -->
        <LayoutContent variant="sidebar" class="h-svh overflow-x-hidden overflow-y-auto md:h-[calc(100svh-1rem)]" scroll-region>
            <slot name="header" />

            <slot />
        </LayoutContent>
    </LayoutShell>
</template>
