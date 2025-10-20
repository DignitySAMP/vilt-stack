import { Config as ZiggyConfig } from 'ziggy-js';
import { PageProps as InertiaPageProps } from '@inertiajs/core';

declare module '@inertiajs/core' {
  interface PageProps {
    // shared props go here
    ziggy?: ZiggyConfig & {
      location: string;
      query?: Record<string, any>;
    };
    errors?: Record<string, string>;
    flash?: {
      success?: string;
      error?: string;
      warning?: string;
      info?: string;
    };
  }
}

export type PageProps<T = Record<string, unknown>> = T & InertiaPageProps;

export {};

