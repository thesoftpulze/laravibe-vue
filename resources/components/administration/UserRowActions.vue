<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import administrationUsers from '@/routes/administration/users'
import type { AdministrationUser } from '@/types/models'
import { router } from '@inertiajs/vue3'
import { Ellipsis, MailCheck, ShieldCheck, ShieldOff, Trash2 } from '@lucide/vue'
import { computed, ref } from 'vue'

interface Props {
    user: AdministrationUser
}

const props = defineProps<Props>()

interface Confirmation {
    title: string
    description: string
    confirmLabel: string
    run: () => void
}

const confirmation = ref<Confirmation | null>(null)
const isConfirming = computed({
    get: () => confirmation.value !== null,
    set: (open: boolean) => {
        if (!open) {
            confirmation.value = null
        }
    },
})
const processing = ref(false)

const hasActions = computed(() => Object.values(props.user.can).some(Boolean))

const visitOptions = {
    preserveScroll: true,
    preserveState: true,
    onStart: () => (processing.value = true),
    onFinish: () => {
        processing.value = false
        confirmation.value = null
    },
}

const makeAdministrator = () => router.put(administrationUsers.role.update.url(props.user.id), { is_admin: true }, visitOptions)

const confirmRemoveAdministrator = () => {
    confirmation.value = {
        title: `Remove ${props.user.name} as an administrator?`,
        description: 'They will lose access to the administration straight away.',
        confirmLabel: 'Remove administrator',
        run: () => router.put(administrationUsers.role.update.url(props.user.id), { is_admin: false }, visitOptions),
    }
}

const resendVerification = () => router.post(administrationUsers.verification.store.url(props.user.id), {}, visitOptions)

const confirmDelete = () => {
    confirmation.value = {
        title: `Delete ${props.user.name}?`,
        description: 'Their account, profile picture and sessions are removed permanently. This cannot be undone.',
        confirmLabel: 'Delete user',
        run: () => router.delete(administrationUsers.destroy.url(props.user.id), visitOptions),
    }
}
</script>

<template>
    <div v-if="hasActions" class="flex justify-end">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" size="icon" class="size-8" :disabled="processing">
                    <Ellipsis />
                    <span class="sr-only">Actions for {{ user.name }}</span>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-56">
                <DropdownMenuLabel class="truncate">{{ user.name }}</DropdownMenuLabel>
                <DropdownMenuSeparator />
                <template v-if="user.can.updateRole">
                    <DropdownMenuItem v-if="user.is_admin" @select="confirmRemoveAdministrator">
                        <ShieldOff />
                        Remove administrator
                    </DropdownMenuItem>
                    <DropdownMenuItem v-else @select="makeAdministrator">
                        <ShieldCheck />
                        Make administrator
                    </DropdownMenuItem>
                </template>
                <DropdownMenuItem v-if="user.can.resendVerification" @select="resendVerification">
                    <MailCheck />
                    Resend verification email
                </DropdownMenuItem>
                <template v-if="user.can.delete">
                    <DropdownMenuSeparator />
                    <DropdownMenuItem variant="destructive" @select="confirmDelete">
                        <Trash2 />
                        Delete user
                    </DropdownMenuItem>
                </template>
            </DropdownMenuContent>
        </DropdownMenu>

        <Dialog v-model:open="isConfirming">
            <DialogContent v-if="confirmation" class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ confirmation.title }}</DialogTitle>
                    <DialogDescription>{{ confirmation.description }}</DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <Button variant="outline" :disabled="processing" @click="isConfirming = false">Cancel</Button>
                    <Button variant="destructive" :disabled="processing" @click="confirmation.run()">{{ confirmation.confirmLabel }}</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
