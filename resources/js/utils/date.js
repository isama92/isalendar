import dayjs from 'dayjs';

/**
 * Format a date/datetime string
 * @param {string|Date} date - The date to format
 * @param {string} format - The format string (defaults to 'DD/MM/YYYY HH:mm:ss')
 * @returns {string} Formatted date string in user's timezone
 */
export const formatWithFormat = (date, format = 'DD/MM/YYYY HH:mm:ss') => {
    return dayjs.utc(date).tz().format(format);
};

/**
 * Format a date/datetime string with a specific timezone
 * @param {string|Date} date - The date to format
 * @param {string} timezone - The timezone to use for formatting
 * @param {string} format - The format string (defaults to 'DD/MM/YYYY HH:mm:ss')
 * @returns {string} Formatted date string in the specified timezone
 */
export const formatWithFormatAndTimezone = (date, timezone, format = 'DD/MM/YYYY HH:mm:ss') => {
    return dayjs.utc(date).tz(timezone).format(format);
};

/**
 * Format a date (without time)
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted date string in user's timezone
 */
export const formatDate = date => {
    return dayjs.utc(date).tz().format('DD/MM/YYYY');
};

/**
 * Format a datetime
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted datetime string in user's timezone
 */
export const formatDateTime = date => {
    return dayjs.utc(date).tz().format('DD/MM/YYYY HH:mm:ss');
};

/**
 * Format a datetime with a specific timezone
 * @param {string|Date} date - The date to format
 * @param {string} timezone - The timezone to use for formatting
 * @returns {string} Formatted datetime string in the specified timezone
 */
export const formatDateTimeWithTimezone = (date, timezone) => {
    return dayjs.utc(date).tz(timezone).format('DD/MM/YYYY HH:mm:ss');
};
