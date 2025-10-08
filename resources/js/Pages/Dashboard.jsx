import EventsByDayOfWeekChart from '../Components/EventsByDayOfWeekChart';
import StatCard from '../Components/StatCard';

const Dashboard = ({
    totalEvents,
    totalCalendars,
    eventsToday,
    eventsTomorrow,
    eventsThisWeek,
    eventsNextWeek,
    averagePerDayOfWeek,
}) => {
    return (
        <div className="space-y-6">
            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h2 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{__('dashboard.dashboard')}</h2>
                <p className="mt-2 text-gray-600 dark:text-gray-300">{__('dashboard.welcome_message')}</p>
            </div>

            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <StatCard label={__('dashboard.total_events')} value={totalEvents} />

                <StatCard label={__('dashboard.events_today')} value={eventsToday} />

                <StatCard label={__('dashboard.events_tomorrow')} value={eventsTomorrow} />

                <StatCard label={__('dashboard.total_calendars')} value={totalCalendars} />

                <StatCard label={__('dashboard.events_this_week')} value={eventsThisWeek} />

                <StatCard label={__('dashboard.events_next_week')} value={eventsNextWeek} />
            </div>

            <EventsByDayOfWeekChart averagePerDayOfWeek={averagePerDayOfWeek} />
        </div>
    );
};

export default Dashboard;
