<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { useClipboard, useDebounceFn } from '@vueuse/core'
import { CheckCheck, Copy, SquareArrowOutUpRight } from '@lucide/vue'
import { type Component } from 'vue'
import { ToastActionPayload, type ToastActionType, ToastPayload } from './'

defineProps<{ toast: ToastPayload }>()

const { copy, copied } = useClipboard()
const copyToClipboard = useDebounceFn((payload: string) => copy(payload), 100)

const actionIcons = {
    copy: Copy,
    redirect: SquareArrowOutUpRight,
} satisfies Record<ToastActionType, Component>

const actionComponent = {
    copy: 'button',
    redirect: 'a',
} satisfies Record<ToastActionType, 'button' | 'a'>

const actionIcon = (action: ToastActionPayload): Component => (action.type === 'copy' && copied.value ? CheckCheck : actionIcons[action.type])

const actionLabel = (action: ToastActionPayload): string =>
    action.type === 'copy' && copied.value ? 'COPIED' : (action.label ?? action.type.toUpperCase())

const onActionClick = (action: ToastActionPayload, event: MouseEvent): void => {
    if (action.type !== 'copy') {
        return
    }

    event.preventDefault()
    copyToClipboard(action.payload)
}
</script>

<template>
    <div class="grid w-full! grid-cols-1 gap-1.5 text-sm">
        <p class="font-medium">{{ toast.message }}</p>
        <div v-if="toast.actions?.length" class="flex gap-1">
            <template v-for="(action, index) in toast.actions" :key="index">
                <Button size="sm" class="h-5 cursor-pointer px-2 text-xs! font-medium" as-child>
                    <component
                        :is="actionComponent[action.type]"
                        @click="onActionClick(action, $event)"
                        :href="action.type === 'redirect' ? action.payload : undefined"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <component :is="actionIcon(action)" class="size-3" />
                        {{ actionLabel(action) }}
                    </component>
                </Button>
            </template>
        </div>
    </div>
</template>
