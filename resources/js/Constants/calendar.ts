export const CALENDAR_VIEWS = {
    MONTH: 'month',
    WEEK: 'week',
    DAY: 'day',
} as const;

export type CalendarView = (typeof CALENDAR_VIEWS)[keyof typeof CALENDAR_VIEWS];

/**
 * Default event durations in minutes
 */
export const DEFAULT_DURATIONS = {
    SHORT_EVENT: 30,
    STANDARD_EVENT: 60,
    LONG_EVENT: 120,
} as const;

export type DefaultDuration = (typeof DEFAULT_DURATIONS)[keyof typeof DEFAULT_DURATIONS];
