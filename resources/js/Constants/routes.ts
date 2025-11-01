export const ROUTES = {
    DASHBOARD: 'dashboard',
    CALENDARS: {
        INDEX: 'calendars.index',
        CREATE: 'calendars.create',
        STORE: 'calendars.store',
        SHOW: 'calendars.show',
        EDIT: 'calendars.edit',
        UPDATE: 'calendars.update',
        DESTROY: 'calendars.destroy',
    },
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
    EVENT_TEMPLATES: {
        INDEX: 'event-templates.index',
        CREATE: 'event-templates.create',
        STORE: 'event-templates.store',
        SHOW: 'event-templates.show',
        EDIT: 'event-templates.edit',
        UPDATE: 'event-templates.update',
        DESTROY: 'event-templates.destroy',
    },
    USERS: {
        INDEX: 'users.index',
        CREATE: 'users.create',
        STORE: 'users.store',
        SHOW: 'users.show',
        EDIT: 'users.edit',
        UPDATE: 'users.update',
        DESTROY: 'users.destroy',
    },
    PROFILE: {
        EDIT: 'profile.edit',
        UPDATE: 'profile.update',
        DESTROY: 'profile.destroy',
    },
} as const;

export type RouteName =
    | typeof ROUTES.DASHBOARD
    | (typeof ROUTES.CALENDARS)[keyof typeof ROUTES.CALENDARS]
    | (typeof ROUTES.EVENTS)[keyof typeof ROUTES.EVENTS]
    | (typeof ROUTES.EVENT_TEMPLATES)[keyof typeof ROUTES.EVENT_TEMPLATES]
    | (typeof ROUTES.USERS)[keyof typeof ROUTES.USERS]
    | (typeof ROUTES.PROFILE)[keyof typeof ROUTES.PROFILE];
