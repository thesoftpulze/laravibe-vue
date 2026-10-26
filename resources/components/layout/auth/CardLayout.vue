<script setup lang="ts">
import Logo from '@/components/Logo.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { home } from '@/routes'
import { Link } from '@inertiajs/vue3'

defineProps<{
    heading?: string
    subheading?: string
}>()
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">
            <Link :href="home.url()" class="flex items-center gap-2 self-center font-medium">
                <div class="flex h-10 items-center justify-center">
                    <Logo class="h-full" />
                </div>
            </Link>

            <div class="flex flex-col gap-6">
                <Card class="rounded-xl">
                    <CardHeader v-if="heading || subheading" class="px-10 pt-8 pb-0 text-center">
                        <CardTitle v-if="heading" class="text-xl">{{ heading }}</CardTitle>
                        <CardDescription v-if="subheading">
                            {{ subheading }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-10 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
