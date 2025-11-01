import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

/**
 * Custom hook for managing URL query parameters
 * @param {Object} options Configuration options
 * @param {Object} options.defaults Default values for parameters
 * @param {Array<string>} options.validValues Optional object with arrays of valid values per param key
 * @returns {[Object, Function]} Current params and setter function
 */
export const useUrlParams = ({ defaults = {}, validValues = {} } = {}) => {
    const [params, setParamsState] = useState(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const initialParams = { ...defaults };

        Object.keys(defaults).forEach(key => {
            const value = urlParams.get(key);
            if (value !== null) {
                // Validate against allowed values if provided
                if (validValues[key] && !validValues[key].includes(value)) {
                    initialParams[key] = defaults[key];
                } else {
                    initialParams[key] = value;
                }
            }
        });

        return initialParams;
    });

    /**
     * Update URL parameters
     * @param {Object|Function} newParams New parameters object or updater function
     */
    const setParams = newParams => {
        setParamsState(prevParams => {
            const updatedParams =
                typeof newParams === 'function' ? newParams(prevParams) : { ...prevParams, ...newParams };

            return updatedParams;
        });
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
