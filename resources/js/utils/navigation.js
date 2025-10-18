import { CalendarIcon, ChartPieIcon, DocumentIcon, UserGroupIcon } from '@heroicons/react/24/outline';

/**
 * Get the navigation items for the application sidebar
 *
 * @param {string} currentRoute - The current route name
 * @param {boolean} connectorEnabled - Whether the user has an enabled connector
 * @param {boolean} isSuperAdmin - Whether the user is a super admin
 * @returns {Array} Array of navigation item objects
 */
export const getNavigationItems = (currentRoute, connectorEnabled, isSuperAdmin) => {
    return [
        {
            name: __('dashboard.dashboard'),
            href: route('dashboard'),
            current: currentRoute === 'dashboard',
            icon: ChartPieIcon,
            visible: true,
        },
        {
            name: __('calendars.calendars'),
            href: route('calendars.index'),
            current: currentRoute === 'calendars.index',
            icon: CalendarIcon,
            visible: true,
        },
        {
            name: __('events.events'),
            href: route('events.index'),
            current: currentRoute === 'events.index',
            icon: DocumentIcon,
            visible: connectorEnabled,
        },
        {
            name: __('users.users'),
            href: route('users.index'),
            current: currentRoute === 'users.index',
            icon: UserGroupIcon,
            visible: isSuperAdmin,
        },
    ].filter(item => item.visible);
};
