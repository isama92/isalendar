import dayjs, { type ManipulateType, type OpUnitType } from 'dayjs';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';

// Extend dayjs with plugins
dayjs.extend(utc);
dayjs.extend(timezone);

/**
 * Date format constants
 */
export const DATE_FORMAT = 'YYYY-MM-DD';
export const DATETIME_FORMAT = 'YYYY-MM-DD HH:mm:ss';
export const DISPLAY_DATE_FORMAT = 'DD/MM/YYYY';
export const DISPLAY_DATETIME_FORMAT = 'DD/MM/YYYY HH:mm';

type DateInput = string | Date | dayjs.Dayjs;

/**
 * Modify a date by adding or subtracting time units
 * @param date - The date to modify
 * @param amount - Amount to add (positive) or subtract (negative)
 * @param unit - Unit of time ('day', 'hour', 'minute', etc.)
 * @param format - The format string (defaults to DATE_FORMAT)
 * @returns Formatted date string
 */
export const modifyDate = (
    date: DateInput,
    amount: number,
    unit: ManipulateType = 'day',
    format: string = DATE_FORMAT
): string => {
    return dayjs(date).add(amount, unit).format(format);
};

/**
 * Add days to a date (convenience wrapper for modifyDate)
 * @param date - The date to add to
 * @param days - Number of days to add
 * @param format - The format string (defaults to DATE_FORMAT)
 * @returns Formatted date string
 */
export const addDays = (date: DateInput, days: number = 1, format: string = DATE_FORMAT): string => {
    return modifyDate(date, days, 'day', format);
};

/**
 * Subtract days from a date (convenience wrapper for modifyDate)
 * @param date - The date to subtract from
 * @param days - Number of days to subtract
 * @param format - The format string (defaults to DATE_FORMAT)
 * @returns Formatted date string
 */
export const subtractDays = (date: DateInput, days: number = 1, format: string = DATE_FORMAT): string => {
    return modifyDate(date, -days, 'day', format);
};

/**
 * Format a date with optional timezone
 * @param date - The date to format
 * @param format - The format string (defaults to 'DD/MM/YYYY HH:mm:ss')
 * @param timezone - Optional timezone (uses user's timezone if null)
 * @returns Formatted date string
 */
export const formatDate = (
    date: DateInput,
    format: string = 'DD/MM/YYYY HH:mm:ss',
    timezone: string | null = null
): string => {
    const utcDate = dayjs.utc(date);
    return timezone ? utcDate.tz(timezone).format(format) : utcDate.tz().format(format);
};

/**
 * Deprecated: Use formatDate(date, format, timezone) instead
 * @deprecated
 */
export const formatWithFormatAndTimezone = (
    date: DateInput,
    timezone: string,
    format: string = 'DD/MM/YYYY HH:mm:ss'
): string => {
    return formatDate(date, format, timezone);
};

/**
 * Deprecated: Use formatDate(date, format) instead
 * @deprecated
 */
export const formatWithFormat = (date: DateInput, format: string = 'DD/MM/YYYY HH:mm:ss'): string => {
    return formatDate(date, format);
};

/**
 * Format a date (without time) in user's timezone
 * @param date - The date to format
 * @returns Formatted date string
 */
export const formatDateOnly = (date: DateInput): string => {
    return formatDate(date, DISPLAY_DATE_FORMAT);
};

/**
 * Format a datetime in user's timezone
 * @param date - The date to format
 * @returns Formatted datetime string
 */
export const formatDateTime = (date: DateInput): string => {
    return formatDate(date, 'DD/MM/YYYY HH:mm:ss');
};

/**
 * Format a datetime with a specific timezone
 * @param date - The date to format
 * @param timezone - The timezone to use for formatting
 * @returns Formatted datetime string
 */
export const formatDateTimeWithTimezone = (date: DateInput, timezone: string): string => {
    return formatDate(date, 'DD/MM/YYYY HH:mm:ss', timezone);
};

/**
 * Get the user's default timezone
 * @returns The user's timezone
 */
export const getUserTimezone = (): string => {
    const tzDate = dayjs().tz();
    return (tzDate as any).$x?.$timezone || dayjs.tz.guess();
};

/**
 * Get the timezone offset for a specific timezone and date
 * @param date - The reference date for the offset calculation
 * @param timezone - The timezone to get the offset for
 * @returns The timezone offset (e.g., '+01:00', '-05:00')
 */
export const getTimezoneOffset = (date: DateInput, timezone: string): string => {
    return dayjs.utc(date).tz(timezone).format('Z');
};

/**
 * Check if a date is before another date
 * @param date1 - First date
 * @param date2 - Second date
 * @returns True if date1 is before date2
 */
export const isBefore = (date1: DateInput, date2: DateInput): boolean => {
    return dayjs(date1).isBefore(dayjs(date2));
};

/**
 * Check if a date is after another date
 * @param date1 - First date
 * @param date2 - Second date
 * @returns True if date1 is after date2
 */
export const isAfter = (date1: DateInput, date2: DateInput): boolean => {
    return dayjs(date1).isAfter(dayjs(date2));
};

/**
 * Check if a date is the same as another date
 * @param date1 - First date
 * @param date2 - Second date
 * @param unit - Unit of comparison ('day', 'hour', etc.)
 * @returns True if dates are the same
 */
export const isSame = (date1: DateInput, date2: DateInput, unit: OpUnitType = 'day'): boolean => {
    return dayjs(date1).isSame(dayjs(date2), unit);
};

/**
 * Convert a UTC date to a specific timezone
 * @param utcDate - UTC date to convert
 * @param timezone - Target timezone
 * @param format - Output format
 * @returns Formatted date in target timezone
 */
export const convertUtcToTimezone = (
    utcDate: DateInput,
    timezone: string,
    format: string = DATETIME_FORMAT
): string => {
    if (!utcDate) return '';
    return dayjs.utc(utcDate).tz(timezone).format(format);
};
