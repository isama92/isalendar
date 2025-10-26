import { calculateEndsAtFromDuration, formatSuggestedEndsAt } from '@/Pages/Events/eventFormHelpers';
import { DATE_FORMAT, subtractDays } from '@/utils/date';
import axios from 'axios';
import { debounce } from 'es-toolkit';
import { useCallback, useEffect, useMemo, useState } from 'react';

/**
 * Display duration suggestions as clickable buttons
 * Fetches suggestions from the API based on event title and all-day status
 */
const DurationSuggestions = ({ title, startsAt, isAllDay, onSelect, isEditing = false }) => {
    const [suggestions, setSuggestions] = useState([]);
    const [error, setError] = useState(false);

    /**
     * Fetch duration suggestions from the API
     */
    const fetchSuggestions = useCallback(async (eventTitle, allDay) => {
        if (!eventTitle || eventTitle.length < 2) {
            setSuggestions([]);
            setError(false);
            return;
        }

        try {
            const response = await axios.get(route('events.suggest-duration'), {
                params: { title: eventTitle, all_day: allDay ? 1 : 0 },
            });
            setSuggestions(response.data || []);
            setError(false);
        } catch (err) {
            console.error('Error fetching duration suggestions:', err);
            setSuggestions([]);
            setError(true);
        }
    }, []);

    const debouncedFetchSuggestions = useMemo(() => debounce(fetchSuggestions, 1000), [fetchSuggestions]);

    // Fetch suggestions when title or all_day changes (debounced)
    useEffect(() => {
        debouncedFetchSuggestions(title, isAllDay);
    }, [title, isAllDay, debouncedFetchSuggestions]);

    // Fetch suggestions immediately on mount for editing
    useEffect(() => {
        if (isEditing && title && title.length >= 2) {
            fetchSuggestions(title, isAllDay);
        }
    }, []);

    if (!startsAt) {
        return null;
    }

    return (
        <>
            {error && (
                <p className="mt-2 text-xs text-yellow-600 dark:text-yellow-400">{__('events.suggestions_error')}</p>
            )}
            {suggestions.length > 0 && (
                <div className="mt-3">
                    <p className="mb-2 text-xs text-gray-500 dark:text-gray-400">{__('events.suggestions')}:</p>
                    <div className="flex flex-wrap gap-2">
                        {suggestions.map((durationMinutes, index) => {
                            let suggestedEndsAt = calculateEndsAtFromDuration(startsAt, durationMinutes, isAllDay);

                            // For all-day events, subtract 1 day for display (DB stores exclusive end date)
                            if (isAllDay && suggestedEndsAt) {
                                suggestedEndsAt = subtractDays(suggestedEndsAt, 1, DATE_FORMAT);
                            }

                            const formattedEndsAt = formatSuggestedEndsAt(suggestedEndsAt, isAllDay);

                            return (
                                <button
                                    key={index}
                                    type="button"
                                    onClick={() => onSelect(durationMinutes)}
                                    className="inline-flex cursor-pointer items-center rounded-md bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20"
                                >
                                    {formattedEndsAt}
                                </button>
                            );
                        })}
                    </div>
                </div>
            )}
        </>
    );
};

export default DurationSuggestions;
