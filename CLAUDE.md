<laravel-boost-guidelines>
=== .ai/isalendar rules ===

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

=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

</laravel-boost-guidelines>
