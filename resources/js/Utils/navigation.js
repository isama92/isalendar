import { ROUTES } from '@/Constants';
import {
    CalendarIcon,
    ChartPieIcon,
    DocumentDuplicateIcon,
    DocumentIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';

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
            href: route(ROUTES.DASHBOARD),
            current: currentRoute === ROUTES.DASHBOARD,
            icon: ChartPieIcon,
            visible: true,
        },
        {
            name: __('calendars.calendars'),
            href: route(ROUTES.CALENDARS.INDEX),
            current: currentRoute === ROUTES.CALENDARS.INDEX,
            icon: CalendarIcon,
            visible: true,
        },
        {
            name: __('events.events'),
            href: route(ROUTES.EVENTS.INDEX),
            current: currentRoute === ROUTES.EVENTS.INDEX,
            icon: DocumentIcon,
            visible: connectorEnabled,
        },
        {
            name: __('event-templates.event_templates'),
            href: route(ROUTES.EVENT_TEMPLATES.INDEX),
            current: currentRoute === ROUTES.EVENT_TEMPLATES.INDEX,
            icon: DocumentDuplicateIcon,
            visible: connectorEnabled,
        },
        {
            name: __('users.users'),
            href: route(ROUTES.USERS.INDEX),
            current: currentRoute === ROUTES.USERS.INDEX,
            icon: UserGroupIcon,
            visible: isSuperAdmin,
        },
    ].filter(item => item.visible);
};
