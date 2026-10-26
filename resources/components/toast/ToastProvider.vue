<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance'
import { BadgeAlert, BadgeCheck, BadgeInfo, BadgeX } from '@lucide/vue'
import { Toaster } from 'vue-sonner'
import 'vue-sonner/style.css'

const { appearance } = useAppearance()
</script>

<template>
    <Toaster :duration="5000" position="top-right" :visibleToasts="5" :theme="appearance" closeButton richColors>
        <template #error-icon>
            <BadgeX class="size-5" />
        </template>
        <template #success-icon>
            <BadgeCheck class="size-5" />
        </template>
        <template #warning-icon>
            <BadgeAlert class="size-5" />
        </template>
        <template #info-icon>
            <BadgeInfo class="size-5" />
        </template>
    </Toaster>
</template>
