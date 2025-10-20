/// <reference types="vite/client" />

declare module '*.vue' {
  import type { DefineComponent } from 'vue';
  const component: DefineComponent<{}, {}, any>;
  export default component;
}

interface ImportMetaEnv {
  readonly VITE_APP_NAME: string;
  // env props go here
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
  readonly glob: (pattern: string, options?: { eager?: boolean }) => Record<string, any>;
}

