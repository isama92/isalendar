import { CalendarIcon, ChartPieIcon, DocumentIcon } from '@heroicons/react/24/outline';

/**
 * Get the navigation items for the application sidebar
 *
 * @param {string} currentRoute - The current route name
 * @param {boolean} connectorEnabled - Whether the user has an enabled connector
 * @returns {Array} Array of navigation item objects
 */
export const getNavigationItems = (currentRoute, connectorEnabled) => {
    return [
        {
            name: __('dashboard.dashboard'),
            href: route('dashboard'),
            current: currentRoute === 'dashboard',
            icon: ChartPieIcon,
        },
        {
            name: __('calendars.calendars'),
            href: route('calendars.index'),
            current: currentRoute === 'calendars.index',
            icon: CalendarIcon,
        },
        ...(connectorEnabled
            ? [
                  {
                      name: __('events.events'),
                      href: route('events.index'),
                      current: currentRoute === 'events.index',
                      icon: DocumentIcon,
                  },
              ]
            : []),
    ];
};
