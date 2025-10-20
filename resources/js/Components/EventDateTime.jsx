import { formatDate, formatDateTime, formatDateTimeWithTimezone, getUserTimezone, subtractDays } from '@/utils/date';
import dayjs from 'dayjs';

const EventDateTime = ({ date, timezone: eventTimezone, allDay, isEndDate = false }) => {
    if (allDay) {
        return isEndDate ? formatDate(subtractDays(date, 1, 'YYYY-MM-DD')) : formatDate(date);
    }

    const userTimezone = getUserTimezone();
    const userTime = formatDateTime(date);

    const userOffset = dayjs.utc(date).tz(userTimezone).format('Z');
    const eventOffset = dayjs.utc(date).tz(eventTimezone).format('Z');

    if (userOffset === eventOffset) {
        return userTime;
    }

    const eventTime = formatDateTimeWithTimezone(date, eventTimezone);

    return (
        <div>
            <div>{userTime}</div>
            <div className="text-xs text-gray-500 dark:text-gray-400" title={eventTimezone}>
                ({eventOffset}) {eventTime}
            </div>
        </div>
    );
};

export default EventDateTime;
