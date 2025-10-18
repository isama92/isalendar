import { formatDate, formatDateTime, formatDateTimeWithTimezone, getUserTimezone, subtractDays } from '@/utils/date';
import dayjs from 'dayjs';

const EventDateTime = ({ date, timezone: eventTimezone, allDay, isEndDate = false }) => {
    if (allDay) {
        return isEndDate ? formatDate(subtractDays(date, 1, 'YYYY-MM-DD')) : formatDate(date);
    }

    const userTimezone = getUserTimezone();
    const userTime = formatDateTime(date);

    if (userTimezone === eventTimezone) {
        return userTime;
    }

    const eventTime = formatDateTimeWithTimezone(date, eventTimezone);
    const offset = dayjs.utc(date).tz(eventTimezone).format('Z');

    return (
        <div>
            <div>{userTime}</div>
            <div className="text-xs text-gray-500 dark:text-gray-400" title={eventTimezone}>
                ({offset}) {eventTime}
            </div>
        </div>
    );
};

export default EventDateTime;
