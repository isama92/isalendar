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
