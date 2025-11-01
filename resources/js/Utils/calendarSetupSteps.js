/**
 * Get the steps for the calendar setup process
 *
 * @param {Object} options - Optional configuration
 * @param {string} options.step1Href - Optional href for step 1 (choose provider)
 * @returns {Array} Array of step objects with name and description
 */
export const getCalendarSetupSteps = (options = {}) => {
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
