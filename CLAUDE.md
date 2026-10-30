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

=== .ai/laravel-conventions rules ===

## Test Enforcement (project rule)

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.

## Browser Logs

- Browser logs are also stored in `storage/logs/browser.log` and can be read directly. Only recent logs are useful.

## PHP Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Laravel Conventions

### Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Eagerly loaded records can be limited natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

### Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Application Structure

- This project uses the streamlined file structure introduced in Laravel 11.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files. There is no HTTP Kernel; middleware classes in `app/Http/Middleware/` are registered there.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

## Pest

### Tests

- If you need to verify a feature is working, write or update a Unit / Feature test.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Assertions

- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:

```php
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
```

### Mocking

- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets

- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

```php
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
```

### Browser Testing

- Pest offers browser testing, smoke testing, visual regression testing and test sharding. Use `search-docs` for detailed guidance.
- Browser tests should live in `tests/Browser/`.
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within browser tests.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari) and on different devices and viewports.
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

```php
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in');

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!');

    Notification::assertSent(ResetPassword::class);
});
```

## Inertia + React

### Navigation

- Use `router.visit()` or `<Link>` for navigation instead of traditional links.

```jsx
import { Link } from '@inertiajs/react'

<Link href="/">Home</Link>
```

### Forms

- The recommended way to build forms when using Inertia is with the `<Form>` component. Use `search-docs` with a query of `form component` for guidance.
- Forms can also be built using the `useForm` helper for more programmatic control, or to follow existing conventions. Use `search-docs` with a query of `useForm helper` for guidance.
- `resetOnError`, `resetOnSuccess`, and `setDefaultsOnSuccess` are available on the `<Form>` component. Use `search-docs` with a query of 'form component resetting' for guidance.

```jsx
import { Form } from '@inertiajs/react'

export default () => (
    <Form action="/users" method="post">
        {({ errors, processing, wasSuccessful }) => (
            <>
                <input type="text" name="name" />

                {errors.name && <div>{errors.name}</div>}

                <button type="submit" disabled={processing}>
                    {processing ? 'Creating...' : 'Create User'}
                </button>

                {wasSuccessful && <div>User created successfully!</div>}
            </>
        )}
    </Form>
)
```

## Tailwind CSS

### Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions.
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically.
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing

- When listing items, use gap utilities for spacing, don't use margins.

```html
<div class="flex gap-8">
    <div>Superior</div>
    <div>Michigan</div>
    <div>Erie</div>
</div>
```

### Dark Mode

- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.

### Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- Import Tailwind using a regular CSS `@import "tailwindcss";` statement, not the v3 `@tailwind base/components/utilities` directives.
- Opacity values are still numeric. Use the replacements for removed utilities:

| Deprecated | Replacement |
|------------|-------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |

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
