import dayjs from 'dayjs';

/**
 * Format a date/datetime string
 * @param {string|Date} date - The date to format
 * @param {string} format - The format string (defaults to 'DD/MM/YYYY HH:mm:ss')
 * @returns {string} Formatted date string
 */
export const formatWithFormat = (date, format = 'DD/MM/YYYY HH:mm:ss') => {
    return dayjs(date).format(format);
};

/**
 * Format a date (without time)
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted date string
 */
export const formatDate = date => {
    return dayjs(date).format('DD/MM/YYYY');
};

/**
 * Format a datetime
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted datetime string
 */
export const formatDateTime = date => {
    return dayjs(date).format('DD/MM/YYYY HH:mm:ss');
};
