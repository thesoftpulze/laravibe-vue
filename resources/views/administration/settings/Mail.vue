<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuth } from '@/composables/useAuth'
import SettingsLayout from '@/layouts/AdministrationSettingsLayout.vue'
import administrationSettings from '@/routes/administration/settings'
import type { MailSettings } from '@/types/data'
import { Form } from '@inertiajs/vue3'
import { Send } from '@lucide/vue'

interface Props {
    settings: MailSettings
    defaults: MailSettings
    mailer: string
}

defineProps<Props>()

const { requireUser } = useAuth()
const user = requireUser()
</script>

<template>
    <SettingsLayout>
        <div class="space-y-6">
            <Form
                method="put"
                :action="administrationSettings.mail.update.url()"
                :options="{ preserveScroll: true }"
                set-defaults-on-success
                v-slot="{ errors, processing, isDirty }"
            >
                <Card>
                    <CardHeader>
                        <CardTitle>Sender</CardTitle>
                        <CardDescription>
                            Who the application's emails come from. Leave a field empty to use its value from <code class="text-xs">.env</code>.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="fromName">Sender name</Label>
                                <Input
                                    id="fromName"
                                    name="fromName"
                                    :default-value="settings.fromName ?? ''"
                                    :placeholder="defaults.fromName ?? ''"
                                    maxlength="100"
                                />
                                <InputError :message="errors.fromName" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="fromAddress">Sender address</Label>
                                <Input
                                    id="fromAddress"
                                    name="fromAddress"
                                    type="email"
                                    :default-value="settings.fromAddress ?? ''"
                                    :placeholder="defaults.fromAddress ?? ''"
                                />
                                <InputError :message="errors.fromAddress" />
                            </div>
                        </div>

                        <p class="text-sm text-muted-foreground">
                            Emails are delivered with the <code class="text-xs">{{ mailer }}</code> mailer. Change how mail is delivered in
                            <code class="text-xs">.env</code>.
                        </p>
                    </CardContent>
                    <CardFooter class="justify-end">
                        <Button type="submit" size="sm" :disabled="processing || !isDirty">Save changes</Button>
                    </CardFooter>
                </Card>
            </Form>

            <Form method="post" :action="administrationSettings.mail.test.store.url()" :options="{ preserveScroll: true }" v-slot="{ processing }">
                <Card>
                    <CardHeader>
                        <CardTitle>Test email</CardTitle>
                        <CardDescription>Send an email to {{ user.email }} to check that delivery works with the saved settings.</CardDescription>
                    </CardHeader>
                    <CardFooter class="justify-end">
                        <Button type="submit" size="sm" variant="outline" :disabled="processing">
                            <Send />
                            Send test email
                        </Button>
                    </CardFooter>
                </Card>
            </Form>
        </div>
    </SettingsLayout>
</template>
