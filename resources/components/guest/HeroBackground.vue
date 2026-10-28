<template>
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,var(--border)_1px,transparent_1px),linear-gradient(to_bottom,var(--border)_1px,transparent_1px)] mask-[radial-gradient(ellipse_60%_55%_at_50%_45%,black,transparent)] bg-size-[56px_56px] opacity-60"
        ></div>
        <div class="absolute top-1/3 left-1/2 size-176 -translate-1/2 rounded-full bg-primary/15 blur-3xl"></div>
        <svg viewBox="0 0 1440 320" preserveAspectRatio="none" class="absolute inset-x-0 bottom-0 h-56 w-full">
            <path
                d="M0 220c120 0 180-90 300-90s180 90 300 90 180-90 300-90 180 90 300 90 180-90 240-90"
                class="fill-none stroke-primary/25"
                stroke-width="2"
            />
            <path
                d="M0 260c120 0 180-90 300-90s180 90 300 90 180-90 300-90 180 90 300 90 180-90 240-90"
                class="fill-none stroke-primary/10"
                stroke-width="2"
            />
        </svg>
    </div>
</template>
