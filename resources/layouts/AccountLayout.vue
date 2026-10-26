<script setup lang="ts">
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import { Separator } from '@/components/ui/separator'
import { isCallable } from '@/lib/helpers'
import { type NavItem } from '@/types'
import { account as accountRoute } from '@/routes'
import account from '@/routes/account'
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const currentPath = computed(() => new URL(page.url, 'http://localhost').pathname)

const sidebarNavItems = ref<NavItem[]>([
    { title: 'General', href: accountRoute.url(), isActive: () => currentPath.value === accountRoute.url() },
    { title: 'Security', href: account.security.url(), isActive: () => currentPath.value === account.security.url() },
    { title: 'Appearance', href: account.appearance.url(), isActive: () => currentPath.value === account.appearance.url() },
])
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Account" classNames="px-4" />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full md:max-w-3xl lg:w-56">
                <Card>
                    <CardContent class="px-4">
                        <nav class="flex flex-col space-y-1 space-x-0">
                            <Button
                                v-for="item in sidebarNavItems"
                                :key="item.href"
                                variant="ghost"
                                :class="['w-full justify-start', { 'bg-accent/50': isCallable(item.isActive) && item.isActive() }]"
                                asChild
                            >
                                <Link :href="item.href">
                                    {{ item.title }}
                                </Link>
                            </Button>
                        </nav>
                    </CardContent>
                </Card>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <section class="flex-1 space-y-12 md:max-w-3xl">
                <slot />
            </section>
        </div>
    </div>
</template>
