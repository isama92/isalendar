import { DATE_FORMAT, DATETIME_FORMAT, DISPLAY_DATE_FORMAT, DISPLAY_DATETIME_FORMAT } from '@/utils/date';
import dayjs from 'dayjs';

/**
 * Constants for default durations
 */
export const DEFAULT_DURATION_MINUTES = 60;

/**
 * Get the appropriate format string based on whether it's an all-day event
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} Format string
 */
const getFormatString = isAllDay => (isAllDay ? DATE_FORMAT : DATETIME_FORMAT);

/**
 * Get the appropriate display format string based on whether it's an all-day event
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} Display format string
 */
const getDisplayFormatString = isAllDay => (isAllDay ? DISPLAY_DATE_FORMAT : DISPLAY_DATETIME_FORMAT);

/**
 * Calculate end date/time by adding duration to start date
 * @param {string} startsAt - The start date/time
 * @param {number} durationMinutes - Duration in minutes to add to startsAt
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The calculated end date/time
 */
export const calculateEndsAtFromDuration = (startsAt, durationMinutes, isAllDay) => {
    if (!startsAt || durationMinutes == null) {
        return '';
    }

    const format = getFormatString(isAllDay);
    return dayjs(startsAt).add(durationMinutes, 'minute').format(format);
};

/**
 * Calculate the default end date/time based on start date and whether it's an all-day event
 * Uses DEFAULT_DURATION_MINUTES if no duration is specified
 * @param {string} startsAt - The start date/time
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @param {number|null} durationMinutes - Optional duration in minutes to add to startsAt
 * @returns {string} The calculated end date/time
 */
export const calculateDefaultEndsAt = (startsAt, isAllDay, durationMinutes = null) => {
    const duration = durationMinutes ?? (isAllDay ? 0 : DEFAULT_DURATION_MINUTES);
    return calculateEndsAtFromDuration(startsAt, duration, isAllDay);
};

/**
 * Format a suggested ends_at date for display
 * @param {string} endsAt - The end date/time
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The formatted end date/time
 */
export const formatSuggestedEndsAt = (endsAt, isAllDay) => {
    if (!endsAt) {
        return '';
    }

    const format = getDisplayFormatString(isAllDay);
    return dayjs(endsAt).format(format);
};

/**
 * Check if the end date is before the start date and should be cleared
 * @param {string} startsAt - The start date/time
 * @param {string} endsAt - The end date/time
 * @returns {boolean} Whether the end date should be cleared
 */
export const shouldClearEndsAt = (startsAt, endsAt) => {
    if (!endsAt || !startsAt) {
        return false;
    }

    return dayjs(endsAt).isBefore(dayjs(startsAt));
};

/**
 * Get the minimum date for the end date picker (based on start date)
 * @param {string|null} startsAt - The start date/time
 * @returns {Date|null} The minimum date as a Date object, or null if no start date
 */
export const getEndsAtMinDate = startsAt => {
    return startsAt ? dayjs(startsAt).toDate() : null;
};

/**
 * Format a date for submission based on whether it's an all-day event
 * @param {string} date - The date/time
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The formatted date/time, or empty string if no date
 */
const formatDateForSubmission = (date, isAllDay) => {
    if (!date) {
        return '';
    }

    const format = getFormatString(isAllDay);
    return dayjs(date).format(format);
};

/**
 * Format a start date for submission based on whether it's an all-day event
 * @param {string} startsAt - The start date/time
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The formatted start date/time, or empty string if no date
 */
export const formatStartsAtForSubmission = (startsAt, isAllDay) => {
    return formatDateForSubmission(startsAt, isAllDay);
};

/**
 * Format an end date for submission based on whether it's an all-day event
 * For all-day events, adds 1 day to the end date as per API requirements
 * (frontend shows inclusive end date, backend expects exclusive end date)
 * @param {string} endsAt - The end date/time
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The formatted end date/time, or empty string if no date
 */
export const formatEndsAtForSubmission = (endsAt, isAllDay) => {
    if (!endsAt) {
        return '';
    }

    if (isAllDay) {
        return dayjs(endsAt).add(1, 'day').format(DATE_FORMAT);
    }

    return formatDateForSubmission(endsAt, isAllDay);
};

/**
 * Convert a UTC date to a specific timezone for display in the form
 * For all-day events, returns the date as-is
 * @param {string|null} utcDate - The UTC date to convert
 * @param {string} timezone - The target timezone
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The formatted date in the target timezone
 */
export const convertUtcToEventTimezone = (utcDate, timezone, isAllDay) => {
    if (!utcDate) return '';
    if (isAllDay) return utcDate;

    return dayjs.utc(utcDate).tz(timezone).format(DATETIME_FORMAT);
};

/**
 * Adjust all-day event end date for display
 * Subtracts 1 day from the end date because the backend stores exclusive end dates
 * but the frontend displays inclusive end dates
 * @param {string} endsAt - The end date from the backend
 * @returns {string} The adjusted end date for display
 */
export const adjustAllDayEndDateForDisplay = endsAt => {
    if (!endsAt) return '';
    return dayjs(endsAt).subtract(1, 'day').format(DATE_FORMAT);
};
