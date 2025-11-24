import type { Page, Router } from '@inertiajs/core';
import { createHeadManager } from '@inertiajs/vue3';

declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME?: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string, options?: { eager?: boolean }) => Record<string, () => Promise<T>>;
    }
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $inertia: typeof Router;
        $page: Page;
        $headManager: ReturnType<typeof createHeadManager>;
    }
}

