<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { store } from '@/routes/password/confirm'
import { Form } from '@inertiajs/vue3'
import { LoaderCircle } from '@lucide/vue'
</script>

<template>
    <Form method="post" :action="store.url()" reset-on-success v-slot="{ errors, processing }">
        <div class="space-y-6">
            <div class="grid gap-2">
                <Label htmlFor="password">Password</Label>
                <Input id="password" type="password" name="password" class="mt-1 block w-full" required autofocus />

                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Button class="w-full" :disabled="processing">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    Confirm Password
                </Button>
            </div>
        </div>
    </Form>
</template>
