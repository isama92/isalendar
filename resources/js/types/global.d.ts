/**
 * Global type declarations for window object and global functions
 */

import { TranslationFunction } from './index';

declare global {
    /**
     * Translation function available globally via window.__
     */
    function __(key: string, replacements?: Record<string, string | number>): string;

    /**
     * Ziggy route function available globally via window.route
     */
    function route(name?: string, params?: Record<string, unknown> | unknown[], absolute?: boolean): string;

    interface Window {
        __: TranslationFunction;
        route: typeof route;
    }
}

export {};
