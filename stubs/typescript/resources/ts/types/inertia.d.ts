import type { PageProps as InertiaPageProps } from '@inertiajs/core';

export type PageProps<
    TProps extends Record<string, unknown> | unknown[] = Record<string, unknown> | unknown[],
> = App.Data.InertiaSharedData & TProps;

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

