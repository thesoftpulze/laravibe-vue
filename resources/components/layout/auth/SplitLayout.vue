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
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col overflow-hidden bg-muted p-10 lg:flex dark:border-r">
            <div class="absolute inset-0 bg-radial-[at_0%_100%] from-primary/20 to-transparent to-60%" />
            <Link :href="home.url()" class="relative z-20 flex items-center">
                <AppLogo class="text-xl" />
            </Link>
        </div>
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-87.5">
                <div v-if="heading || subheading" class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="heading">{{ heading }}</h1>
                    <p class="text-sm text-muted-foreground" v-if="subheading">{{ subheading }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
