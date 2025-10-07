# Isalendar
Add events to a shared calendar.


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
