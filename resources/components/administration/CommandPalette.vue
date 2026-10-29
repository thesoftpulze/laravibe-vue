<script setup lang="ts">
import { CommandDialog, CommandGroup, CommandInput, CommandItem, CommandList, CommandSeparator } from '@/components/ui/command'
import { useAppearance } from '@/composables/useAppearance'
import { isNavItemActive } from '@/lib/helpers'
import { cn } from '@/lib/utils'
import { account, home, logout } from '@/routes'
import administrationUsers from '@/routes/administration/users'
import { router } from '@inertiajs/vue3'
import { House, LogOut, Monitor, Moon, Sun, User, UserSearch } from '@lucide/vue'
import { useEventListener } from '@vueuse/core'
import { ListboxItem } from 'reka-ui'
import { computed, ref, watch } from 'vue'
import { useAdministrationNavigation } from './navigation'

const open = defineModel<boolean>('open', { required: true })

const { mainNavGroups } = useAdministrationNavigation()
const { updateAppearance } = useAppearance()

// Matches CommandItem's styling.
const commandItemClass =
    "relative flex cursor-default items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-hidden select-none data-[highlighted]:bg-accent data-[highlighted]:text-accent-foreground [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 [&_svg:not([class*='text-'])]:text-muted-foreground"

const query = ref('')
const userSearch = computed(() => query.value.trim())

useEventListener('keydown', (event: KeyboardEvent) => {
    if (event.key.toLowerCase() === 'k' && (event.metaKey || event.ctrlKey)) {
        event.preventDefault()
        open.value = !open.value
    }
})

watch(open, () => {
    query.value = ''
})

const run = (action: () => void) => {
    open.value = false
    action()
}

const visit = (href: string) => run(() => router.visit(href))

const logOut = () =>
    run(() => {
        router.flushAll()
        router.post(logout.url())
    })
</script>

<template>
    <CommandDialog v-model:open="open" title="Command palette" description="Go to a page, find a user or run an action.">
        <CommandInput placeholder="Search pages, users and actions…" @input="(event: Event) => (query = (event.target as HTMLInputElement).value)" />
        <CommandList>
            <template v-for="(group, index) in mainNavGroups" :key="group.label ?? index">
                <CommandGroup :heading="group.label">
                    <CommandItem v-for="item in group.items" :key="item.href" :value="item.href" @select="visit(item.href)">
                        <component :is="item.icon" />
                        {{ item.title }}
                        <span v-if="isNavItemActive(item)" class="ml-auto text-xs text-muted-foreground">Current</span>
                    </CommandItem>
                </CommandGroup>
            </template>

            <CommandGroup heading="General">
                <CommandItem value="account" @select="visit(account.url())">
                    <User />
                    Account
                </CommandItem>
                <CommandItem value="view-site" @select="visit(home.url())">
                    <House />
                    View site
                </CommandItem>
            </CommandGroup>

            <CommandGroup heading="Appearance">
                <CommandItem value="appearance-light" @select="run(() => updateAppearance('light'))">
                    <Sun />
                    Light theme
                </CommandItem>
                <CommandItem value="appearance-dark" @select="run(() => updateAppearance('dark'))">
                    <Moon />
                    Dark theme
                </CommandItem>
                <CommandItem value="appearance-system" @select="run(() => updateAppearance('system'))">
                    <Monitor />
                    System theme
                </CommandItem>
            </CommandGroup>

            <CommandGroup>
                <CommandItem value="log-out" @select="logOut">
                    <LogOut />
                    Log out
                </CommandItem>
            </CommandGroup>

            <!-- Not a CommandItem: its label follows the query, so the text filter must not hide it. -->
            <template v-if="userSearch">
                <CommandSeparator />
                <ListboxItem
                    :value="`search-users:${userSearch}`"
                    data-slot="command-item"
                    :class="cn(commandItemClass, 'm-1')"
                    @select="visit(administrationUsers.index.url({ query: { search: userSearch } }))"
                >
                    <UserSearch />
                    Search users for “{{ userSearch }}”
                </ListboxItem>
            </template>
        </CommandList>
    </CommandDialog>
</template>
