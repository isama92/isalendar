export interface SetupStep {
    name: string;
    description: string;
    href?: string;
}

export interface CalendarSetupOptions {
    step1Href?: string;
}

/**
 * Get the steps for the calendar setup process
 *
 * @param options - Optional configuration
 * @returns Array of step objects with name and description
 */
export const getCalendarSetupSteps = (options: CalendarSetupOptions = {}): SetupStep[] => {
    const { step1Href } = options;

    return [
        {
            name: __('actions.named.select', { name: __('providers.provider') }),
            description: __('providers.select_provider_description'),
            ...(step1Href && { href: step1Href }),
        },
        {
            name: __('actions.named.select', { name: __('calendars.calendar') }),
            description: __('calendars.select_calendar_description'),
        },
    ];
};
