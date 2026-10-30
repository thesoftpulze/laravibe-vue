import inertia from '@inertiajs/vite'
import { wayfinder } from '@laravel/vite-plugin-wayfinder'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { bunny } from 'laravel-vite-plugin/fonts'
import path from 'path'
import { defineConfig, lazyPlugins } from 'vite-plus'

export default defineConfig({
    plugins: lazyPlugins(() => [
        laravel({
            input: ['resources/app.ts'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
                bunny('Outfit', {
                    weights: [500, 600, 700],
                }),
            ],
        }),
        inertia({
            ssr: {
                cluster: true,
            },
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        wayfinder({
            formVariants: true,
        }),
    ]),
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources'),
        },
    },
    server: {
        watch: {
            ignored: ['**/.agents/**', '**/.claude/**', '**/.cursor/**', '**/.github/skills/**', '**/.junie/**', '**/demo/**', '**/vendor/**'],
        },
    },
    lint: {
        ignorePatterns: [
            'vendor/**',
            'node_modules/**',
            'public/**',
            'bootstrap/ssr/**',
            'demo/**',
            'resources/components/ui/*',
            'resources/actions/**',
            'resources/routes/**',
            'resources/wayfinder/**',
        ],
        options: {
            denyWarnings: true,
            typeAware: true,
        },
    },
    fmt: {
        printWidth: 150,
        tabWidth: 4,
        singleQuote: true,
        semi: false,
        singleAttributePerLine: false,
        htmlWhitespaceSensitivity: 'css',
        ignorePatterns: [
            '.agents/**',
            '.ai/**',
            '.claude/**',
            '.github/**',
            '.junie/**',
            '.lerd.yaml',
            '.mcp.json',
            'AGENTS.md',
            'CLAUDE.md',
            'boost.json',
            'composer.json',
            'demo/**',
            'opencode.json',
            'public/**',
            'resources/components/ui/*',
            'resources/blades/mail/*',
            'resources/actions/**',
            'resources/routes/**',
            'resources/wayfinder/**',
        ],
        sortTailwindcss: {
            functions: ['clsx', 'cn', 'cva'],
            stylesheet: 'resources/css/app.css',
        },
    },
})
