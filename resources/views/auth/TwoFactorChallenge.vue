<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { store } from '@/routes/two-factor/login'
import { Form } from '@inertiajs/vue3'
import { LoaderCircle } from '@lucide/vue'
import { ref } from 'vue'

const usingRecoveryCode = ref(false)

const toggleRecoveryCode = () => {
    usingRecoveryCode.value = !usingRecoveryCode.value
}
</script>

<template>
    <Form
        :key="usingRecoveryCode ? 'recovery' : 'code'"
        method="post"
        :action="store.url()"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div v-if="usingRecoveryCode" class="grid gap-2">
            <Label for="recovery_code">Recovery code</Label>
            <Input id="recovery_code" name="recovery_code" type="text" required autofocus autocomplete="off" placeholder="abcdefghij-abcdefghij" />
            <InputError :message="errors.recovery_code" />
        </div>

        <div v-else class="grid gap-2">
            <Label for="code">Authentication code</Label>
            <Input
                id="code"
                name="code"
                type="text"
                required
                autofocus
                inputmode="numeric"
                maxlength="6"
                autocomplete="one-time-code"
                placeholder="123456"
            />
            <InputError :message="errors.code" />
        </div>

        <Button type="submit" class="w-full" :disabled="processing">
            <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
            Continue
        </Button>

        <button type="button" class="text-center text-sm text-muted-foreground underline-offset-4 hover:underline" @click="toggleRecoveryCode">
            {{ usingRecoveryCode ? 'Use an authentication code instead' : 'Use a recovery code instead' }}
        </button>
    </Form>
</template>
