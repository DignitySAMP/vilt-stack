import { Config as ZiggyConfig } from 'ziggy-js';

declare global {
  interface Window {
    Ziggy: ZiggyConfig & {
      location: string;
    };
  }

  var Ziggy: ZiggyConfig & {
    location: string;
  };

  var route: {
    (name?: undefined): string;
    (name: string, params?: RouteParamsWithQueryOverload | RouteParam, absolute?: boolean): string;
    current(): string | undefined;
    current(name: string): boolean;
    has(name: string): boolean;
    params: RouteParams;
  };
}

export interface RouteParams {
  [key: string]: RouteParam;
}

export type RouteParam = string | number | boolean | null | undefined | RouteParams | RouteParam[];

export type RouteParamsWithQueryOverload = RouteParams | undefined;

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $route: typeof route;
  }
}

export {};

