<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import SettingsLayout from '@/layouts/AdministrationSettingsLayout.vue'
import administrationSettings from '@/routes/administration/settings'
import type { GeneralSettings } from '@/types/data'
import { Form } from '@inertiajs/vue3'

interface Props {
    settings: GeneralSettings
    defaultSiteName: string
}

defineProps<Props>()
</script>

<template>
    <SettingsLayout>
        <Form
            method="put"
            :action="administrationSettings.general.update.url()"
            :options="{ preserveScroll: true }"
            set-defaults-on-success
            v-slot="{ errors, processing, isDirty }"
        >
            <Card>
                <CardHeader>
                    <CardTitle>General</CardTitle>
                    <CardDescription>How the application presents itself. Leave a field empty to use its default.</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="siteName">Site name</Label>
                        <Input
                            id="siteName"
                            name="siteName"
                            :default-value="settings.siteName ?? ''"
                            :placeholder="defaultSiteName"
                            maxlength="60"
                            aria-describedby="siteName-description"
                        />
                        <p id="siteName-description" class="text-sm text-muted-foreground">
                            Shown in the header, browser tabs and emails. Empty uses “{{ defaultSiteName }}” from
                            <code class="text-xs">APP_NAME</code>.
                        </p>
                        <InputError :message="errors.siteName" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="tagline">Tagline</Label>
                        <Input
                            id="tagline"
                            name="tagline"
                            :default-value="settings.tagline ?? ''"
                            placeholder="What your application does, in a sentence"
                            maxlength="160"
                            aria-describedby="tagline-description"
                        />
                        <p id="tagline-description" class="text-sm text-muted-foreground">The description search engines and link previews show.</p>
                        <InputError :message="errors.tagline" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="supportEmail">Support email</Label>
                        <Input
                            id="supportEmail"
                            name="supportEmail"
                            type="email"
                            :default-value="settings.supportEmail ?? ''"
                            placeholder="support@example.com"
                            aria-describedby="supportEmail-description"
                        />
                        <p id="supportEmail-description" class="text-sm text-muted-foreground">Shown on error pages so people can reach you.</p>
                        <InputError :message="errors.supportEmail" />
                    </div>
                </CardContent>
                <CardFooter class="justify-end">
                    <Button type="submit" size="sm" :disabled="processing || !isDirty">Save changes</Button>
                </CardFooter>
            </Card>
        </Form>
    </SettingsLayout>
</template>
