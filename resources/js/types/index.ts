/**
 * Shared TypeScript type definitions for Isalendar
 *
 * This file contains type definitions for all domain entities and common types
 * used throughout the application.
 */

/**
 * Common timestamp fields for database models
 */
export interface Timestamps {
    created_at: string | null;
    updated_at: string | null;
}

/**
 * Soft delete timestamp field
 */
export interface SoftDeletes {
    deleted_at: string | null;
}

/**
 * User entity
 */
export interface User extends Timestamps, SoftDeletes {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    email_verified_at: string | null;
    settings?: UserSettings;
}

/**
 * User settings
 */
export interface UserSettings extends Timestamps, SoftDeletes {
    id: number;
    user_id: number;
    default_timezone: string;
}

/**
 * Provider entity (e.g., Google Calendar, Microsoft Outlook)
 */
export interface Provider {
    id: number;
    name: string;
    image_path: string;
    enabled: boolean;
    auth_route: string;
}

/**
 * Connector entity - represents a user's OAuth connection to a provider
 */
export interface Connector extends Timestamps, SoftDeletes {
    id: number;
    user_id: number;
    provider_id: number;
    external_id: string;
    enabled: boolean;
    provider?: Provider;
    user?: User;
}

/**
 * Calendar entity
 */
export interface Calendar extends Timestamps, SoftDeletes {
    id: number;
    external_id: string;
    name: string;
    description: string | null;
    enabled: boolean;
    synchronized_at: string | null;
    connectors?: Connector[];
}

/**
 * Event entity
 */
export interface Event extends Timestamps, SoftDeletes {
    id: number;
    calendar_id: number;
    created_by_connector_id: number;
    external_id: string;
    title: string;
    all_day: boolean;
    timezone: string;
    starts_at: string;
    ends_at: string;
    description: string | null;
    marked_for_deletion_at: string | null;
    calendar?: Calendar;
    created_by_connector?: Connector;
}

/**
 * Event template entity
 */
export interface EventTemplate extends Timestamps, SoftDeletes {
    id: number;
    user_id: number;
    calendar_id: number;
    name: string;
    event_title: string;
    event_all_day: boolean;
    event_timezone: string;
    event_description: string | null;
    calendar?: Calendar;
    user?: User;
}

/**
 * Permission entity (for role-based access control)
 */
export interface Permission extends Timestamps {
    id: number;
    name: string;
    guard_name: string;
}

/**
 * Role entity (for role-based access control)
 */
export interface Role extends Timestamps {
    id: number;
    name: string;
    guard_name: string;
    permissions?: Permission[];
}

/**
 * Paginated response wrapper
 */
export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: PaginationLink[];
}

/**
 * Pagination link
 */
export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

/**
 * Chart data for events by day of week
 */
export interface EventsByDayOfWeek {
    [key: string]: number; // day name -> count
}

/**
 * Dashboard statistics
 */
export interface DashboardStats {
    connectorEnabled: boolean;
    totalEvents: number;
    totalCalendars: number;
    eventsToday: number;
    eventsTomorrow: number;
    eventsThisWeek: number;
    eventsNextWeek: number;
    averagePerDayOfWeek: EventsByDayOfWeek;
}

/**
 * Common props for Inertia pages
 */
export interface PageProps {
    auth: {
        user: User;
    };
    flash?: {
        success?: string;
        error?: string;
        warning?: string;
        info?: string;
    };
    errors?: Record<string, string>;
    [key: string]: unknown; // Index signature for Inertia compatibility
}

/**
 * Translation function type
 */
export type TranslationFunction = (key: string, replacements?: Record<string, string | number>) => string;

/**
 * Form data types
 */
export interface EventFormData {
    calendar_id: number | null;
    title: string;
    all_day: boolean;
    timezone: string;
    starts_at: string;
    ends_at: string;
    description: string;
}

export interface CalendarFormData {
    name: string;
    description: string;
    enabled: boolean;
}

export interface UserSettingsFormData {
    default_timezone: string;
}

/**
 * Filter and sort types for tables
 */
export interface TableFilter {
    id: string;
    value: string | number | boolean;
}

export interface TableSort {
    id: string;
    desc: boolean;
}

/**
 * Date range type
 */
export interface DateRange {
    start: string;
    end: string;
}
