<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { pushToast } from '@/components/toast'
import { cn } from '@/lib/utils'
import { Check, Copy } from '@lucide/vue'
import { useTimeoutFn } from '@vueuse/core'
import { computed, ref } from 'vue'

const commands = {
    laravel: 'laravel new my-app --using=thesoftpulze/laravibe-vue',
    composer: 'composer create-project thesoftpulze/laravibe-vue my-app',
} as const

type Installer = keyof typeof commands

const installer = ref<Installer>('laravel')
const command = computed(() => commands[installer.value])

const copiedCommand = ref<string | null>(null)
const copied = computed(() => copiedCommand.value === command.value)
const { start: resetCopied } = useTimeoutFn(() => (copiedCommand.value = null), 2000, { immediate: false })

const copy = async (value: string): Promise<boolean> => {
    try {
        await navigator.clipboard.writeText(value)

        return true
    } catch {
        const textarea = document.createElement('textarea')
        textarea.value = value
        textarea.style.position = 'absolute'
        textarea.style.opacity = '0'
        document.body.appendChild(textarea)

        try {
            textarea.select()

            return document.execCommand('copy')
        } finally {
            textarea.remove()
        }
    }
}

const copyCommand = async (): Promise<void> => {
    if (!navigator.clipboard) {
        pushToast({ type: 'error', message: 'Copying is not available in this browser.' })

        return
    }

    const value = command.value

    try {
        if (!(await copy(value))) {
            throw new Error('Clipboard write failed.')
        }
    } catch {
        copiedCommand.value = null
        pushToast({ type: 'error', message: 'Could not copy the install command.' })

        return
    }

    copiedCommand.value = value
    resetCopied()
    pushToast({ type: 'success', message: 'Install command copied.' })
}
</script>

<template>
    <div class="w-full max-w-xl">
        <div class="mb-2 flex justify-center gap-1 text-xs">
            <button
                v-for="name in Object.keys(commands) as Installer[]"
                :key="name"
                type="button"
                :class="
                    cn(
                        'rounded-full px-3 py-1 font-medium transition-colors',
                        installer === name ? 'bg-foreground text-background' : 'text-muted-foreground hover:text-foreground',
                    )
                "
                @click="installer = name"
            >
                {{ name }}
            </button>
        </div>
        <div class="flex items-center gap-3 rounded-xl border bg-card/80 py-2 pr-2 pl-4 shadow-sm backdrop-blur">
            <code class="min-w-0 flex-1 truncate text-left font-mono text-sm"> <span class="text-primary select-none">$ </span>{{ command }} </code>
            <Button variant="ghost" size="icon" class="size-8 shrink-0" :aria-label="copied ? 'Copied' : 'Copy install command'" @click="copyCommand">
                <Check v-if="copied" class="size-4 text-primary" />
                <Copy v-else class="size-4" />
            </Button>
        </div>
    </div>
</template>
