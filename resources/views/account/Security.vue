<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import SettingsLayout from '@/layouts/AccountLayout.vue'
import { Form } from '@inertiajs/vue3'
import { ref } from 'vue'

import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import CardFooter from '@/components/ui/card/CardFooter.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/composables/useAuth'
import { password } from '@/routes/account/security'
import type { BrowserSession, TwoFactorStatus } from '@/types/data'
import type { Passkey } from '@/types/models'
import { Info, LockKeyhole } from '@lucide/vue'

import BrowserSessions from './partial/BrowserSessions.vue'
import Passkeys from './partial/Passkeys.vue'
import TwoFactorAuthentication from './partial/TwoFactorAuthentication.vue'

defineProps<{
    twoFactor: TwoFactorStatus
    passkeys: Passkey[]
    sessions: BrowserSession[]
}>()

const passwordInput = ref<HTMLInputElement | null>(null)
const currentPasswordInput = ref<HTMLInputElement | null>(null)

const { user } = useAuth()
</script>

<template>
    <SettingsLayout>
        <div class="space-y-6">
            <Form
                method="put"
                :action="password.url()"
                :options="{
                    preserveScroll: true,
                }"
                reset-on-success
                :reset-on-error="['password', 'password_confirmation', 'current_password']"
                v-slot="{ errors, processing }"
            >
                <input v-if="user" hidden type="text" autocomplete="username" :value="user.email" />

                <Card>
                    <CardHeader class="pb-4">
                        <CardTitle class="flex items-center gap-2 text-lg font-medium">
                            <LockKeyhole class="size-4 text-primary" />
                            Update Password
                        </CardTitle>
                        <CardDescription class="text-sm text-muted-foreground">
                            Ensure your account is using a long, random password to stay secure.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="current_password" class="text-sm font-medium">Current Password</Label>
                            <Input
                                id="current_password"
                                ref="currentPasswordInput"
                                name="current_password"
                                type="password"
                                class="h-9"
                                autocomplete="current-password"
                                placeholder="Enter your current password"
                            />
                            <InputError class="text-xs" :message="errors.current_password" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="password" class="text-sm font-medium">New Password</Label>
                                <Input
                                    id="password"
                                    ref="passwordInput"
                                    name="password"
                                    type="password"
                                    class="h-9"
                                    autocomplete="new-password"
                                    placeholder="Enter new password"
                                />
                                <InputError class="text-xs" :message="errors.password" />
                            </div>
                            <div class="space-y-2">
                                <Label for="password_confirmation" class="text-sm font-medium">Confirm Password</Label>
                                <Input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="h-9"
                                    autocomplete="new-password"
                                    placeholder="Confirm new password"
                                />
                                <InputError class="text-xs" :message="errors.password_confirmation" />
                            </div>
                        </div>

                        <div class="rounded-md bg-blue-50 p-3 dark:bg-blue-950/50">
                            <div class="flex">
                                <div class="shrink-0">
                                    <Info class="size-4 text-blue-500" />
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Password Security Tips</h3>
                                    <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                        <ul class="list-inside list-disc space-y-1">
                                            <li>Use at least 12 characters</li>
                                            <li>Include uppercase and lowercase letters</li>
                                            <li>Add numbers and special characters</li>
                                            <li>Avoid personal information</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="gap-2">
                        <Button :disabled="processing" size="sm">
                            {{ 'Update Password' }}
                        </Button>
                    </CardFooter>
                </Card>
            </Form>

            <TwoFactorAuthentication :two-factor="twoFactor" />

            <Passkeys :passkeys="passkeys" />

            <BrowserSessions :sessions="sessions" />
        </div>
    </SettingsLayout>
</template>
