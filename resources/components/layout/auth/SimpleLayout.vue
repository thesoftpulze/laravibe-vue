<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue'
import { home } from '@/routes'
import { Link } from '@inertiajs/vue3'

defineProps<{
    heading?: string
    subheading?: string
}>()
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="home.url()" class="flex flex-col items-center gap-2 font-medium">
                        <AppLogo class="mb-1 text-2xl" />
                        <span v-if="heading" class="sr-only">{{ heading }}</span>
                    </Link>
                    <div v-if="heading || subheading" class="space-y-2 text-center">
                        <h1 v-if="heading" class="text-xl font-medium">{{ heading }}</h1>
                        <p v-if="subheading" class="text-center text-sm text-muted-foreground">{{ subheading }}</p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
