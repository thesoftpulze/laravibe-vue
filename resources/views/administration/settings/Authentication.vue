<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Checkbox } from '@/components/ui/checkbox'
import { Label } from '@/components/ui/label'
import SettingsLayout from '@/layouts/AdministrationSettingsLayout.vue'
import administrationSettings from '@/routes/administration/settings'
import type { AuthenticationSettings } from '@/types/data'
import { useForm } from '@inertiajs/vue3'

interface Props {
    settings: AuthenticationSettings
}

const props = defineProps<Props>()

// A bound form, because a plain <Form> reads a checkbox's hidden input before it changes and gets `isDirty` backwards.
const form = useForm({ ...props.settings })

const submit = () => form.put(administrationSettings.authentication.update.url(), { preserveScroll: true, onSuccess: () => form.defaults() })
</script>

<template>
    <SettingsLayout>
        <form @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Authentication</CardTitle>
                    <CardDescription>Who can sign up, and what they need before using the application.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="flex items-start gap-3">
                        <Checkbox
                            id="allowRegistration"
                            :model-value="form.allowRegistration"
                            aria-describedby="allowRegistration-description"
                            @update:model-value="(checked) => (form.allowRegistration = checked === true)"
                            class="mt-0.5"
                        />
                        <div class="grid gap-1.5">
                            <Label for="allowRegistration">Allow new sign-ups</Label>
                            <p id="allowRegistration-description" class="text-sm text-muted-foreground">
                                When off, the sign-up page and its links disappear. Existing users can still log in.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <Checkbox
                            id="requireEmailVerification"
                            :model-value="form.requireEmailVerification"
                            aria-describedby="requireEmailVerification-description"
                            @update:model-value="(checked) => (form.requireEmailVerification = checked === true)"
                            class="mt-0.5"
                        />
                        <div class="grid gap-1.5">
                            <Label for="requireEmailVerification">Require a verified email address</Label>
                            <p id="requireEmailVerification-description" class="text-sm text-muted-foreground">
                                Pages behind the <code class="text-xs">verified</code> middleware, such as the administration, ask people to verify
                                first. When off, they get straight in, and verification emails are still sent.
                            </p>
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="justify-end">
                    <Button type="submit" size="sm" :disabled="form.processing || !form.isDirty">Save changes</Button>
                </CardFooter>
            </Card>
        </form>
    </SettingsLayout>
</template>
