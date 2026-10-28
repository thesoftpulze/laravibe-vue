<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { router } from '@inertiajs/vue3'
import { useClipboard } from '@vueuse/core'
import { ArrowRight, CheckCheck, Copy, SquareArrowOutUpRight } from '@lucide/vue'
import { type Component, ref } from 'vue'
import { toast as vueSoonerToast } from 'vue-sonner'
import { type ToastActionPayload, type ToastActionType, type ToastPayload } from './'

const props = defineProps<{ toast: ToastPayload; toastId?: string | number }>()

const { copy, copied } = useClipboard({ copiedDuring: 2000, legacy: true })
const copiedIndex = ref<number | null>(null)

const actionIcons = {
    copy: Copy,
    redirect: SquareArrowOutUpRight,
    visit: ArrowRight,
} satisfies Record<ToastActionType, Component>

const actionLabels = {
    copy: 'Copy',
    redirect: 'Open',
    visit: 'View',
} satisfies Record<ToastActionType, string>

const isCopied = (index: number): boolean => copied.value && copiedIndex.value === index

const actionIcon = (action: ToastActionPayload, index: number): Component => (isCopied(index) ? CheckCheck : actionIcons[action.type])

const actionLabel = (action: ToastActionPayload, index: number): string => (isCopied(index) ? 'Copied' : (action.label ?? actionLabels[action.type]))

const onActionClick = async (action: ToastActionPayload, index: number, event: MouseEvent): Promise<void> => {
    // Let the browser handle new-tab links, and modified clicks on visit links (e.g. Cmd+click).
    if (action.type === 'redirect' || (action.type === 'visit' && (event.metaKey || event.ctrlKey || event.shiftKey))) {
        return
    }

    event.preventDefault()

    if (action.type === 'visit') {
        vueSoonerToast.dismiss(props.toastId)
        router.visit(action.payload)

        return
    }

    copiedIndex.value = index
    await copy(action.payload)
}
</script>

<template>
    <div class="grid w-full! grid-cols-1 gap-1 text-sm">
        <p class="leading-snug font-medium">{{ toast.message }}</p>
        <p v-if="toast.description" class="leading-snug text-muted-foreground">{{ toast.description }}</p>
        <div v-if="toast.actions?.length" class="mt-1.5 flex flex-wrap gap-1.5">
            <Button v-for="(action, index) in toast.actions" :key="index" size="xs" :variant="index === 0 ? 'default' : 'outline'" as-child>
                <a
                    v-if="action.type !== 'copy'"
                    :href="action.payload"
                    :target="action.type === 'redirect' ? '_blank' : undefined"
                    :rel="action.type === 'redirect' ? 'noopener noreferrer' : undefined"
                    class="cursor-pointer"
                    @click="onActionClick(action, index, $event)"
                >
                    {{ actionLabel(action, index) }}
                    <component :is="actionIcon(action, index)" />
                </a>
                <button v-else type="button" class="cursor-pointer" @click="onActionClick(action, index, $event)">
                    <component :is="actionIcon(action, index)" />
                    {{ actionLabel(action, index) }}
                </button>
            </Button>
        </div>
    </div>
</template>
