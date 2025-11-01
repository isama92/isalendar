import type { PageProps } from '@/types';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export type NotificationType = 'success' | 'error' | 'warning' | 'info';

export interface Notification {
    type: NotificationType;
    message: string;
}

export interface UseNotificationReturn {
    notification: Notification | null;
    clearNotification: () => void;
    showNotification: (type: NotificationType, message: string) => void;
}

/**
 * Custom hook for handling flash notifications from Laravel
 * Integrates with Laravel's session flash messages passed via Inertia
 *
 * @returns Notification state and handlers
 */
export const useNotification = (): UseNotificationReturn => {
    const { flash } = usePage<PageProps>().props;
    const [notification, setNotification] = useState<Notification | null>(null);

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
    const clearNotification = (): void => {
        setNotification(null);
    };

    /**
     * Show a custom notification
     * @param type - Notification type (success, error, warning, info)
     * @param message - Notification message
     */
    const showNotification = (type: NotificationType, message: string): void => {
        setNotification({ type, message });
    };

    return {
        notification,
        clearNotification,
        showNotification,
    };
};
