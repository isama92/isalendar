import dayjs from 'dayjs';

/**
 * Subtract days from a date
 * @param {string|Date} date - The date to subtract from
 * @param {number} days - Number of days to subtract
 * @param {string} format - The format string (defaults to 'YYYY-MM-DD')
 * @returns {string} Formatted date string
 */
export const subtractDays = (date, days = 1, format = 'YYYY-MM-DD') => {
    return dayjs(date).subtract(days, 'day').format(format);
};

/**
 * Add days to a date
 * @param {string|Date} date - The date to add to
 * @param {number} days - Number of days to add
 * @param {string} format - The format string (defaults to 'YYYY-MM-DD')
 * @returns {string} Formatted date string
 */
export const addDays = (date, days = 1, format = 'YYYY-MM-DD') => {
    return dayjs(date).add(days, 'day').format(format);
};

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

/**
 * Get the user's default timezone
 * @returns {string} The user's timezone (from dayjs default or guessed)
 */
export const getUserTimezone = () => {
    // When using tz() without arguments, it uses the default timezone
    // We can get the timezone name by formatting with 'z' or by using the internal _d property
    const tzDate = dayjs().tz();
    // Try to get the timezone name - dayjs stores it internally
    return tzDate.$x?.$timezone || dayjs.tz.guess();
};
