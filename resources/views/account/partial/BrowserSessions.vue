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
import { destroy } from '@/routes/account/security/sessions'
import type { BrowserSession } from '@/types/data'
import { MonitorSmartphone, Monitor, Smartphone } from '@lucide/vue'

defineProps<{
    sessions: BrowserSession[]
}>()

const confirmingLogout = ref(false)

const closeModal = () => {
    confirmingLogout.value = false
}

const { user } = useAuth()
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
                <MonitorSmartphone class="size-4 text-primary" />
                Browser Sessions
            </CardTitle>
            <CardDescription class="text-sm text-muted-foreground">
                Manage and log out your active sessions on other browsers and devices.
            </CardDescription>
        </CardHeader>

        <CardContent>
            <ul v-if="sessions.length > 0" class="space-y-4">
                <li v-for="(session, index) in sessions" :key="index" class="flex items-center gap-3">
                    <Monitor v-if="session.isDesktop" class="size-6 text-muted-foreground" />
                    <Smartphone v-else class="size-6 text-muted-foreground" />

                    <div class="text-sm">
                        <div>{{ session.platform }} - {{ session.browser }}</div>
                        <div class="text-xs text-muted-foreground">
                            {{ session.ipAddress ?? 'Unknown IP' }},
                            <span v-if="session.isCurrentDevice" class="font-semibold text-green-600">This device</span>
                            <span v-else>Last active {{ session.lastActive }}</span>
                        </div>
                    </div>
                </li>
            </ul>

            <p v-else class="text-sm text-muted-foreground">Session details are only available with the database session driver.</p>
        </CardContent>

        <CardFooter>
            <Dialog v-model:open="confirmingLogout">
                <DialogTrigger as-child>
                    <Button variant="outline" size="sm">Log Out Other Browser Sessions</Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Log out other browser sessions</DialogTitle>
                        <DialogDescription class="text-muted-foreground">
                            Enter your password to confirm you want to log out of your other browser sessions on all of your devices.
                        </DialogDescription>
                    </DialogHeader>

                    <Form
                        method="delete"
                        :action="destroy.url()"
                        :options="{ preserveScroll: true }"
                        reset-on-error
                        @success="closeModal"
                        v-slot="{ errors, processing }"
                    >
                        <input v-if="user" hidden type="text" autocomplete="username" :value="user.email" />

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label for="sessions_password" class="text-sm font-medium">Password</Label>
                                <Input
                                    id="sessions_password"
                                    name="password"
                                    type="password"
                                    class="h-9"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                />
                                <InputError class="text-xs" :message="errors.password" />
                            </div>

                            <DialogFooter class="mt-6">
                                <Button type="button" variant="outline" class="mr-2" @click="closeModal">Cancel</Button>
                                <Button type="submit" :disabled="processing">Log Out Other Sessions</Button>
                            </DialogFooter>
                        </div>
                    </Form>
                </DialogContent>
            </Dialog>
        </CardFooter>
    </Card>
</template>
