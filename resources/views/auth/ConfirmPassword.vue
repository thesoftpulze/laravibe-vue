<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { confirm as passkeyConfirm, confirmOptions as passkeyConfirmOptions } from '@/routes/passkey'
import { store } from '@/routes/password/confirm'
import { Form, router } from '@inertiajs/vue3'
import { usePasskeyVerify } from '@laravel/passkeys/vue'
import { KeyRound, LoaderCircle } from '@lucide/vue'

defineProps<{
    hasPasskeys: boolean
}>()

const {
    verify: confirmWithPasskey,
    isLoading: passkeyLoading,
    error: passkeyError,
    isSupported: passkeySupported,
} = usePasskeyVerify({
    routes: {
        options: passkeyConfirmOptions.url(),
        submit: passkeyConfirm.url(),
    },
    onSuccess: (response) => {
        router.visit(response.redirect ?? '/')
    },
})
</script>

<template>
    <Form method="post" :action="store.url()" reset-on-success v-slot="{ errors, processing }">
        <div class="space-y-6">
            <div class="grid gap-2">
                <Label htmlFor="password">Password</Label>
                <Input id="password" type="password" name="password" class="mt-1 block w-full" required autofocus />

                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Button class="w-full" :disabled="processing">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    Confirm Password
                </Button>
            </div>

            <template v-if="hasPasskeys && passkeySupported">
                <Button type="button" variant="outline" class="w-full" :disabled="passkeyLoading" @click="confirmWithPasskey">
                    <LoaderCircle v-if="passkeyLoading" class="h-4 w-4 animate-spin" />
                    <KeyRound v-else class="h-4 w-4" />
                    Confirm with a passkey
                </Button>
                <InputError :message="passkeyError ?? undefined" />
            </template>
        </div>
    </Form>
</template>
