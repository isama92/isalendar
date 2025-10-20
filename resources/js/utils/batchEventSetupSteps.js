/**
 * Get the steps for the batch event creation process
 *
 * @param {Object} options - Optional configuration
 * @param {string} options.step1Href - Optional href for step 1 (event details)
 * @returns {Array} Array of step objects with name and description
 */
export const getBatchEventSetupSteps = (options = {}) => {
    const { step1Href } = options;

    return [
        {
            name: __('events.event_details'),
            description: __('events.batch_event_details_description'),
            ...(step1Href && { href: step1Href }),
        },
        {
            name: __('events.event_dates'),
            description: __('events.batch_event_dates_description'),
        },
    ];
};
