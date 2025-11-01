import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

/**
 * Custom hook for handling flash notifications from Laravel
 * Integrates with Laravel's session flash messages passed via Inertia
 *
 * @returns {Object} Notification state and handlers
 */
export const useNotification = () => {
    const { flash } = usePage().props;
    const [notification, setNotification] = useState(null);

    useEffect(() => {
        if (flash?.success) {
            setNotification({
                type: 'success',
                message: flash.success,
            });
        } else if (flash?.error) {
            setNotification({
                type: 'error',
                message: flash.error,
            });
        } else if (flash?.warning) {
            setNotification({
                type: 'warning',
                message: flash.warning,
            });
        } else if (flash?.info) {
            setNotification({
                type: 'info',
                message: flash.info,
            });
        }
    }, [flash]);

    /**
     * Clear the current notification
     */
    const clearNotification = () => {
        setNotification(null);
    };

    /**
     * Show a custom notification
     * @param {string} type Notification type (success, error, warning, info)
     * @param {string} message Notification message
     */
    const showNotification = (type, message) => {
        setNotification({ type, message });
    };

    return {
        notification,
        clearNotification,
        showNotification,
    };
};
