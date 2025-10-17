# Isalendar
Add events to a shared calendar.

## Functionality
- Providers enabled field is used to enable/disable the creation of new connectors with that provider.
- Connectors enabled fields is used to prevent the synchronisation of that connector.
- Connectors enabled fields is used to prevent the synchronisation of that connector (changes from false to true when user has created at least one calendar).
- Calendars enabled fields is used by the user to disable the sync of a calendar.
- Pagination is handled in the frontend, with a high number of events (higher than 100k), it might have performance issues

## BUG
- when selecting datetime from datetime picker in events form date changes immediately
- when changing timezone, the date should also change in the events form
- ... google calendar shows the date in user timezone when creating/updating event, independently from the event timezone
  
## TODO
- add OIDC for authentik
