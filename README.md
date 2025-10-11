# Isalendar
Add events to a shared calendar.

## Functionality
- Providers enabled field is used to enable/disable the creation of new connectors with that provider.
- Connectors enabled fields is used to prevent the synchronisation of that connector.
- Connectors enabled fields is used to prevent the synchronisation of that connector (changes from false to true when user has created at least one calendar).
- Calendars enabled fields is used by the user to disable the sync of a calendar.

## TODO
- BUG: fix pagination in events and calendars index table
- if user doesn't have a calendar model
  - show a permanent message somewhere to tell them to add it
- if user have a calendar model but doesn't have the external_calendar_id
  - show a permanent message somewhere to tell them to select it (show a button in the message "Select Now")

- add observer on event creation/edit, push the event immediately
- add a scheduled command that checks if there are not synchronized events (lock when pushing in observer, check if it's locked in scheduled command, in case they run at the same time)

- add timezone to events
  - let the user select the timezone when creating event
  - when fetching or pushing events, also save timezone
