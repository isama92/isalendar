import { DATE_FORMAT, subtractDays } from '@/Utils/date';
import { useForm } from '@inertiajs/react';
import { useCallback } from 'react';
import {
    adjustAllDayEndDateForDisplay,
    calculateDefaultEndsAt,
    calculateEndsAtFromDuration,
    convertUtcToEventTimezone,
    formatEndsAtForSubmission,
    formatStartsAtForSubmission,
    shouldClearEndsAt,
} from '../Pages/Events/eventFormHelpers';

/**
 * Custom hook for managing event form state and logic
 * @param {Object} options Configuration options
 * @param {Object} options.event Existing event (for editing)
 * @param {Object} options.currentUser Current user object
 * @returns {Object} Form state and handlers
 */
export const useEventForm = ({ event = null, currentUser }) => {
    const isEditing = !!event;

    const { data, setData, post, put, processing, transform, errors, reset } = useForm({
        calendar_id: event?.calendar?.id || '',
        title: event?.title || '',
        all_day: event?.all_day || false,
        starts_at: event ? convertUtcToEventTimezone(event.starts_at, event.timezone, event.all_day) : '',
        ends_at: event
            ? event.all_day
                ? adjustAllDayEndDateForDisplay(event.ends_at)
                : convertUtcToEventTimezone(event.ends_at, event.timezone, event.all_day)
            : '',
        timezone: event?.timezone || currentUser.settings.default_timezone,
        description: event?.description || '',
    });

    /**
     * Handle changes to the start date/time
     */
    const handleStartsAtChange = useCallback(
        value => {
            setData('starts_at', value);

            if (!value) {
                return;
            }

            if (!data.ends_at || shouldClearEndsAt(value, data.ends_at)) {
                setData('ends_at', calculateDefaultEndsAt(value, data.all_day));
            }
        },
        [data.all_day, data.ends_at, setData]
    );

    /**
     * Handle template selection
     */
    const handleTemplateSelect = useCallback(
        template => {
            setData({
                ...data,
                calendar_id: template.calendar_id || '',
                title: template.event_title || '',
                all_day: template.event_all_day || false,
                timezone: template.event_timezone || currentUser.settings.default_timezone,
                description: template.event_description || '',
            });
        },
        [currentUser.settings.default_timezone, data, setData]
    );

    /**
     * Handle clicking a duration suggestion
     */
    const handleSuggestionClick = useCallback(
        durationMinutes => {
            if (!data.starts_at) {
                return;
            }

            let calculatedEndsAt = calculateEndsAtFromDuration(data.starts_at, durationMinutes, data.all_day);

            // For all-day events, subtract 1 day to match frontend display (DB stores exclusive end date)
            if (data.all_day && calculatedEndsAt) {
                calculatedEndsAt = subtractDays(calculatedEndsAt, 1, DATE_FORMAT);
            }

            setData('ends_at', calculatedEndsAt);
        },
        [data.all_day, data.starts_at, setData]
    );

    /**
     * Prepare form data for submission
     */
    const prepareSubmissionData = useCallback(
        redirectTo => ({
            ...data,
            starts_at: formatStartsAtForSubmission(data.starts_at, data.all_day),
            ends_at: formatEndsAtForSubmission(data.ends_at, data.all_day),
            redirect_to: redirectTo,
        }),
        [data]
    );

    /**
     * Handle form submission
     */
    const handleSubmit = useCallback(
        (e, redirectTo = null) => {
            e.preventDefault();

            const defaultRedirect = isEditing ? 'show' : 'new';
            const actualRedirect = redirectTo || defaultRedirect;

            transform(() => prepareSubmissionData(actualRedirect));

            if (isEditing) {
                put(route('events.update', event.id));
            } else {
                post(route('events.store'), {
                    onSuccess: () => {
                        if (actualRedirect === 'new') {
                            reset();
                        }
                    },
                });
            }
        },
        [isEditing, event, prepareSubmissionData, transform, put, post, reset]
    );

    return {
        data,
        setData,
        processing,
        errors,
        isEditing,
        handleStartsAtChange,
        handleTemplateSelect,
        handleSuggestionClick,
        handleSubmit,
    };
};
