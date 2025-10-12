import EventsByDayOfWeekChart from '@/Components/EventsByDayOfWeekChart';
import StatCard from '@/Components/StatCard';
import { ExclamationTriangleIcon } from '@heroicons/react/20/solid';
import { Link, usePage } from '@inertiajs/react';

const Dashboard = ({
    totalEvents,
    totalCalendars,
    eventsToday,
    eventsTomorrow,
    eventsThisWeek,
    eventsNextWeek,
    averagePerDayOfWeek,
}) => {
    const { connectorEnabled } = usePage().props;

    return (
        <div className="space-y-6 px-3">
            {/* No Calendar Alert */}
            {!connectorEnabled && (
                <div className="flex justify-center px-4 pt-3 sm:px-6 lg:px-8">
                    <div className="w-full max-w-4xl border-l-4 border-yellow-400 bg-yellow-50 p-4 dark:border-yellow-500 dark:bg-yellow-500/10">
                        <div className="flex">
                            <div className="shrink-0">
                                <ExclamationTriangleIcon
                                    aria-hidden="true"
                                    className="size-5 text-yellow-400 dark:text-yellow-500"
                                />
                            </div>
                            <div className="ml-3">
                                <p className="text-sm text-yellow-700 dark:text-yellow-300">
                                    {__('calendars.no_calendar_yet')}{' '}
                                    <Link
                                        href={route('providers.create')}
                                        className="font-medium text-yellow-700 underline hover:text-yellow-600 dark:text-yellow-300 dark:hover:text-yellow-200"
                                    >
                                        {__('calendars.create_calendar_connection')}
                                    </Link>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            )}

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
