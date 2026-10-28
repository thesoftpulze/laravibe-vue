<script setup lang="ts">
import { AuthCardLayout as Layout } from '@/components/layout'
import { usePageMeta } from '@/composables/usePageMeta'
import { Head } from '@inertiajs/vue3'

const meta = usePageMeta()
</script>

<template>
    <Head :title="meta.title" />

    <Layout :heading="meta.heading" :subheading="meta.subheading">
        <slot />
    </Layout>
</template>
