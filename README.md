# Isalendar
Add events to a shared calendar.

## Authentication

### OIDC Configuration
Isalendar supports generic OIDC (OpenID Connect) authentication, compatible with any OIDC-compliant provider such as Authentik, Authelia, Keycloak, etc.

To enable OIDC authentication, configure the following environment variables in your `.env` file:

```env
OIDC_ENABLED=true
OIDC_CLIENT_ID=your_client_id
OIDC_CLIENT_SECRET=your_client_secret
OIDC_AUTHORIZE_URL=https://your-oidc-provider.com/oauth/authorize
OIDC_TOKEN_URL=https://your-oidc-provider.com/oauth/token
OIDC_USERINFO_URL=https://your-oidc-provider.com/oauth/userinfo
OIDC_SCOPES="openid profile email"
OIDC_BUTTON_LABEL="Login with OIDC"
```

#### Example: Authentik
```env
OIDC_ENABLED=true
OIDC_CLIENT_ID=your_authentik_client_id
OIDC_CLIENT_SECRET=your_authentik_client_secret
OIDC_AUTHORIZE_URL=https://authentik.example.com/application/o/authorize/
OIDC_TOKEN_URL=https://authentik.example.com/application/o/token/
OIDC_USERINFO_URL=https://authentik.example.com/application/o/userinfo/
OIDC_BUTTON_LABEL="Login with Authentik"
```

#### Example: Authelia
```env
OIDC_ENABLED=true
OIDC_CLIENT_ID=your_authelia_client_id
OIDC_CLIENT_SECRET=your_authelia_client_secret
OIDC_AUTHORIZE_URL=https://authelia.example.com/api/oidc/authorization
OIDC_TOKEN_URL=https://authelia.example.com/api/oidc/token
OIDC_USERINFO_URL=https://authelia.example.com/api/oidc/userinfo
OIDC_BUTTON_LABEL="Login with Authelia"
```

**Notes:**
- When `OIDC_ENABLED` is `true`, an OIDC login button will appear on the login page.
- The OIDC provider must return the user's email in the `email` claim.
- Users are matched by email - if a user with the same email exists, they will be logged in and their name updated from OIDC.
- If no user exists with the email, a new user will be created.

## Functionality
- Providers enabled field is used to enable/disable the creation of new connectors with that provider.
- Connectors enabled fields is used to prevent the synchronisation of that connector.
- Connectors enabled fields is used to prevent the synchronisation of that connector (changes from false to true when user has created at least one calendar).
- Calendars enabled fields is used by the user to disable the sync of a calendar.
- Pagination is handled in the frontend, with a high number of events (higher than 100k), it might have performance issues

## BUG
- when selecting datetime from datetime picker in events form date changes immediately
- when changing timezone, the date should also change in the events form
- ... google calendar shows the date in user timezone when creating/updating event, independently of the event timezone

## TODO
- stop using google/apiclient library
  - use saloon to make requests
  - implement rate limits retries
  - implement pagination

- improves events and calendar translations by moving things in common and using actions named translations
