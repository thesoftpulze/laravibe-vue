<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { home } from '@/routes'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
    status: 403 | 404 | 419 | 500 | 503
    supportEmail?: string | null
}>()

const messages = {
    403: { title: 'Forbidden', description: "Sorry, you don't have permission to access this page." },
    404: { title: 'Page Not Found', description: "Sorry, we couldn't find the page you're looking for." },
    419: { title: 'Page Expired', description: 'Your session has expired. Please refresh the page and try again.' },
    500: { title: 'Server Error', description: 'Whoops, something went wrong on our end. Please try again later.' },
    503: { title: 'Service Unavailable', description: "We're doing some maintenance. Please check back soon." },
}

const message = computed(() => messages[props.status])

const refresh = () => window.location.reload()
</script>

<template>
    <div class="flex min-h-[70vh] flex-col items-center justify-center px-6 text-center">
        <Head :title="message.title" />

        <p class="text-base font-semibold text-primary">{{ status }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-balance sm:text-5xl">{{ message.title }}</h1>
        <p class="mt-6 text-lg text-pretty text-muted-foreground">{{ message.description }}</p>

        <div class="mt-10 flex items-center justify-center gap-x-4">
            <Button v-if="status === 419" @click="refresh">Refresh Page</Button>
            <Button :variant="status === 419 ? 'outline' : 'default'" as-child>
                <Link :href="home()">Go Home</Link>
            </Button>
        </div>

        <p v-if="supportEmail" class="mt-8 text-sm text-muted-foreground">
            Still stuck? Email
            <a :href="`mailto:${supportEmail}`" class="font-medium text-foreground underline-offset-4 hover:underline">{{ supportEmail }}</a>
        </p>
    </div>
</template>
