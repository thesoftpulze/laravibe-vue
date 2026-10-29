<script setup lang="ts">
import Heading from '@/components/Heading.vue'
import UserInfo from '@/components/UserInfo.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardAction, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'
import { formatDate, formatRelativeDate } from '@/lib/helpers'
import administrationUsers from '@/routes/administration/users'
import type { UserStatistics } from '@/types/data'
import type { AdministrationUser } from '@/types/models'
import { Link } from '@inertiajs/vue3'
import { ArrowRight, MailWarning, ShieldCheck, UserPlus, Users } from '@lucide/vue'
import { computed } from 'vue'

interface Props {
    statistics?: UserStatistics
    recentUsers?: AdministrationUser[]
}

const props = defineProps<Props>()

const statisticCards = computed(() => [
    { label: 'Total users', value: props.statistics?.total, icon: Users, href: administrationUsers.index.url() },
    {
        label: 'Administrators',
        value: props.statistics?.administrators,
        icon: ShieldCheck,
        href: administrationUsers.index.url({ query: { role: 'admin' } }),
    },
    {
        label: 'Unverified emails',
        value: props.statistics?.unverified,
        icon: MailWarning,
        href: administrationUsers.index.url({ query: { verification: 'unverified' } }),
    },
    { label: 'Joined in the last 7 days', value: props.statistics?.joinedThisWeek, icon: UserPlus, href: administrationUsers.index.url() },
])
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <Heading title="Dashboard" description="An overview of the people using your application." class-names="mb-0" />

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Link
                v-for="card in statisticCards"
                :key="card.label"
                :href="card.href"
                class="group rounded-xl focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
            >
                <Card class="h-full gap-2 transition-colors group-hover:border-primary/40">
                    <CardHeader>
                        <CardDescription>{{ card.label }}</CardDescription>
                        <CardAction>
                            <component :is="card.icon" class="size-4 text-muted-foreground" />
                        </CardAction>
                    </CardHeader>
                    <CardContent>
                        <Skeleton v-if="card.value === undefined" class="h-8 w-16" />
                        <p v-else class="text-3xl font-semibold tabular-nums">{{ card.value.toLocaleString() }}</p>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Recent sign-ups</CardTitle>
                <CardDescription>The newest accounts.</CardDescription>
                <CardAction>
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="administrationUsers.index.url()">
                            View all
                            <ArrowRight />
                        </Link>
                    </Button>
                </CardAction>
            </CardHeader>
            <CardContent>
                <ul v-if="recentUsers === undefined" class="divide-y" aria-busy="true">
                    <li v-for="index in 5" :key="index" class="flex items-center gap-3 py-3">
                        <Skeleton class="size-8 rounded-lg" />
                        <div class="flex-1 space-y-1.5">
                            <Skeleton class="h-3.5 w-40" />
                            <Skeleton class="h-3 w-56" />
                        </div>
                    </li>
                </ul>
                <p v-else-if="recentUsers.length === 0" class="py-6 text-center text-sm text-muted-foreground">No one has signed up yet.</p>
                <ul v-else class="divide-y">
                    <li v-for="user in recentUsers" :key="user.id" class="flex items-center gap-2 py-3">
                        <UserInfo :user="user" show-email />
                        <Badge v-if="user.is_admin" class="hidden sm:inline-flex">Admin</Badge>
                        <Badge
                            v-if="!user.email_verified_at"
                            variant="outline"
                            class="hidden border-amber-300 text-amber-700 sm:inline-flex dark:border-amber-700 dark:text-amber-300"
                        >
                            Unverified
                        </Badge>
                        <time
                            :datetime="user.created_at"
                            :title="formatDate(user.created_at, { dateStyle: 'full', timeStyle: 'short' })"
                            class="ml-2 text-xs whitespace-nowrap text-muted-foreground"
                        >
                            {{ formatRelativeDate(user.created_at) }}
                        </time>
                    </li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
