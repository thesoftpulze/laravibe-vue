<script setup lang="ts">
import Footer from '@/components/guest/Footer.vue'
import Hero from '@/components/guest/Hero.vue'
import HeroBackground from '@/components/guest/HeroBackground.vue'

const repositoryUrl = 'https://github.com/thesoftpulze/laravibe-vue'
</script>

<template>
    <div class="relative isolate flex flex-1 flex-col px-4">
        <HeroBackground />
        <div class="flex flex-1 items-center justify-center py-12 lg:py-0">
            <Hero :repository-url="repositoryUrl" />
        </div>
        <Footer :repository-url="repositoryUrl" class="pb-6" />
    </div>
</template>
