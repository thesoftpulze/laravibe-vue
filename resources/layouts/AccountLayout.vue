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
import { LayoutGrid, LockKeyhole, SwatchBook, User } from '@lucide/vue'
import { computed, ref } from 'vue'

interface NavGroup {
    label?: string
    items: NavItem[]
}

const page = usePage()
const currentPath = computed(() => new URL(page.url, 'http://localhost').pathname)

const isCurrent = (href: string) => () => currentPath.value === href

// Add a group here for each area of the product a signed-in user works in.
const navGroups = ref<NavGroup[]>([
    {
        items: [{ title: 'Overview', href: accountRoute.url(), icon: LayoutGrid, isActive: isCurrent(accountRoute.url()) }],
    },
    {
        label: 'Settings',
        items: [
            { title: 'Profile', href: account.profile.url(), icon: User, isActive: isCurrent(account.profile.url()) },
            { title: 'Security', href: account.security.url(), icon: LockKeyhole, isActive: isCurrent(account.security.url()) },
            { title: 'Appearance', href: account.appearance.url(), icon: SwatchBook, isActive: isCurrent(account.appearance.url()) },
        ],
    },
])
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Account" classNames="px-4" />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full md:max-w-3xl lg:w-56">
                <Card>
                    <CardContent class="space-y-4 px-4">
                        <nav
                            v-for="(group, index) in navGroups"
                            :key="group.label ?? index"
                            :aria-label="group.label ?? 'Overview'"
                            class="flex flex-col space-y-1"
                        >
                            <p v-if="group.label" class="px-3 pb-1 text-xs font-medium text-muted-foreground">{{ group.label }}</p>
                            <Button
                                v-for="item in group.items"
                                :key="item.href"
                                variant="ghost"
                                :class="['w-full justify-start', { 'bg-accent/50': isCallable(item.isActive) && item.isActive() }]"
                                asChild
                            >
                                <Link :href="item.href">
                                    <component v-if="item.icon" :is="item.icon" class="size-4" />
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
