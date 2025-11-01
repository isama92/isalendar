import type { SetupStep } from './calendarSetupSteps';

export interface BatchEventSetupOptions {
    step1Href?: string;
}

/**
 * Get the steps for the batch event creation process
 *
 * @param options - Optional configuration
 * @returns Array of step objects with name and description
 */
export const getBatchEventSetupSteps = (options: BatchEventSetupOptions = {}): SetupStep[] => {
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
