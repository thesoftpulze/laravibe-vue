<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import { Button } from '@/components/ui/button'
import { logout } from '@/wayfinder/routes'
import { send } from '@/wayfinder/routes/verification'
import { Form } from '@inertiajs/vue3'
import { LoaderCircle } from '@lucide/vue'
</script>

<template>
    <Form method="post" :action="send.url()" class="space-y-6 text-center" v-slot="{ processing }">
        <Button :disabled="processing" variant="secondary">
            <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
            Resend verification email
        </Button>

        <TextLink :href="logout.url()" method="post" as="button" class="mx-auto block text-sm"> Log out </TextLink>
    </Form>
</template>
