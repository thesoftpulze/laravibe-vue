<script setup lang="ts">
import { Form, router, useHttp } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

import InputError from '@/components/InputError.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    confirm,
    disable,
    enable,
    qrCode,
    recoveryCodes as recoveryCodesRoute,
    regenerateRecoveryCodes,
    secretKey as secretKeyRoute,
} from '@/routes/two-factor'
import type { TwoFactorStatus } from '@/types/data'
import { ShieldCheck } from '@lucide/vue'

const props = defineProps<{
    twoFactor: TwoFactorStatus
}>()

const qrCodeSvg = ref<string | null>(null)
const secretKey = ref<string | null>(null)
const recoveryCodes = ref<string[]>([])

const qrCodeRequest = useHttp<Record<string, never>, { svg: string; url: string }>()
const secretKeyRequest = useHttp<Record<string, never>, { secretKey: string }>()
const recoveryCodesRequest = useHttp<Record<string, never>, string[]>()

const loadSetupDetails = async () => {
    const [qrCodeResponse, secretKeyResponse] = await Promise.all([qrCodeRequest.get(qrCode.url()), secretKeyRequest.get(secretKeyRoute.url())])

    qrCodeSvg.value = qrCodeResponse.svg
    secretKey.value = secretKeyResponse.secretKey
}

const loadRecoveryCodes = async () => {
    recoveryCodes.value = await recoveryCodesRequest.get(recoveryCodesRoute.url())
}

const clearSetupDetails = () => {
    qrCodeSvg.value = null
    secretKey.value = null
}

const enableTwoFactor = () => {
    router.post(enable.url(), {}, { preserveScroll: true })
}

const disableTwoFactor = () => {
    router.delete(disable.url(), {
        preserveScroll: true,
        onSuccess: () => {
            clearSetupDetails()
            recoveryCodes.value = []
        },
    })
}

const regenerateCodes = () => {
    router.post(regenerateRecoveryCodes.url(), {}, { preserveScroll: true, onSuccess: () => void loadRecoveryCodes() })
}

const onConfirmed = () => {
    clearSetupDetails()
    void loadRecoveryCodes()
}

watch(
    () => props.twoFactor.pendingConfirmation,
    (pendingConfirmation) => {
        if (pendingConfirmation) {
            void loadSetupDetails()
        }
    },
    { immediate: true },
)
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
                <ShieldCheck class="size-4 text-primary" />
                Two-Factor Authentication
                <Badge v-if="twoFactor.enabled" variant="secondary">Enabled</Badge>
            </CardTitle>
            <CardDescription class="text-sm text-muted-foreground">
                Add an extra layer of security by requiring a code from your authenticator app when you log in.
            </CardDescription>
        </CardHeader>

        <CardContent class="space-y-4">
            <template v-if="twoFactor.pendingConfirmation">
                <p class="text-sm text-muted-foreground">
                    Scan this QR code with your authenticator app, or enter the setup key manually. Then enter the 6-digit code it shows.
                </p>

                <div v-if="qrCodeSvg" class="inline-block rounded-md bg-white p-3" v-html="qrCodeSvg" />

                <div v-if="secretKey" class="text-sm">
                    <span class="text-muted-foreground">Setup key:</span>
                    <code class="ml-1 rounded bg-muted px-1.5 py-0.5 font-mono">{{ secretKey }}</code>
                </div>

                <Form
                    method="post"
                    :action="confirm.url()"
                    error-bag="confirmTwoFactorAuthentication"
                    :options="{ preserveScroll: true }"
                    reset-on-error
                    @success="onConfirmed"
                    v-slot="{ errors, processing }"
                    class="space-y-2"
                >
                    <Label for="two_factor_code" class="text-sm font-medium">Authentication code</Label>
                    <div class="flex gap-2">
                        <Input
                            id="two_factor_code"
                            name="code"
                            type="text"
                            class="h-9 max-w-40"
                            inputmode="numeric"
                            maxlength="6"
                            autocomplete="one-time-code"
                            placeholder="123456"
                        />
                        <Button type="submit" size="sm" class="h-9" :disabled="processing">Confirm</Button>
                    </div>
                    <InputError class="text-xs" :message="errors.code" />
                </Form>
            </template>

            <template v-else-if="twoFactor.enabled && recoveryCodes.length > 0">
                <p class="text-sm text-muted-foreground">
                    Store these recovery codes somewhere safe. Each one can be used once to log in if you lose access to your authenticator app.
                </p>
                <div class="grid gap-1 rounded-md bg-muted p-3 font-mono text-sm sm:grid-cols-2">
                    <span v-for="code in recoveryCodes" :key="code">{{ code }}</span>
                </div>
            </template>

            <p v-else-if="!twoFactor.enabled" class="text-sm text-muted-foreground">Two-factor authentication is not enabled.</p>
        </CardContent>

        <CardFooter class="gap-2">
            <Button v-if="!twoFactor.enabled && !twoFactor.pendingConfirmation" size="sm" @click="enableTwoFactor">Enable</Button>

            <Button v-if="twoFactor.pendingConfirmation" variant="outline" size="sm" @click="disableTwoFactor">Cancel</Button>

            <template v-if="twoFactor.enabled">
                <Button
                    v-if="recoveryCodes.length === 0"
                    variant="outline"
                    size="sm"
                    :disabled="recoveryCodesRequest.processing"
                    @click="loadRecoveryCodes"
                >
                    Show Recovery Codes
                </Button>
                <Button v-else variant="outline" size="sm" @click="regenerateCodes">Regenerate Recovery Codes</Button>
                <Button variant="destructive" size="sm" @click="disableTwoFactor">Disable</Button>
            </template>
        </CardFooter>
    </Card>
</template>
