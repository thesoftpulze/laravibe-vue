<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import CardFooter from '@/components/ui/card/CardFooter.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/composables/useAuth'
import SettingsLayout from '@/layouts/AccountLayout.vue'
import account from '@/routes/account'
import { send } from '@/routes/verification'
import { Info, TriangleAlert, User } from '@lucide/vue'
import DeleteUser from './partial/DeleteUser.vue'

interface Props {
    mustVerifyEmail: boolean
}

defineProps<Props>()

const { requireUser } = useAuth()
const user = requireUser()
</script>

<template>
    <SettingsLayout>
        <div class="space-y-6">
            <Form method="patch" :action="account.update.url()" class="space-y-4" v-slot="{ errors, processing }">
                <Card>
                    <CardHeader class="pb-4">
                        <CardTitle class="flex items-center gap-2 text-lg font-medium">
                            <User class="size-4 text-primary" />
                            Profile
                        </CardTitle>
                        <CardDescription class="text-sm text-muted-foreground"> Your name and email address. </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name" class="text-sm font-medium">Full Name</Label>
                                <Input
                                    id="name"
                                    class="h-9"
                                    name="name"
                                    :default-value="user.name"
                                    required
                                    autocomplete="name"
                                    placeholder="Enter your full name"
                                />
                                <InputError class="text-xs" :message="errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email" class="text-sm font-medium">Email Address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    class="h-9"
                                    name="email"
                                    :default-value="user.email"
                                    required
                                    autocomplete="username"
                                    placeholder="Enter your email address"
                                />
                                <InputError class="text-xs" :message="errors.email" />
                            </div>
                        </div>

                        <!-- Email Verification Notice -->
                        <div
                            v-if="mustVerifyEmail && !user.email_verified_at"
                            class="rounded-md border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-950/50"
                        >
                            <div class="flex">
                                <div class="shrink-0">
                                    <TriangleAlert class="size-4 text-amber-500" />
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">Email Verification Required</h3>
                                    <div class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                                        <p>
                                            Your email address is unverified.
                                            <Link
                                                :href="send.url()"
                                                method="post"
                                                as="button"
                                                class="font-medium underline decoration-amber-400 underline-offset-4 transition-colors duration-200 hover:decoration-amber-600 dark:decoration-amber-300 dark:hover:decoration-amber-100"
                                            >
                                                Click here to resend the verification email.
                                            </Link>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Tips -->
                        <div class="rounded-md bg-blue-50 p-3 dark:bg-blue-950/50">
                            <div class="flex">
                                <div class="shrink-0">
                                    <Info class="size-4 text-blue-500" />
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Profile Tips</h3>
                                    <div class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                        <ul class="list-inside list-disc space-y-1">
                                            <li>Use your real name for better recognition</li>
                                            <li>Keep your email address up to date</li>
                                            <li>Verify your email for account security</li>
                                            <li>Changes will be reflected across your profile</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Button :disabled="processing" size="sm">
                            {{ 'Save Changes' }}
                        </Button>
                    </CardFooter>
                </Card>
            </Form>

            <DeleteUser />
        </div>
    </SettingsLayout>
</template>
