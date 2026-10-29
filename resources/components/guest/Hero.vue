<script setup lang="ts">
import InstallCommand from '@/components/guest/InstallCommand.vue'
import { Button } from '@/components/ui/button'
import { useAuth } from '@/composables/useAuth'
import { account, login, register } from '@/routes'
import { Link } from '@inertiajs/vue3'
import { ArrowRight, FolderGit } from '@lucide/vue'

defineProps<{ repositoryUrl: string }>()

const { user } = useAuth()
</script>

<template>
    <section class="flex w-full flex-col items-center text-center">
        <span
            class="mb-6 inline-flex items-center gap-2 rounded-full border bg-card/70 px-3 py-1 text-xs font-medium text-muted-foreground backdrop-blur"
        >
            <span class="size-1.5 rounded-full bg-primary"></span>
            Open source · MIT licensed
        </span>

        <h1 class="max-w-3xl font-display text-4xl font-semibold tracking-tight text-balance sm:text-5xl lg:text-6xl">
            Build on Laravel + Vue, <span class="text-primary">not on boilerplate.</span>
        </h1>

        <p class="mt-5 max-w-xl text-base text-pretty text-muted-foreground sm:text-lg">
            Authentication, passkeys, account and admin pages, typed routes, SSR and AI agent guidelines, ready from the first commit.
        </p>

        <div class="mt-8 w-full max-w-xl">
            <InstallCommand />
        </div>

        <div class="mt-8 flex items-center gap-x-4">
            <Button v-if="user" asChild>
                <Link :href="account.url()">Go to your account <ArrowRight /></Link>
            </Button>
            <Button v-else-if="$page.props.canRegister" asChild>
                <Link :href="register.url()">Get started <ArrowRight /></Link>
            </Button>
            <Button v-else asChild>
                <Link :href="login.url()">Log in <ArrowRight /></Link>
            </Button>
            <Button variant="ghost" asChild>
                <a :href="repositoryUrl" target="_blank" rel="noopener"><FolderGit /> GitHub</a>
            </Button>
        </div>
    </section>
</template>
