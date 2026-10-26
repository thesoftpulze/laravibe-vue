<script setup lang="ts">
import { Form } from '@inertiajs/vue3'
import { ref } from 'vue'

import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/composables/useAuth'
import { Nullable } from '@/types'
import account from '@/routes/account'
import { Trash2, TriangleAlert } from '@lucide/vue'

const confirmingUserDeletion = ref(false)
const passwordInput = ref<Nullable<HTMLInputElement>>(null)

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true
}

const closeModal = () => {
    confirmingUserDeletion.value = false
}

const { user } = useAuth()
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
                <Trash2 class="size-4 text-destructive" />
                Delete Account
            </CardTitle>
            <CardDescription class="text-sm text-muted-foreground">
                Permanently delete your account and all associated data. This action cannot be undone.
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
            <!-- Warning Section -->
            <div class="rounded-md border border-amber-200 bg-amber-50/50 p-3 dark:border-amber-700/50 dark:bg-amber-950/20">
                <div class="flex">
                    <div class="shrink-0">
                        <TriangleAlert class="size-4 text-amber-500" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">Warning: This action is irreversible</h3>
                        <div class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                            <ul class="list-inside list-disc space-y-1">
                                <li>All your personal data will be permanently deleted</li>
                                <li>Your profile and content will be removed</li>
                                <li>You will lose access to all services immediately</li>
                                <li>This action cannot be undone</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
        <CardFooter>
            <Dialog v-model:open="confirmingUserDeletion">
                <DialogTrigger as-child>
                    <Button variant="destructive" @click="confirmUserDeletion" size="sm"> Delete Account </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle> Are you absolutely sure? </DialogTitle>
                        <DialogDescription class="text-muted-foreground">
                            This will permanently delete your account and remove all your data from our servers. This action cannot be undone.
                        </DialogDescription>
                    </DialogHeader>

                    <Form
                        method="delete"
                        :action="account.destroy.url()"
                        :options="{ preserveScroll: true }"
                        @success="closeModal"
                        v-slot="{ errors, processing }"
                    >
                        <input v-if="user" hidden type="text" autocomplete="username" :value="user.email" />

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="password" class="text-sm font-medium"> Confirm your password to continue </Label>
                                <Input
                                    id="password"
                                    ref="passwordInput"
                                    name="password"
                                    type="password"
                                    class="h-9"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                />
                                <InputError class="text-xs" :message="errors.password" />
                            </div>

                            <DialogFooter class="mt-6">
                                <Button type="button" variant="outline" @click="closeModal" class="mr-2"> Cancel </Button>
                                <Button type="submit" variant="destructive" :disabled="processing">
                                    {{ 'Delete Account' }}
                                </Button>
                            </DialogFooter>
                        </div>
                    </Form>
                </DialogContent>
            </Dialog>
        </CardFooter>
    </Card>
</template>
