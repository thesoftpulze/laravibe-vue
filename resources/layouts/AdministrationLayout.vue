<script setup lang="ts">
import CommandPalette from '@/components/administration/CommandPalette.vue'
import Header from '@/components/administration/Header.vue'
import Sidebar from '@/components/administration/Sidebar.vue'
import { SidebarLayout as Layout } from '@/components/layout'
import { usePageMeta } from '@/composables/usePageMeta'
import { Head, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const meta = usePageMeta()

const isCommandPaletteOpen = ref(false)
</script>

<template>
    <Head :title="meta.title" />

    <Layout :initiallyIsSidebarOpen="usePage().props.sidebarOpen">
        <template #sidebar>
            <Sidebar />
        </template>

        <template #header>
            <Header @search="isCommandPaletteOpen = true" />
        </template>

        <slot />

        <CommandPalette v-model:open="isCommandPaletteOpen" />
    </Layout>
</template>
