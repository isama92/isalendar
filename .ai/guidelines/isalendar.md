## Application Domain
**Isalendar** is a calendar application that allows users to synchronize and manage events from multiple external calendar providers (like Google Calendar and Microsoft Outlook) in a unified interface.

### Core Entities

#### Users
Authenticated users who can connect to calendar providers and manage their events. Users have standard authentication fields (name, email, password) and support roles and permissions through Laravel's permission system.

#### Providers
External calendar services (e.g., Google Calendar, Microsoft Outlook) that users can connect to. Each provider has:
- `name` - Provider name (e.g., "Google", "Microsoft")
- `image_path` - Logo/icon for the provider
- `enabled` - Whether the provider is currently available for connections
- `access_object` - The class/service used to interact with the provider's API
- `auth_route` - The route used to initiate OAuth authentication

#### Connectors
The authentication bridge between a user and a provider. Each connector represents one user's OAuth connection to a specific provider and contains:
- `user_id` - The user who owns this connection
- `provider_id` - The provider being connected to
- `external_id` - The user's ID on the external provider
- `access_details` - Encrypted OAuth tokens and credentials (JSON)
- `enabled` - Whether this connector is active
- Soft deletes supported
- Connectors cannot be soft deleted

**Relationship**: A connector belongs to both a user and a provider. One user can have multiple connectors (one per provider), and one provider can have many connectors (one per user).

#### Calendars
Individual calendars synced from external providers. A calendar can be shared across multiple connectors (many-to-many relationship via `calendar_connector` pivot table). Properties include:
- `external_id` - Unique identifier from the external provider
- `name` - Calendar display name
- `description` - Calendar description (nullable)
- `enabled` - Whether the calendar is actively syncing
- Soft deletes supported
- Indexed on `name` for search performance
- Calendars cannot be force deleted

**Relationship**: Calendars have a many-to-many relationship with connectors, allowing shared calendars across multiple users.

#### Events
Calendar events belonging to a specific calendar. Properties include:
- `calendar_id` - Parent calendar
- `external_id` - Unique identifier from the external provider
- `title` - Event title (indexed for search)
- `starts_at` - Start date/time
- `ends_at` - End date/time
- `all_day` - Boolean flag for all-day events
- `description` - Event description (nullable)
- Soft deletes supported
- Events cannot be force deleted

**Relationship**: Events belong to a calendar.

### Domain Flow
1. User authenticates with the application
2. User selects a provider and initiates OAuth connection
3. A connector is created with encrypted access tokens
4. User selects which calendars to sync from that provider
5. Calendars and their events are synchronized and stored locally
6. Users can view and manage events across all connected calendars

### Additional Features
- **Soft Deletes**: Users, connectors, calendars, and events support soft deletion for data recovery
- **Permissions System**: Role-based access control using Laravel's permission package
- **Bilingual Support**: Full internationalization in English and Italian
- **Background Jobs**: Uses Laravel Horizon for queue management and calendar synchronization

## Local Development (lerd)
- This application runs locally on lerd at https://isalendar.test. Laravel Sail is NOT used locally; do not run `./vendor/bin/sail`.
- Run commands directly, lerd provides them on PATH: `php artisan migrate`, `composer install`, `npm install`, `node --version`.
- MySQL (`lerd-mysql`) and Redis (`lerd-redis`) are lerd services configured in `.env`.
- The lerd `php` wrapper runs inside a container: it does not pass exported shell env vars through (put values in `.env`), and `127.0.0.1` inside it is not the host.
- lerd's PHP CLI has `memory_limit=128M`. Some tests (e.g. `SaveUserPhotoTest`) need more: run the suite with `php -d memory_limit=-1 vendor/bin/pest` if it runs out of memory.
- **NPM Development Server**: Vite (`npm run dev`) is expected to be running. DO NOT run `npm run build` as changes are automatically hot-reloaded by Vite.

## Code Formatting
- To format/lint PHP files, use: `composer lint`
- This will run Laravel Pint to ensure code matches the project's style.
- You must run `composer lint` before finalizing changes.


## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Translations
- Translation files are organized by domain in the `lang/{locale}/` directory (e.g., `lang/en/`, `lang/it/`).
- Use the `__()` helper function in JSX/JavaScript files to access translations: `__('file.key')`.
- Translation file organization:
  - `common.php` - Common UI elements used across the application (cancel, save, delete, edit, view, create, update, back, actions, yes, no, pagination, etc.)
  - Domain-specific files (e.g., `events.php`, `profile.php`, `auth.php`, `dashboard.php`) - Translations specific to that domain/feature.
- When adding new translations:
  - Always add the translation to both English (`lang/en/`) and Italian (`lang/it/`) files.
  - Place translations in the most appropriate file - prefer domain-specific files over `common.php` when the translation is specific to a feature.
  - Keep translation keys descriptive and follow existing naming conventions in the file.
