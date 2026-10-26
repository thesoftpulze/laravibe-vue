<script setup lang="ts">
import { usePasskeyRegister } from '@laravel/passkeys/vue'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { destroy, registrationOptions, store } from '@/routes/passkey'
import type { Passkey } from '@/types/models'
import { KeyRound, Trash2 } from '@lucide/vue'

defineProps<{
    passkeys: Passkey[]
}>()

const name = ref('')

const { register, isLoading, error, isSupported } = usePasskeyRegister({
    routes: {
        options: registrationOptions.url(),
        submit: store.url(),
    },
    onSuccess: () => {
        name.value = ''
        router.reload({ only: ['passkeys'] })
    },
})

const addPasskey = () => {
    void register(name.value.trim())
}

const removePasskey = (passkey: Passkey) => {
    router.delete(destroy.url(passkey.id), { preserveScroll: true, only: ['passkeys'] })
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
                <KeyRound class="size-4 text-primary" />
                Passkeys
            </CardTitle>
            <CardDescription class="text-sm text-muted-foreground">
                Sign in without a password using your device's fingerprint, face, screen lock, or a security key.
            </CardDescription>
        </CardHeader>

        <CardContent class="space-y-4">
            <ul v-if="passkeys.length > 0" class="divide-y rounded-md border">
                <li v-for="passkey in passkeys" :key="passkey.id" class="flex items-center justify-between gap-3 p-3">
                    <div class="text-sm">
                        <div class="font-medium">{{ passkey.name }}</div>
                        <div class="text-xs text-muted-foreground">
                            <span v-if="passkey.authenticator">{{ passkey.authenticator }} · </span>
                            Added {{ passkey.created_at }} ·
                            {{ passkey.last_used_at ? `Last used ${passkey.last_used_at}` : 'Never used' }}
                        </div>
                    </div>
                    <Button variant="ghost" size="icon" :aria-label="`Remove ${passkey.name}`" @click="removePasskey(passkey)">
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </li>
            </ul>

            <p v-else class="text-sm text-muted-foreground">You haven't added any passkeys yet.</p>

            <p v-if="!isSupported" class="text-sm text-muted-foreground">This browser doesn't support passkeys.</p>
        </CardContent>

        <CardFooter>
            <form class="w-full space-y-2" @submit.prevent="addPasskey">
                <Label for="passkey_name" class="text-sm font-medium">Passkey name</Label>
                <div class="flex gap-2">
                    <Input id="passkey_name" v-model="name" type="text" class="h-9 max-w-64" placeholder="e.g. MacBook Pro" maxlength="255" />
                    <Button type="submit" size="sm" class="h-9" :disabled="!isSupported || isLoading || name.trim() === ''">Add Passkey</Button>
                </div>
                <InputError class="text-xs" :message="error ?? undefined" />
            </form>
        </CardFooter>
    </Card>
</template>
