<script setup lang="ts">
defineProps<{ repositoryUrl: string }>()

const stack = ['Laravel 13', 'Inertia 3', 'Vue 3', 'Tailwind 4', 'TypeScript']
</script>

<template>
    <footer class="flex flex-col items-center gap-6">
        <ul class="flex flex-wrap justify-center gap-x-3 gap-y-1 text-sm text-muted-foreground">
            <li v-for="(name, index) in stack" :key="name" class="flex items-center gap-x-3">
                <span v-if="index > 0" aria-hidden="true" class="text-border">·</span>
                {{ name }}
            </li>
        </ul>
        <div class="flex w-full flex-col items-center justify-between gap-2 border-t pt-4 text-xs text-muted-foreground sm:flex-row">
            <p>
                Made by <a href="https://softpulze.com" target="_blank" rel="noopener" class="font-medium hover:text-foreground">TheSoftPulze</a> ·
                <a :href="`${repositoryUrl}/blob/main/LICENSE`" target="_blank" rel="noopener" class="hover:text-foreground">MIT License</a>
            </p>
            <p>Replace this page in <code class="rounded bg-muted px-1.5 py-0.5 font-mono">resources/views/Home.vue</code></p>
        </div>
    </footer>
</template>
