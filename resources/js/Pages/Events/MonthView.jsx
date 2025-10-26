import { ClockIcon } from '@heroicons/react/20/solid';
import { Link } from '@inertiajs/react';
import dayjs from 'dayjs';
import { useMemo } from 'react';

const MonthView = ({ date, events }) => {
    const dayNames = useMemo(() => {
        const startOfWeek = dayjs().startOf('week');
        return Array.from({ length: 7 }, (_, i) => {
            const day = startOfWeek.add(i, 'day');
            return {
                short: day.format('ddd'),
                full: day.format('dddd'),
            };
        });
    }, []);

    const monthDays = useMemo(() => {
        const startOfMonth = date.startOf('month');
        const endOfMonth = date.endOf('month');
        const startDate = startOfMonth.startOf('week');
        const endDate = endOfMonth.endOf('week');

        const days = [];
        let day = startDate;

        while (day.isBefore(endDate) || day.isSame(endDate, 'day')) {
            const dayEvents = events.filter(event => {
                if (event.allDay) {
                    return event.startsAt.isSame(day, 'day');
                }
                return event.startsAt.isSame(day, 'day');
            });

            days.push({
                date: day.format('YYYY-MM-DD'),
                isCurrentMonth: day.month() === date.month(),
                isToday: day.isSame(dayjs(), 'day'),
                isSelected: false,
                events: dayEvents,
            });

            day = day.add(1, 'day');
        }

        return days;
    }, [date, events]);

    const numberOfWeeks = useMemo(() => Math.ceil(monthDays.length / 7), [monthDays]);

    const upcomingEvents = useMemo(() => {
        const today = dayjs();
        return events
            .filter(event => event.startsAt.isAfter(today))
            .sort((a, b) => a.startsAt.diff(b.startsAt))
            .slice(0, 5);
    }, [events]);

    return (
        <>
            <div className="shadow-sm ring-1 ring-black/5 lg:flex lg:min-h-[650px] lg:flex-auto lg:flex-col dark:shadow-none dark:ring-white/5">
                <div className="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-200 text-center text-xs/6 font-semibold text-gray-700 lg:flex-none dark:border-white/5 dark:bg-white/15 dark:text-gray-300">
                    {dayNames.map((day, index) => (
                        <div key={index} className="flex justify-center bg-white py-2 capitalize dark:bg-gray-900">
                            <span className="sr-only sm:not-sr-only">{day.short}</span>
                        </div>
                    ))}
                </div>
                <div className="flex bg-gray-200 text-xs/6 text-gray-700 lg:flex-auto dark:bg-white/10 dark:text-gray-300">
                    <div
                        className="hidden w-full lg:grid lg:grid-cols-7 lg:gap-px"
                        style={{ gridTemplateRows: `repeat(${numberOfWeeks}, 1fr)` }}
                    >
                        {monthDays.map(day => (
                            <div
                                key={day.date}
                                data-is-today={day.isToday ? '' : undefined}
                                data-is-current-month={day.isCurrentMonth ? '' : undefined}
                                className="relative bg-gray-50 px-3 py-2 text-gray-500 data-is-current-month:bg-white dark:bg-gray-900 dark:text-gray-400 dark:not-data-is-current-month:before:pointer-events-none dark:not-data-is-current-month:before:absolute dark:not-data-is-current-month:before:inset-0 dark:not-data-is-current-month:before:bg-gray-800/50 dark:data-is-current-month:bg-gray-900"
                            >
                                <time
                                    dateTime={day.date}
                                    className="relative not-data-is-current-month:opacity-75 in-data-is-today:flex in-data-is-today:size-6 in-data-is-today:items-center in-data-is-today:justify-center in-data-is-today:rounded-full in-data-is-today:bg-indigo-600 in-data-is-today:font-semibold in-data-is-today:text-white dark:in-data-is-today:bg-indigo-500"
                                >
                                    {day.date.split('-').pop().replace(/^0/, '')}
                                </time>
                                {day.events.length > 0 ? (
                                    <ol className="mt-2">
                                        {day.events.slice(0, 2).map(event => (
                                            <li key={event.id}>
                                                <Link href={event.href} className="group flex">
                                                    <p className="flex-auto truncate font-medium text-gray-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">
                                                        {event.name}
                                                    </p>
                                                    {event.time && (
                                                        <time
                                                            dateTime={event.datetime}
                                                            className="ml-3 hidden flex-none text-gray-500 group-hover:text-indigo-600 xl:block dark:text-gray-400 dark:group-hover:text-indigo-400"
                                                        >
                                                            {event.time}
                                                        </time>
                                                    )}
                                                </Link>
                                            </li>
                                        ))}
                                        {day.events.length > 2 ? (
                                            <li className="text-gray-500 dark:text-gray-400">
                                                + {day.events.length - 2} {__('common.more')}
                                            </li>
                                        ) : null}
                                    </ol>
                                ) : null}
                            </div>
                        ))}
                    </div>
                    <div
                        className="isolate grid w-full grid-cols-7 gap-px lg:hidden"
                        style={{ gridTemplateRows: `repeat(${numberOfWeeks}, 1fr)` }}
                    >
                        {monthDays.map(day => (
                            <button
                                key={day.date}
                                type="button"
                                data-is-today={day.isToday ? '' : undefined}
                                data-is-selected={day.isSelected ? '' : undefined}
                                data-is-current-month={day.isCurrentMonth ? '' : undefined}
                                className="group relative flex h-14 cursor-pointer flex-col px-3 py-2 not-data-is-current-month:bg-gray-50 not-data-is-selected:not-data-is-current-month:not-data-is-today:text-gray-500 hover:bg-gray-100 focus:z-10 data-is-current-month:bg-white not-data-is-selected:data-is-current-month:not-data-is-today:text-gray-900 data-is-current-month:hover:bg-gray-100 data-is-selected:font-semibold data-is-selected:text-white data-is-today:font-semibold not-data-is-selected:data-is-today:text-indigo-600 dark:not-data-is-current-month:bg-gray-900 dark:not-data-is-selected:not-data-is-current-month:not-data-is-today:text-gray-400 dark:not-data-is-current-month:before:pointer-events-none dark:not-data-is-current-month:before:absolute dark:not-data-is-current-month:before:inset-0 dark:not-data-is-current-month:before:bg-gray-800/50 dark:hover:bg-gray-900/50 dark:data-is-current-month:bg-gray-900 dark:not-data-is-selected:data-is-current-month:not-data-is-today:text-white dark:data-is-current-month:hover:bg-gray-900/50 dark:not-data-is-selected:data-is-today:text-indigo-400"
                            >
                                <time
                                    dateTime={day.date}
                                    className="ml-auto group-not-data-is-current-month:opacity-75 in-data-is-selected:flex in-data-is-selected:size-6 in-data-is-selected:items-center in-data-is-selected:justify-center in-data-is-selected:rounded-full in-data-is-selected:not-in-data-is-today:bg-gray-900 in-data-is-selected:in-data-is-today:bg-indigo-600 dark:in-data-is-selected:not-in-data-is-today:bg-white dark:in-data-is-selected:not-in-data-is-today:text-gray-900 dark:in-data-is-selected:in-data-is-today:bg-indigo-500"
                                >
                                    {day.date.split('-').pop().replace(/^0/, '')}
                                </time>
                                <span className="sr-only">
                                    {day.events.length} {__('events.events')}
                                </span>
                                {day.events.length > 0 ? (
                                    <span className="-mx-0.5 mt-auto flex flex-wrap-reverse">
                                        {day.events.map(event => (
                                            <span
                                                key={event.id}
                                                className="mx-0.5 mb-1 size-1.5 rounded-full bg-gray-400 dark:bg-gray-500"
                                            />
                                        ))}
                                    </span>
                                ) : null}
                            </button>
                        ))}
                    </div>
                </div>
            </div>
            <div className="relative px-4 py-10 sm:px-6 lg:hidden dark:after:pointer-events-none dark:after:absolute dark:after:inset-x-0 dark:after:top-0 dark:after:h-px dark:after:bg-white/10">
                <ol className="divide-y divide-gray-100 overflow-hidden rounded-lg bg-white text-sm shadow-sm outline-1 outline-black/5 dark:divide-white/10 dark:bg-gray-800/50 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
                    {upcomingEvents.map(event => (
                        <li
                            key={event.id}
                            className="group flex p-4 pr-6 focus-within:bg-gray-50 hover:bg-gray-50 dark:focus-within:bg-white/5 dark:hover:bg-white/5"
                        >
                            <div className="flex-auto">
                                <p className="font-semibold text-gray-900 dark:text-white">{event.name}</p>
                                <time
                                    dateTime={event.datetime}
                                    className="mt-2 flex items-center text-gray-700 dark:text-gray-300"
                                >
                                    <ClockIcon
                                        aria-hidden="true"
                                        className="mr-2 size-5 text-gray-400 dark:text-gray-500"
                                    />
                                    {event.time || event.startsAt.format('MMM D, YYYY')}
                                </time>
                            </div>
                            <Link
                                href={event.href}
                                className="ml-6 flex-none self-center rounded-md bg-white px-3 py-2 font-semibold text-gray-900 opacity-0 shadow-xs ring-1 ring-gray-300 ring-inset group-hover:opacity-100 hover:ring-gray-400 focus:opacity-100 dark:bg-white/10 dark:text-white dark:shadow-none dark:ring-white/5 dark:hover:bg-white/20 dark:hover:ring-white/5"
                            >
                                {__('common.view')}
                                <span className="sr-only">, {event.name}</span>
                            </Link>
                        </li>
                    ))}
                </ol>
            </div>
        </>
    );
};

export default MonthView;
