import EventsByDayOfWeekChart from '@/Components/EventsByDayOfWeekChart';
import StatCard from '@/Components/StatCard';
import { ExclamationTriangleIcon } from '@heroicons/react/20/solid';
import { Link } from '@inertiajs/react';

const Dashboard = ({
    connectorEnabled,
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
            {/* No Calendar Alert */}
            {!connectorEnabled && (
                <div className="mx-auto max-w-3xl bg-white shadow-sm sm:rounded-lg dark:bg-gray-800/50 dark:shadow-none dark:outline dark:-outline-offset-1 dark:outline-white/10">
                    <div className="px-4 py-5 sm:p-6">
                        <div className="sm:flex sm:items-start sm:justify-between">
                            <div>
                                <h3 className="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-white">
                                    <ExclamationTriangleIcon aria-hidden="true" className="size-5 text-yellow-500" />
                                    {__('calendars.no_calendar_title')}
                                </h3>
                                <div className="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                                    <p>{__('calendars.no_calendar_description')}</p>
                                </div>
                            </div>
                            <div className="mt-5 sm:mt-0 sm:ml-6 sm:flex sm:shrink-0 sm:items-center">
                                <Link
                                    href={route('providers.create')}
                                    className="inline-flex items-center rounded-md bg-yellow-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-yellow-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-500 dark:bg-yellow-600 dark:shadow-none dark:hover:bg-yellow-500 dark:focus-visible:outline-yellow-600"
                                >
                                    {__('actions.named.add', { name: __('calendars.calendar') })}
                                </Link>
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
