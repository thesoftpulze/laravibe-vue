<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { update } from '@/routes/password'
import { Form } from '@inertiajs/vue3'
import { LoaderCircle } from '@lucide/vue'
import { ref } from 'vue'

const props = defineProps<{
    token: string
    email: string
    passwordRules?: string
}>()

const inputEmail = ref(props.email)
</script>

<template>
    <Form
        method="post"
        :action="update.url()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input id="email" type="email" name="email" autocomplete="email" v-model="inputEmail" class="mt-1 block w-full" readonly />
                <InputError :message="errors.email" class="mt-2" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <Input
                    id="password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    :passwordrules="passwordRules"
                    class="mt-1 block w-full"
                    autofocus
                    placeholder="Password"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation"> Confirm Password </Label>
                <Input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    placeholder="Confirm password"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button type="submit" class="mt-4 w-full" :disabled="processing">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Reset password
            </Button>
        </div>
    </Form>
</template>
