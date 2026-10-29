<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { register } from '@/routes'
import { store } from '@/routes/login'
import { login as passkeyLogin, loginOptions as passkeyLoginOptions } from '@/routes/passkey'
import { request } from '@/routes/password'
import { Form, router } from '@inertiajs/vue3'
import { usePasskeyVerify } from '@laravel/passkeys/vue'
import { KeyRound, LoaderCircle } from '@lucide/vue'
import { ref } from 'vue'

defineProps<{
    status?: string
    canResetPassword: boolean
}>()

const remember = ref<boolean | 'indeterminate'>(false)

const {
    verify: verifyPasskey,
    error: passkeyError,
    isSupported: passkeySupported,
} = usePasskeyVerify({
    autofill: true,
    remember: () => remember.value === true,
    routes: {
        options: passkeyLoginOptions.url(),
        submit: passkeyLogin.url(),
    },
    onSuccess: (response) => {
        router.visit(response.redirect ?? '/')
    },
})

/**
 * The composable's `isLoading` stays true while autofill waits in the background,
 * so the button tracks only the sign-ins it starts itself.
 */
const passkeyLoading = ref(false)

async function signInWithPasskey(): Promise<void> {
    passkeyLoading.value = true

    try {
        await verifyPasskey()
    } finally {
        passkeyLoading.value = false
    }
}
</script>

<template>
    <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
        {{ status }}
    </div>

    <Form method="post" :action="store.url()" :reset-on-success="['password']" v-slot="{ errors, processing }" class="flex flex-col gap-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email webauthn"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Password</Label>
                    <TextLink v-if="canResetPassword" :href="request.url()" class="text-sm" :tabindex="5"> Forgot password? </TextLink>
                </div>
                <Input id="password" type="password" name="password" required :tabindex="2" autocomplete="current-password" placeholder="Password" />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" v-model="remember" name="remember" :tabindex="3" />
                    <span>Remember me</span>
                </Label>
            </div>

            <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="processing">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Log in
            </Button>

            <template v-if="passkeySupported">
                <div class="relative text-center text-xs text-muted-foreground uppercase">
                    <span class="bg-background px-2">or</span>
                </div>

                <Button type="button" variant="outline" class="w-full" :disabled="passkeyLoading" @click="signInWithPasskey">
                    <LoaderCircle v-if="passkeyLoading" class="h-4 w-4 animate-spin" />
                    <KeyRound v-else class="h-4 w-4" />
                    Sign in with a passkey
                </Button>
                <InputError :message="passkeyError ?? undefined" />
            </template>
        </div>

        <div v-if="$page.props.canRegister" class="text-center text-sm text-muted-foreground">
            Don't have an account?
            <TextLink :href="register.url()" :tabindex="5">Sign up</TextLink>
        </div>
    </Form>
</template>
