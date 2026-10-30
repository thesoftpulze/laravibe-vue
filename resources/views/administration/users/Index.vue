<script setup lang="ts">
import UserRowActions from '@/components/administration/UserRowActions.vue'
import Heading from '@/components/Heading.vue'
import UserInfo from '@/components/UserInfo.vue'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { formatDate } from '@/lib/helpers'
import { cn } from '@/lib/utils'
import administrationUsers from '@/routes/administration/users'
import type { Paginated, SortDirection, UserFilters, UserSortColumn } from '@/types/data'
import type { AdministrationUser } from '@/types/models'
import { Link, router } from '@inertiajs/vue3'
import { ArrowDown, ArrowUp, ArrowUpDown, Search, X } from '@lucide/vue'
import { useDebounceFn } from '@vueuse/core'
import { computed, ref, watch } from 'vue'

interface Props {
    users: Paginated<AdministrationUser>
    filters: UserFilters
}

const props = defineProps<Props>()

const DEFAULT_SORT: UserSortColumn = 'created_at'
const DEFAULT_DIRECTION: SortDirection = 'desc'

const search = ref(props.filters.search ?? '')
const role = ref(props.filters.role ?? 'all')
const verification = ref(props.filters.verification ?? 'all')
const sort = ref(props.filters.sort ?? DEFAULT_SORT)
const direction = ref(props.filters.direction ?? DEFAULT_DIRECTION)
const loading = ref(false)

const hasFilters = computed(() => search.value !== '' || role.value !== 'all' || verification.value !== 'all')

const query = computed(() => {
    const isDefaultOrder = sort.value === DEFAULT_SORT && direction.value === DEFAULT_DIRECTION

    return {
        ...(search.value.trim() !== '' && { search: search.value.trim() }),
        ...(role.value !== 'all' && { role: role.value }),
        ...(verification.value !== 'all' && { verification: verification.value }),
        ...(!isDefaultOrder && { sort: sort.value, direction: direction.value }),
    }
})

const reload = () => {
    router.get(administrationUsers.index.url(), query.value, {
        only: ['users', 'filters'],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onStart: () => (loading.value = true),
        onFinish: () => (loading.value = false),
    })
}

const reloadAfterTyping = useDebounceFn(reload, 300)

watch([() => search.value.trim(), role, verification], ([searchTerm], [previousSearchTerm]) =>
    searchTerm !== previousSearchTerm ? reloadAfterTyping() : reload(),
)

const sortBy = (column: UserSortColumn) => {
    if (sort.value === column) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
        sort.value = column
        direction.value = column === 'name' ? 'asc' : 'desc'
    }

    reload()
}

const sortIcon = (column: UserSortColumn) => {
    if (sort.value !== column) {
        return ArrowUpDown
    }

    return direction.value === 'asc' ? ArrowUp : ArrowDown
}

const ariaSort = (column: UserSortColumn) => {
    if (sort.value !== column) {
        return 'none'
    }

    return direction.value === 'asc' ? 'ascending' : 'descending'
}

const clearFilters = () => {
    search.value = ''
    role.value = 'all'
    verification.value = 'all'
}
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <Heading title="Users" description="Everyone who has an account." class-names="mb-0" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <div class="relative w-full sm:max-w-xs">
                <Search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="search" type="search" placeholder="Search name or email" class="pl-9" aria-label="Search users" />
            </div>

            <div class="flex gap-2">
                <Select v-model="role">
                    <SelectTrigger class="w-full sm:w-36" aria-label="Filter by role">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">All roles</SelectItem>
                        <SelectItem value="admin">Admins</SelectItem>
                        <SelectItem value="user">Users</SelectItem>
                    </SelectContent>
                </Select>

                <Select v-model="verification">
                    <SelectTrigger class="w-full sm:w-40" aria-label="Filter by email status">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Any email status</SelectItem>
                        <SelectItem value="verified">Verified</SelectItem>
                        <SelectItem value="unverified">Unverified</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <Button v-if="hasFilters" variant="ghost" size="sm" class="self-start sm:self-auto" @click="clearFilters">
                <X />
                Clear filters
            </Button>
        </div>

        <div
            :class="
                cn(
                    'rounded-xl border border-sidebar-border/70 transition-opacity dark:border-sidebar-border',
                    loading && 'pointer-events-none opacity-60',
                )
            "
            :aria-busy="loading"
        >
            <Table>
                <TableHeader class="bg-muted/50">
                    <TableRow>
                        <TableHead class="px-4" :aria-sort="ariaSort('name')">
                            <Button variant="ghost" size="sm" class="-ml-3 h-8" @click="sortBy('name')">
                                User
                                <component :is="sortIcon('name')" class="text-muted-foreground" />
                            </Button>
                        </TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead class="hidden sm:table-cell">Email</TableHead>
                        <TableHead :aria-sort="ariaSort('created_at')">
                            <Button variant="ghost" size="sm" class="-ml-3 h-8" @click="sortBy('created_at')">
                                Joined
                                <component :is="sortIcon('created_at')" class="text-muted-foreground" />
                            </Button>
                        </TableHead>
                        <TableHead class="w-12"><span class="sr-only">Actions</span></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in users.data" :key="user.id">
                        <TableCell class="px-4">
                            <div class="flex max-w-72 min-w-48 items-center gap-2">
                                <UserInfo :user="user" show-email />
                            </div>
                        </TableCell>
                        <TableCell>
                            <Badge :variant="user.is_admin ? 'default' : 'secondary'">{{ user.is_admin ? 'Admin' : 'User' }}</Badge>
                        </TableCell>
                        <TableCell class="hidden sm:table-cell">
                            <Badge v-if="user.email_verified_at" variant="outline">Verified</Badge>
                            <Badge v-else variant="outline" class="border-amber-300 text-amber-700 dark:border-amber-700 dark:text-amber-300">
                                Unverified
                            </Badge>
                        </TableCell>
                        <TableCell class="text-muted-foreground">
                            <time :datetime="user.created_at" :title="formatDate(user.created_at, { dateStyle: 'full', timeStyle: 'short' })">
                                {{ formatDate(user.created_at) }}
                            </time>
                        </TableCell>
                        <TableCell class="pr-4">
                            <UserRowActions :user="user" />
                        </TableCell>
                    </TableRow>
                    <TableEmpty v-if="users.data.length === 0" :colspan="5">
                        <div class="flex flex-col items-center gap-3 text-muted-foreground">
                            No users match these filters.
                            <Button v-if="hasFilters" variant="outline" size="sm" @click="clearFilters">Clear filters</Button>
                        </div>
                    </TableEmpty>
                </TableBody>
            </Table>
        </div>

        <div v-if="users.meta.total > 0" class="flex items-center justify-between gap-4 text-sm text-muted-foreground">
            <p>Showing {{ users.meta.from }} to {{ users.meta.to }} of {{ users.meta.total }} users</p>

            <div class="flex items-center gap-2">
                <span v-if="users.meta.last_page > 1" class="hidden sm:inline">Page {{ users.meta.current_page }} of {{ users.meta.last_page }}</span>
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
