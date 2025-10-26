import dayjs from 'dayjs';

/**
 * Calculate the default end date/time based on start date and whether it's an all-day event
 * @param {string} startsAt - The start date/time
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The calculated end date/time
 */
export const calculateDefaultEndsAt = (startsAt, isAllDay) => {
    if (isAllDay) {
        return startsAt;
    }

    return dayjs(startsAt).add(1, 'hour').format('YYYY-MM-DD HH:mm:ss');
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
    if (!startsAt) {
        return null;
    }

    return dayjs(startsAt).toDate();
};

/**
 * Format a start date for submission based on whether it's an all-day event
 * @param {string} startsAt - The start date/time
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The formatted start date/time, or empty string if no date
 */
export const formatStartsAtForSubmission = (startsAt, isAllDay) => {
    if (!startsAt) {
        return '';
    }

    const format = isAllDay ? 'YYYY-MM-DD' : 'YYYY-MM-DD HH:mm:ss';
    return dayjs(startsAt).format(format);
};

/**
 * Format an end date for submission based on whether it's an all-day event
 * For all-day events, adds 1 day to the end date as per API requirements
 * @param {string} endsAt - The end date/time
 * @param {boolean} isAllDay - Whether it's an all-day event
 * @returns {string} The formatted end date/time, or empty string if no date
 */
export const formatEndsAtForSubmission = (endsAt, isAllDay) => {
    if (!endsAt) {
        return '';
    }

    if (isAllDay) {
        return dayjs(endsAt).add(1, 'day').format('YYYY-MM-DD');
    }

    return dayjs(endsAt).format('YYYY-MM-DD HH:mm:ss');
};
