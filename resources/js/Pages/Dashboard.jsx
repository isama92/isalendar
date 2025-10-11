import EventsByDayOfWeekChart from '@/Components/EventsByDayOfWeekChart';
import StatCard from '@/Components/StatCard';

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
        <div className="space-y-6 px-3">
            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h2 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{__('dashboard.dashboard')}</h2>
                <p className="mt-2 text-gray-600 dark:text-gray-300">{__('dashboard.welcome_message')}</p>
            </div>

            <dl className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <StatCard label={__('dashboard.total_events')} value={totalEvents} className="order-1 lg:order-1" />
                <StatCard label={__('dashboard.events_today')} value={eventsToday} className="order-3 lg:order-2" />
                <StatCard
                    label={__('dashboard.events_tomorrow')}
                    value={eventsTomorrow}
                    className="order-4 lg:order-3"
                />
                <StatCard
                    label={__('dashboard.total_calendars')}
                    value={totalCalendars}
                    className="order-2 lg:order-4"
                />
                <StatCard
                    label={__('dashboard.events_this_week')}
                    value={eventsThisWeek}
                    className="order-5 lg:order-5"
                />
                <StatCard
                    label={__('dashboard.events_next_week')}
                    value={eventsNextWeek}
                    className="order-6 lg:order-6"
                />
            </dl>

            <EventsByDayOfWeekChart averagePerDayOfWeek={averagePerDayOfWeek} />
        </div>
    );
};

export default Dashboard;
