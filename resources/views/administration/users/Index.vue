<script setup lang="ts">
import Heading from '@/components/Heading.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import administrationUsers from '@/routes/administration/users'
import type { Paginated } from '@/types/data'
import type { AdministrationUser } from '@/types/models'
import { Link, router } from '@inertiajs/vue3'
import { Search } from '@lucide/vue'
import { useDebounceFn } from '@vueuse/core'
import { ref } from 'vue'

interface Props {
    users: Paginated<AdministrationUser>
    filters: {
        search: string
    }
}

const props = defineProps<Props>()

const search = ref(props.filters.search)

const applySearch = useDebounceFn(() => {
    router.get(administrationUsers.index.url(), search.value ? { search: search.value } : {}, {
        only: ['users', 'filters'],
        preserveState: true,
        replace: true,
    })
}, 300)
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <Heading title="Users" description="Everyone who has an account." class-names="mb-0" />

            <div class="relative w-full sm:max-w-xs">
                <Search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Search name or email"
                    class="pl-9"
                    aria-label="Search users"
                    @input="applySearch"
                />
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-left text-sm">
                <thead class="border-b bg-muted/50 text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users.data" :key="user.id" class="border-b last:border-b-0">
                        <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                {{ user.email }}
                                <Badge v-if="!user.email_verified_at" variant="outline">Unverified</Badge>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <Badge :variant="user.is_admin ? 'default' : 'secondary'">{{ user.is_admin ? 'Admin' : 'User' }}</Badge>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-muted-foreground">{{ user.created_at.slice(0, 10) }}</td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="4" class="px-4 py-10 text-center text-muted-foreground">No users match your search.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="users.meta.total > 0" class="flex items-center justify-between gap-4 text-sm text-muted-foreground">
            <p>Showing {{ users.meta.from }} to {{ users.meta.to }} of {{ users.meta.total }} users</p>

            <div class="flex gap-2">
                <Button variant="outline" size="sm" :disabled="!users.links.prev" :as-child="!!users.links.prev">
                    <Link v-if="users.links.prev" :href="users.links.prev" preserve-scroll>Previous</Link>
                    <span v-else>Previous</span>
                </Button>
                <Button variant="outline" size="sm" :disabled="!users.links.next" :as-child="!!users.links.next">
                    <Link v-if="users.links.next" :href="users.links.next" preserve-scroll>Next</Link>
                    <span v-else>Next</span>
                </Button>
            </div>
        </div>
    </div>
</template>
