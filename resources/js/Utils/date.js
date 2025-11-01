import dayjs from 'dayjs';

/**
 * Date format constants
 */
export const DATE_FORMAT = 'YYYY-MM-DD';
export const DATETIME_FORMAT = 'YYYY-MM-DD HH:mm:ss';
export const DISPLAY_DATE_FORMAT = 'DD/MM/YYYY';
export const DISPLAY_DATETIME_FORMAT = 'DD/MM/YYYY HH:mm';

/**
 * Modify a date by adding or subtracting time units
 * @param {string|Date} date - The date to modify
 * @param {number} amount - Amount to add (positive) or subtract (negative)
 * @param {string} unit - Unit of time ('day', 'hour', 'minute', etc.)
 * @param {string} format - The format string (defaults to DATE_FORMAT)
 * @returns {string} Formatted date string
 */
export const modifyDate = (date, amount, unit = 'day', format = DATE_FORMAT) => {
    return dayjs(date).add(amount, unit).format(format);
};

/**
 * Add days to a date (convenience wrapper for modifyDate)
 * @param {string|Date} date - The date to add to
 * @param {number} days - Number of days to add
 * @param {string} format - The format string (defaults to DATE_FORMAT)
 * @returns {string} Formatted date string
 */
export const addDays = (date, days = 1, format = DATE_FORMAT) => {
    return modifyDate(date, days, 'day', format);
};

/**
 * Subtract days from a date (convenience wrapper for modifyDate)
 * @param {string|Date} date - The date to subtract from
 * @param {number} days - Number of days to subtract
 * @param {string} format - The format string (defaults to DATE_FORMAT)
 * @returns {string} Formatted date string
 */
export const subtractDays = (date, days = 1, format = DATE_FORMAT) => {
    return modifyDate(date, -days, 'day', format);
};

/**
 * Format a date with optional timezone
 * @param {string|Date} date - The date to format
 * @param {string} format - The format string (defaults to 'DD/MM/YYYY HH:mm:ss')
 * @param {string|null} timezone - Optional timezone (uses user's timezone if null)
 * @returns {string} Formatted date string
 */
export const formatDate = (date, format = 'DD/MM/YYYY HH:mm:ss', timezone = null) => {
    const utcDate = dayjs.utc(date);
    return timezone ? utcDate.tz(timezone).format(format) : utcDate.tz().format(format);
};

/**
 * Deprecated: Use formatDate(date, format, timezone) instead
 * @deprecated
 */
export const formatWithFormatAndTimezone = (date, timezone, format = 'DD/MM/YYYY HH:mm:ss') => {
    return formatDate(date, format, timezone);
};

/**
 * Deprecated: Use formatDate(date, format) instead
 * @deprecated
 */
export const formatWithFormat = (date, format = 'DD/MM/YYYY HH:mm:ss') => {
    return formatDate(date, format);
};

/**
 * Format a date (without time) in user's timezone
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted date string
 */
export const formatDateOnly = date => {
    return formatDate(date, DISPLAY_DATE_FORMAT);
};

/**
 * Format a datetime in user's timezone
 * @param {string|Date} date - The date to format
 * @returns {string} Formatted datetime string
 */
export const formatDateTime = date => {
    return formatDate(date, 'DD/MM/YYYY HH:mm:ss');
};

/**
 * Format a datetime with a specific timezone
 * @param {string|Date} date - The date to format
 * @param {string} timezone - The timezone to use for formatting
 * @returns {string} Formatted datetime string
 */
export const formatDateTimeWithTimezone = (date, timezone) => {
    return formatDate(date, 'DD/MM/YYYY HH:mm:ss', timezone);
};

/**
 * Get the user's default timezone
 * @returns {string} The user's timezone
 */
export const getUserTimezone = () => {
    const tzDate = dayjs().tz();
    return tzDate.$x?.$timezone || dayjs.tz.guess();
};

/**
 * Get the timezone offset for a specific timezone and date
 * @param {string|Date} date - The reference date for the offset calculation
 * @param {string} timezone - The timezone to get the offset for
 * @returns {string} The timezone offset (e.g., '+01:00', '-05:00')
 */
export const getTimezoneOffset = (date, timezone) => {
    return dayjs.utc(date).tz(timezone).format('Z');
};

/**
 * Check if a date is before another date
 * @param {string|Date} date1 - First date
 * @param {string|Date} date2 - Second date
 * @returns {boolean} True if date1 is before date2
 */
export const isBefore = (date1, date2) => {
    return dayjs(date1).isBefore(dayjs(date2));
};

/**
 * Check if a date is after another date
 * @param {string|Date} date1 - First date
 * @param {string|Date} date2 - Second date
 * @returns {boolean} True if date1 is after date2
 */
export const isAfter = (date1, date2) => {
    return dayjs(date1).isAfter(dayjs(date2));
};

/**
 * Check if a date is the same as another date
 * @param {string|Date} date1 - First date
 * @param {string|Date} date2 - Second date
 * @param {string} unit - Unit of comparison ('day', 'hour', etc.)
 * @returns {boolean} True if dates are the same
 */
export const isSame = (date1, date2, unit = 'day') => {
    return dayjs(date1).isSame(dayjs(date2), unit);
};

/**
 * Convert a UTC date to a specific timezone
 * @param {string|Date} utcDate - UTC date to convert
 * @param {string} timezone - Target timezone
 * @param {string} format - Output format
 * @returns {string} Formatted date in target timezone
 */
export const convertUtcToTimezone = (utcDate, timezone, format = DATETIME_FORMAT) => {
    if (!utcDate) return '';
    return dayjs.utc(utcDate).tz(timezone).format(format);
};
