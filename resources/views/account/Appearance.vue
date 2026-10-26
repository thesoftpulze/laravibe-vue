<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import AppearanceTabs from './partial/AppearanceTabs.vue'

import SettingsLayout from '@/layouts/AccountLayout.vue'
import { SwatchBook } from '@lucide/vue'
</script>

<template>
    <SettingsLayout>
        <div class="space-y-6">
            <Card>
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-2 text-lg font-medium">
                        <SwatchBook class="size-4 text-primary" />
                        Appearance
                    </CardTitle>
                    <CardDescription class="text-sm text-muted-foreground"> Adjust your preferences for a personalized experience. </CardDescription>
                </CardHeader>
                <CardContent>
                    <AppearanceTabs />
                </CardContent>
            </Card>
        </div>
    </SettingsLayout>
</template>
