# Isalendar - Calendar Synchronization Application

Isalendar is a self-hosted calendar application that allows users to synchronize and manage events from multiple external calendar providers (like Google Calendar and Microsoft Outlook) in a unified interface.

## Features

- **Multi-Provider Sync**: Connect Google Calendar, Microsoft Outlook, and more
- **Unified Interface**: Manage all your calendars in one place
- **Real-time Updates**: Laravel Horizon for queue processing
- **Bilingual**: Full support for English and Italian
- **OIDC Authentication**: Support for Authentik, Authelia, Keycloak, and other OIDC providers
- **Self-Hosted**: Complete control of your calendar data

## Quick Start with Docker

The easiest way to deploy Isalendar is using the pre-built Docker image.

### Installation

1. **Download the configuration files**

```bash
# Create a directory for Isalendar
mkdir isalendar && cd isalendar

# Download compose.production.yml
curl -O https://raw.githubusercontent.com/isama92/isalendar/main/compose.production.yml

# Download environment template
curl -O https://raw.githubusercontent.com/isama92/isalendar/main/.env.production.example
mv .env.production.example .env
```

2. **Configure your environment**

Edit the `.env` file and set the required values:

```bash
# Generate a Laravel application key
# You can use: docker run --rm --entrypoint php ghcr.io/isama92/isalendar:latest artisan key:generate --show
APP_KEY=base64:YOUR_GENERATED_KEY_HERE

# Set your domain
APP_URL=https://your-domain.com

# Database credentials (use strong passwords!)
DB_DATABASE=isalendar
DB_USERNAME=isalendar_user
DB_PASSWORD=your_strong_password_here
DB_ROOT_PASSWORD=your_strong_root_password_here

# Redis password
REDIS_PASSWORD=your_redis_password_here

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-mail-password

# Google OAuth (required for Google Calendar sync)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

# Optional: IP Geolocation (to get user default timezone)
IPGEOLOCATION_KEY=your-api-key
```

3. **Start the application**

```bash
# Pull images and start all services
docker compose pull
docker compose up -d

# Check that everything is running
docker compose ps
```

That's it! Your application should now be running at `http://localhost` (or your configured domain).

The first time the container starts, it will automatically:
- Wait for the database to be ready
- Run database migrations
- Optimize the application for production

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
- Tested with Authentik

## Application Notes

### Functionality
- Providers `enabled` field is used to enable/disable the creation of new connectors with that provider
- Connectors `enabled` field is used to prevent the synchronization of that connector (changes from false to true when user has created at least one calendar)
- Calendars `enabled` field is used by the user to disable the sync of a calendar
- Pagination is handled in the frontend; with a high number of events (higher than 100k), it might have performance issues

## TODOS
- Stop using `google/apiclient` library
  - Use Saloon to make requests
  - Implement rate limit retries
  - Implement pagination
- set correct created by and last updated by when importing connector from remote
- ends_at should not be nullable in db

## License

MIT License
