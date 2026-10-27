<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { useAuth } from '@/composables/useAuth'
import SettingsLayout from '@/layouts/AccountLayout.vue'
import account from '@/routes/account'
import { Link } from '@inertiajs/vue3'
import { ChevronRight, Fingerprint, LayoutGrid, Mail, ShieldCheck } from '@lucide/vue'
import { computed } from 'vue'

interface Props {
    twoFactorEnabled: boolean
    passkeyCount: number
}

const props = defineProps<Props>()

const { requireUser } = useAuth()
const user = requireUser()

const statusItems = computed(() => [
    {
        title: 'Email address',
        description: user.email,
        icon: Mail,
        status: user.email_verified_at ? 'Verified' : 'Unverified',
        isOk: !!user.email_verified_at,
        href: account.profile.url(),
    },
    {
        title: 'Two-factor authentication',
        description: 'A code from your authenticator app when you log in.',
        icon: ShieldCheck,
        status: props.twoFactorEnabled ? 'Enabled' : 'Disabled',
        isOk: props.twoFactorEnabled,
        href: account.security.url(),
    },
    {
        title: 'Passkeys',
        description: 'Log in with your fingerprint, face or device PIN.',
        icon: Fingerprint,
        status: props.passkeyCount === 1 ? '1 passkey' : `${props.passkeyCount} passkeys`,
        isOk: props.passkeyCount > 0,
        href: account.security.url(),
    },
])
</script>

<template>
    <SettingsLayout>
        <div class="space-y-6">
            <Card>
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-2 text-lg font-medium">
                        <LayoutGrid class="size-4 text-primary" />
                        Welcome back, {{ user.name }}
                    </CardTitle>
                    <CardDescription class="text-sm text-muted-foreground"> A quick look at the state of your account. </CardDescription>
                </CardHeader>
                <CardContent class="divide-y p-0">
                    <Link
                        v-for="item in statusItems"
                        :key="item.title"
                        :href="item.href"
                        class="flex items-center gap-4 px-6 py-4 transition-colors hover:bg-accent/50"
                    >
                        <component :is="item.icon" class="size-5 shrink-0 text-muted-foreground" />
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium">{{ item.title }}</p>
                            <p class="truncate text-sm text-muted-foreground">{{ item.description }}</p>
                        </div>
                        <Badge :variant="item.isOk ? 'secondary' : 'outline'">{{ item.status }}</Badge>
                        <ChevronRight class="size-4 shrink-0 text-muted-foreground" />
                    </Link>
                </CardContent>
            </Card>
        </div>
    </SettingsLayout>
</template>
