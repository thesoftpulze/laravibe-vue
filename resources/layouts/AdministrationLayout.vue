<script setup lang="ts">
import Header from '@/components/administration/Header.vue'
import Sidebar from '@/components/administration/Sidebar.vue'
import { SidebarLayout as Layout } from '@/components/layout'
import { ToastProvider } from '@/components/toast'
import { usePageMeta } from '@/composables/usePageMeta'
import type { Breadcrumb } from '@/types/data'
import { Head, usePage } from '@inertiajs/vue3'

interface Props {
    breadcrumbs?: Breadcrumb[]
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
})

const meta = usePageMeta()
</script>

<template>
    <Head :title="meta.title" />

    <Layout :breadcrumbs="breadcrumbs" :initiallyIsSidebarOpen="usePage().props.sidebarOpen">
        <template #sidebar>
            <Sidebar />
        </template>

        <template #header>
            <Header :breadcrumbs="breadcrumbs" />
        </template>

        <slot />

        <ToastProvider />
    </Layout>
</template>
