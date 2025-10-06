# Isalendar
Add events to a shared calendar.


## TODO
- fix pagination in events index table
- allow end date when all day is set (to have a multiday all day thingy)

- queue action to fetch events, every hour
  - in the command loop the users with a calendar and run the fetch and save action
- if user doesn't have a calendar show a permanent message somewhere to tell them to add it
- BUG: when saving calendar the event end date is +2h (timezone)
