<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import Button from '@/components/ui/button/Button.vue'
import { CodeXml, FolderGit } from '@lucide/vue'
</script>

<template>
    <div class="relative isolate flex h-screen items-center justify-center px-6 lg:-mt-18 lg:px-8">
        <div aria-hidden="true" class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
            <div
                style="
                    clip-path: polygon(
                        74.1% 44.1%,
                        100% 61.6%,
                        97.5% 26.9%,
                        85.5% 0.1%,
                        80.7% 2%,
                        72.5% 32.5%,
                        60.2% 62.4%,
                        52.4% 68.1%,
                        47.5% 58.3%,
                        45.2% 34.5%,
                        27.5% 76.7%,
                        0.1% 64.9%,
                        17.9% 100%,
                        27.6% 76.8%,
                        76.1% 97.7%,
                        74.1% 44.1%
                    );
                "
                class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-primary/40 to-primary/80 opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"
            ></div>
        </div>

        <div class="max-w-2xl">
            <div class="mb-8 flex justify-center">
                <Badge variant="outline" class="rounded-full px-3 py-1">We are still in early development. <CodeXml /> </Badge>
            </div>
            <div class="flex flex-col items-center justify-center text-center">
                <AppLogo class="text-4xl lg:text-6xl" />
                <p class="mt-8 text-lg font-medium text-pretty text-muted-foreground sm:text-xl/8">
                    A seamlessly optimized, clean, well-structured, and open-source Laravel & Vue 3 starter kit featuring Inertia, Vite, Tailwind CSS,
                    Shadcn Vue, and reusable utilities to accelerate your development. Focus on crafting your application, not the boilerplate.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <Button asChild>
                        <a href="https://github.com/thesoftpulze/laravibe-vue#installation" target="_blank" rel="noopener"> Installation </a>
                    </Button>
                    <Button variant="link" class="text-sm/6 font-semibold text-muted-foreground hover:text-primary" asChild>
                        <a href="https://github.com/thesoftpulze/laravibe-vue" target="_blank" rel="noopener">
                            <FolderGit /> Github Repo <span aria-hidden="true">→</span>
                        </a>
                    </Button>
                </div>
            </div>
        </div>

        <div
            aria-hidden="true"
            class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-50rem)]"
        >
            <div
                style="
                    clip-path: polygon(
                        74.1% 44.1%,
                        100% 61.6%,
                        97.5% 26.9%,
                        85.5% 0.1%,
                        80.7% 2%,
                        72.5% 32.5%,
                        60.2% 62.4%,
                        52.4% 68.1%,
                        47.5% 58.3%,
                        45.2% 34.5%,
                        27.5% 76.7%,
                        0.1% 64.9%,
                        17.9% 100%,
                        27.6% 76.8%,
                        76.1% 97.7%,
                        74.1% 44.1%
                    );
                "
                class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr from-primary/40 to-primary/80 opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"
            ></div>
        </div>
    </div>
</template>
