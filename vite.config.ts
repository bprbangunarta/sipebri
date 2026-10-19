import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import babel from '@rolldown/plugin-babel';
import tailwindcss from '@tailwindcss/vite';
import react, { reactCompilerPreset } from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig, lazyPlugins } from 'vite-plus';

// The company's clock (`config/app.php`, `timezone`): tests that write a time (`formatDateTime`) must give the same text on a laptop in Jakarta and on a
// CI machine set to UTC. Set before the test workers start, so they inherit it.
process.env.TZ = 'Asia/Jakarta';

export default defineConfig({
    plugins: lazyPlugins(() => [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        inertia(),
        react(),
        babel({
            presets: [reactCompilerPreset()],
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ]),
    server: {
        watch: {
            ignored: ['**/.agents/**', '**/.claude/**', '**/.cursor/**', '**/.junie/**', '**/vendor/**'],
        },
    },
    test: {
        environment: 'jsdom',
        setupFiles: ['./resources/js/test/setup.ts'],
        css: false,
        include: ['resources/js/**/*.test.{ts,tsx}'],
    },
    lint: {
        ignorePatterns: [
            'vendor/**',
            'node_modules/**',
            'public/**',
            'bootstrap/ssr/**',
            'tailwind.config.js',
            'resources/js/actions/**',
            'resources/js/routes/**',
            'resources/js/wayfinder/**',
        ],
        // Style rules outside the base lint set. They flag behaviour-neutral spreads in `lib/mask.ts` (ASCII letters and digits only).
        // Never run `vp check --fix` on resources/js: oxlint's autofix of these rules changed `mask.ts` behaviour in codex and broke its tests.
        rules: {
            'typescript/no-misused-spread': 'off',
            'unicorn/no-useless-spread': 'off',
        },
        options: {
            denyWarnings: true,
            typeAware: true,
        },
    },
    fmt: {
        printWidth: 160,
        tabWidth: 4,
        singleQuote: true,
        trailingComma: 'all',
        semi: true,
        singleAttributePerLine: false,
        htmlWhitespaceSensitivity: 'css',
        ignorePatterns: [
            '.github/**',
            // Vendored antislop skills: kept byte for byte as released (their contrast script parses a table in SKILL.md).
            '.ai/skills/**',
            'AGENTS.md',
            '.ai/rules/index.md',
            'lang/**',
            'composer.json',
            'pint.json',
            'pnpm-workspace.yaml',
            '.mcp.json',
            'resources/views/**',
            'resources/js/actions/**',
            'resources/js/routes/**',
            'resources/js/wayfinder/**',
        ],
        sortTailwindcss: {
            functions: ['clsx', 'cn', 'cva'],
            stylesheet: 'resources/css/app.css',
        },
        overrides: [{ files: ['*.json', '*.yml', '*.yaml'], options: { tabWidth: 2 } }],
    },
});
