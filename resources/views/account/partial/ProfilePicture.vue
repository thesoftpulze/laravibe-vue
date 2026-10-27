<script setup lang="ts">
import { Form, router } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, ref } from 'vue'

import InputError from '@/components/InputError.vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { useAuth } from '@/composables/useAuth'
import { getInitials } from '@/composables/useInitials'
import account from '@/routes/account'
import { Nullable } from '@/types'
import { Camera, Trash2 } from '@lucide/vue'

const { requireUser } = useAuth()
const user = computed(() => requireUser())

const fileInput = ref<Nullable<HTMLInputElement>>(null)
const previewUrl = ref<Nullable<string>>(null)

const clearPreview = () => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value)
    }

    previewUrl.value = null
}

const selectFile = () => {
    fileInput.value?.click()
}

const onFileSelected = (event: Event, submit: () => void) => {
    const file = (event.target as HTMLInputElement).files?.[0]

    if (!file) {
        return
    }

    clearPreview()
    previewUrl.value = URL.createObjectURL(file)
    submit()
}

const resetFileInput = () => {
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

const onError = () => {
    clearPreview()
}

const removeAvatar = () => {
    router.delete(account.avatar.destroy.url(), { preserveScroll: true, onSuccess: clearPreview })
}

onBeforeUnmount(clearPreview)
</script>

<template>
    <Card>
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
                <Camera class="size-4 text-primary" />
                Profile Picture
            </CardTitle>
            <CardDescription class="text-sm text-muted-foreground"> A JPG, PNG or WebP image, up to 2 MB. </CardDescription>
        </CardHeader>
        <CardContent>
            <Form
                method="post"
                :action="account.avatar.update.url()"
                :options="{ preserveScroll: true }"
                @error="onError"
                @finish="resetFileInput"
                v-slot="{ errors, processing, submit }"
            >
                <div class="flex items-center gap-4">
                    <Avatar class="size-16 overflow-hidden rounded-full">
                        <AvatarImage :src="previewUrl ?? user.avatar ?? ''" :alt="user.name" class="object-cover" />
                        <AvatarFallback class="rounded-full bg-neutral-200 text-lg font-semibold text-black dark:bg-neutral-700 dark:text-white">
                            {{ getInitials(user.name) }}
                        </AvatarFallback>
                    </Avatar>

                    <input
                        ref="fileInput"
                        type="file"
                        name="avatar"
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden"
                        @change="onFileSelected($event, submit)"
                    />

                    <div class="flex flex-wrap gap-2">
                        <Button type="button" size="sm" variant="outline" :disabled="processing" @click="selectFile">
                            {{ processing ? 'Uploading...' : user.avatar ? 'Change Picture' : 'Upload Picture' }}
                        </Button>

                        <Button
                            v-if="user.avatar"
                            type="button"
                            size="sm"
                            variant="ghost"
                            :disabled="processing"
                            class="text-destructive hover:text-destructive"
                            @click="removeAvatar"
                        >
                            <Trash2 class="size-4" />
                            Remove
                        </Button>
                    </div>
                </div>

                <InputError class="mt-2 text-xs" :message="errors.avatar" />
            </Form>
        </CardContent>
    </Card>
</template>
