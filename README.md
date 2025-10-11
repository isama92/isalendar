# Isalendar
Add events to a shared calendar.

## Functionality
- Providers enabled field is used to enable/disable the creation of new connectors with that provider.
- Connectors enabled fields is used to prevent the synchronisation of that connector.
- Connectors enabled fields is used to prevent the synchronisation of that connector (changes from false to true when user has created at least one calendar).
- Calendars enabled fields is used by the user to disable the sync of a calendar.

## TODO
- BUG: fix pagination in events index table
- BUG: allow end date when all day is set (to have a multiday all day thingy)
- BUG: when saving calendar the event end date is +2h (timezone problem)
- BUG: when saving calendar from google event it's +2h (timezone problem)
  - create from carbon with timezone, and use calendar timezone
- if user doesn't have a calendar model
  - show a permanent message somewhere to tell them to add it
- if user have a calendar model but doesn't have the external_calendar_id
  - show a permanent message somewhere to tell them to select it (show a button in the message "Select Now")
  - show a button in the edit profile to allow the user to select the calendar "Select Calendar"
- check out https://headlessui.com/
- improve the ui of the calendar box in profile edit

- hide events tab on the sidebar if user doesn't have any enabled connectors

- what happens if they reconnect the calendar? show events or create new ones? 
- squash migrations
- add filter by provider in calendars index
- add observer on event creation/edit, push the event immediately
- add a scheduled command that checks if there are not synchronized events (lock when pushing in observer, check if it's locked in scheduled command, in case they run at the same time)
- add tags to actions/batches so to know what is being processed in horizon ui
- soft delete users
- allow the user to disable calendars
- add timezone to events
  - let the user select the timezone when creating event
  - add default timezone for user in profile
  - when fetching or pushing events, also save timezone
- in events form, add the provider name to calendars in the select (pluck key: 'calendar id', value: 'calendar name (provider name)')
- in the events form (and i suppose the calendar ones), change the Cancel button to Back, and go back instead of going to the index
