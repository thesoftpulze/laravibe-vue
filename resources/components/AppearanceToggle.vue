<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { type Appearance, useAppearance } from '@/composables/useAppearance'
import { Monitor, Moon, Sun } from '@lucide/vue'

const { appearance, updateAppearance } = useAppearance()

const options = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' },
] as const
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="size-8">
                <Sun class="dark:hidden" />
                <Moon class="hidden dark:block" />
                <span class="sr-only">Change appearance</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="min-w-36">
            <DropdownMenuRadioGroup :model-value="appearance" @update:model-value="(value) => updateAppearance(value as Appearance)">
                <DropdownMenuRadioItem v-for="{ value, Icon, label } in options" :key="value" :value="value">
                    <component :is="Icon" />
                    {{ label }}
                </DropdownMenuRadioItem>
            </DropdownMenuRadioGroup>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
