/**
 * Application route names
 * Use these constants instead of hardcoded strings to reference routes
 */
export const ROUTES = {
    // Dashboard
    DASHBOARD: 'dashboard',

    // Calendars
    CALENDARS: {
        INDEX: 'calendars.index',
        CREATE: 'calendars.create',
        STORE: 'calendars.store',
        SHOW: 'calendars.show',
        EDIT: 'calendars.edit',
        UPDATE: 'calendars.update',
        DESTROY: 'calendars.destroy',
    },

    // Events
    EVENTS: {
        INDEX: 'events.index',
        CREATE: 'events.create',
        STORE: 'events.store',
        SHOW: 'events.show',
        EDIT: 'events.edit',
        UPDATE: 'events.update',
        DESTROY: 'events.destroy',
        SUGGEST_DURATION: 'events.suggest-duration',
    },

    // Event Templates
    EVENT_TEMPLATES: {
        INDEX: 'event-templates.index',
        CREATE: 'event-templates.create',
        STORE: 'event-templates.store',
        SHOW: 'event-templates.show',
        EDIT: 'event-templates.edit',
        UPDATE: 'event-templates.update',
        DESTROY: 'event-templates.destroy',
    },

    // Users
    USERS: {
        INDEX: 'users.index',
        CREATE: 'users.create',
        STORE: 'users.store',
        SHOW: 'users.show',
        EDIT: 'users.edit',
        UPDATE: 'users.update',
        DESTROY: 'users.destroy',
    },

    // Profile
    PROFILE: {
        EDIT: 'profile.edit',
        UPDATE: 'profile.update',
        DESTROY: 'profile.destroy',
    },
};
