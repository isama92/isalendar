import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export interface UseUrlParamsOptions<T extends Record<string, string>> {
    defaults?: T;
    validValues?: Partial<Record<keyof T, string[]>>;
}

export type SetParamsFunction<T> = (newParams: Partial<T> | ((prevParams: T) => T)) => void;

/**
 * Custom hook for managing URL query parameters
 * @param options - Configuration options
 * @returns Current params and setter function
 */
export const useUrlParams = <T extends Record<string, string>>({
    defaults = {} as T,
    validValues = {},
}: UseUrlParamsOptions<T> = {}): [T, SetParamsFunction<T>] => {
    const [params, setParamsState] = useState<T>(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const initialParams = { ...defaults };

        Object.keys(defaults).forEach(key => {
            const value = urlParams.get(key);
            if (value !== null) {
                // Validate against allowed values if provided
                const validValuesForKey = validValues[key as keyof T];
                if (validValuesForKey && !validValuesForKey.includes(value)) {
                    initialParams[key as keyof T] = defaults[key as keyof T];
                } else {
                    initialParams[key as keyof T] = value as T[keyof T];
                }
            }
        });

        return initialParams;
    });

    /**
     * Update URL parameters
     * @param newParams - New parameters object or updater function
     */
    const setParams: SetParamsFunction<T> = newParams => {
        setParamsState(prevParams =>
            typeof newParams === 'function' ? newParams(prevParams) : { ...prevParams, ...newParams }
        );
    };

    // Sync params to URL when they change
    useEffect(() => {
        const searchParams = new URLSearchParams();

        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                searchParams.set(key, String(value));
            }
        });

        const newUrl = `${window.location.pathname}?${searchParams.toString()}`;

        router.visit(newUrl, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }, [params]);

    return [params, setParams];
};
