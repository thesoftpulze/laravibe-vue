<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue'
import AppLogo from '@/components/AppLogo.vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet'
import UserMenuContent from '@/components/UserMenuContent.vue'
import { useAuth } from '@/composables/useAuth'
import { getInitials } from '@/composables/useInitials'
import { isNavItemActive } from '@/lib/helpers'
import { cn } from '@/lib/utils'
import type { NavItem } from '@/types'
import type { Breadcrumb } from '@/types/data'
import { home, login, register } from '@/routes'
import { Link, usePage } from '@inertiajs/vue3'
import { Menu } from '@lucide/vue'
import { computed, ref } from 'vue'

interface Props {
    breadcrumbs?: Breadcrumb[]
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
})

const { user } = useAuth()
const page = usePage()
const currentPath = computed(() => new URL(page.url, 'http://localhost').pathname)

const navigationItems = ref<NavItem[]>([
    {
        title: 'Home',
        href: home.url(),
        isActive: () => currentPath.value === home.url(),
    },
])
</script>

<template>
    <div class="z-50">
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button variant="ghost" size="icon" class="mr-2 h-9 w-9">
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-75 p-6">
                            <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                            <SheetHeader class="flex justify-start text-left">
                                <AppLogo class="text-lg" />
                            </SheetHeader>
                            <div class="flex h-full flex-1 flex-col justify-between space-y-4 py-6">
                                <nav class="-mx-3 space-y-1">
                                    <Link
                                        v-for="item in navigationItems"
                                        :key="item.title"
                                        :href="item.href"
                                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                        :class="isNavItemActive(item) ? 'bg-accent text-accent-foreground' : ''"
                                    >
                                        <component v-if="item.icon" :is="item.icon" class="h-5 w-5" />
                                        {{ item.title }}
                                    </Link>
                                </nav>
                                <div class="flex flex-col space-y-4">
                                    <div :class="['grid gap-2 lg:hidden', $page.props.canRegister ? 'grid-cols-2' : 'grid-cols-1']" v-if="!user">
                                        <Button variant="outline" size="sm" class="h-9" asChild>
                                            <Link :href="login.url()"> Log In </Link>
                                        </Button>
                                        <Button v-if="$page.props.canRegister" size="sm" class="h-9" asChild>
                                            <Link :href="register.url()"> Sign Up </Link>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <!-- Logo -->
                <Link :href="home.url()" class="flex items-center gap-x-2">
                    <AppLogo class="text-xl" />
                </Link>

                <!-- Desktop Navigation Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10">
                        <NavigationMenuList>
                            <NavigationMenuItem v-for="item in navigationItems" :key="item.title">
                                <NavigationMenuLink
                                    :class="
                                        cn(navigationMenuTriggerStyle(), 'h-9 px-3', isNavItemActive(item) ? 'bg-accent text-accent-foreground' : '')
                                    "
                                    as-child
                                >
                                    <Link :href="item.href">
                                        <component v-if="item.icon" :is="item.icon" class="mr-2 h-4 w-4" />
                                        {{ item.title }}
                                    </Link>
                                </NavigationMenuLink>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <!-- Right Side Actions -->
                <div class="ml-auto flex items-center space-x-2">
                    <!-- User Menu -->
                    <DropdownMenu v-if="user">
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                            >
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    <AvatarImage :src="user.avatar ?? ''" :alt="user.name" class="object-cover" />
                                    <AvatarFallback class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                        {{ getInitials(user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="user" />
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <!-- Auth Buttons -->
                    <template v-else>
                        <div :class="['hidden gap-2 lg:inline-grid', $page.props.canRegister ? 'grid-cols-2' : 'grid-cols-1']">
                            <Button variant="outline" size="sm" class="h-9" asChild>
                                <Link :href="login.url()"> Log In </Link>
                            </Button>
                            <Button v-if="$page.props.canRegister" size="sm" class="h-9" asChild>
                                <Link :href="register.url()"> Sign Up </Link>
                            </Button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Breadcrumbs -->
        <div v-if="breadcrumbs.length > 1" class="flex w-full border-b border-sidebar-border/70">
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
