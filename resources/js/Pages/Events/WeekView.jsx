import { Link } from '@inertiajs/react';
import clsx from 'clsx';
import dayjs from 'dayjs';
import { useEffect, useMemo, useRef } from 'react';

const WeekView = ({ date, events }) => {
    const hours = Array.from({ length: 24 }, (_, i) => i);
    const firstEventRef = useRef(null);
    const timeslotContainerRef = useRef(null);

    // Color palette for events
    const eventColors = [
        {
            bg: 'bg-blue-50',
            bgHover: 'hover:bg-blue-100',
            text: 'text-blue-700',
            textSecondary: 'text-blue-500',
            textHover: 'group-hover:text-blue-700',
            darkBg: 'dark:bg-blue-600/15',
            darkBgHover: 'dark:hover:bg-blue-600/20',
            darkText: 'dark:text-blue-300',
            darkTextSecondary: 'dark:text-blue-400',
            darkTextHover: 'dark:group-hover:text-blue-300',
        },
        {
            bg: 'bg-emerald-50',
            bgHover: 'hover:bg-emerald-100',
            text: 'text-emerald-700',
            textSecondary: 'text-emerald-500',
            textHover: 'group-hover:text-emerald-700',
            darkBg: 'dark:bg-emerald-600/15',
            darkBgHover: 'dark:hover:bg-emerald-600/20',
            darkText: 'dark:text-emerald-300',
            darkTextSecondary: 'dark:text-emerald-400',
            darkTextHover: 'dark:group-hover:text-emerald-300',
        },
        {
            bg: 'bg-amber-50',
            bgHover: 'hover:bg-amber-100',
            text: 'text-amber-700',
            textSecondary: 'text-amber-500',
            textHover: 'group-hover:text-amber-700',
            darkBg: 'dark:bg-amber-600/15',
            darkBgHover: 'dark:hover:bg-amber-600/20',
            darkText: 'dark:text-amber-300',
            darkTextSecondary: 'dark:text-amber-400',
            darkTextHover: 'dark:group-hover:text-amber-300',
        },
        {
            bg: 'bg-rose-50',
            bgHover: 'hover:bg-rose-100',
            text: 'text-rose-700',
            textSecondary: 'text-rose-500',
            textHover: 'group-hover:text-rose-700',
            darkBg: 'dark:bg-rose-600/15',
            darkBgHover: 'dark:hover:bg-rose-600/20',
            darkText: 'dark:text-rose-300',
            darkTextSecondary: 'dark:text-rose-400',
            darkTextHover: 'dark:group-hover:text-rose-300',
        },
        {
            bg: 'bg-violet-50',
            bgHover: 'hover:bg-violet-100',
            text: 'text-violet-700',
            textSecondary: 'text-violet-500',
            textHover: 'group-hover:text-violet-700',
            darkBg: 'dark:bg-violet-600/15',
            darkBgHover: 'dark:hover:bg-violet-600/20',
            darkText: 'dark:text-violet-300',
            darkTextSecondary: 'dark:text-violet-400',
            darkTextHover: 'dark:group-hover:text-violet-300',
        },
        {
            bg: 'bg-cyan-50',
            bgHover: 'hover:bg-cyan-100',
            text: 'text-cyan-700',
            textSecondary: 'text-cyan-500',
            textHover: 'group-hover:text-cyan-700',
            darkBg: 'dark:bg-cyan-600/15',
            darkBgHover: 'dark:hover:bg-cyan-600/20',
            darkText: 'dark:text-cyan-300',
            darkTextSecondary: 'dark:text-cyan-400',
            darkTextHover: 'dark:group-hover:text-cyan-300',
        },
    ];

    const weekDays = useMemo(() => {
        const startOfWeek = date.startOf('week');
        const days = [];

        for (let i = 0; i < 7; i++) {
            const day = startOfWeek.add(i, 'day');
            days.push({
                date: day.format('YYYY-MM-DD'),
                dayName: day.format('ddd'),
                dayNumber: day.format('D'),
                isToday: day.isSame(dayjs(), 'day'),
            });
        }

        return days;
    }, [date]);

    const { allDayEvents, timedEvents } = useMemo(() => {
        const startOfWeek = date.startOf('week');
        const endOfWeek = date.endOf('week');

        const filteredEvents = events.filter(event => {
            return event.startsAt.isBetween(startOfWeek, endOfWeek, 'day', '[]');
        });

        // Separate all-day and timed events
        const allDayEventsRaw = [];
        const timedEventsRaw = [];

        filteredEvents.forEach(event => {
            // Calculate which day of the week by comparing date strings
            const eventDateStr = event.startsAt.format('YYYY-MM-DD');
            const dayIndex = weekDays.findIndex(day => day.date === eventDateStr);
            const gridColumn = dayIndex >= 0 ? dayIndex + 1 : 1;

            if (event.allDay) {
                allDayEventsRaw.push({
                    ...event,
                    gridColumn,
                    dayIndex,
                });
            } else {
                const hour = event.startsAt.hour();
                const minute = event.startsAt.minute();
                const duration = event.endsAt.diff(event.startsAt, 'minute');

                // Grid has 288 rows for 24 hours = 12 rows per hour (5-minute intervals)
                // Row 1 starts at 00:00
                const gridRowStart = 1 + hour * 12 + Math.floor(minute / 5);
                const gridRowSpan = Math.max(1, Math.ceil(duration / 5));

                timedEventsRaw.push({
                    ...event,
                    gridColumn,
                    gridRowStart,
                    gridRowSpan,
                });
            }
        });

        // Process all-day events per day
        const processedAllDayEvents = [];
        for (let col = 0; col < 7; col++) {
            const dayEvents = allDayEventsRaw.filter(e => e.dayIndex === col);
            dayEvents.forEach((event, idx) => {
                processedAllDayEvents.push({
                    ...event,
                    rowIndex: idx,
                });
            });
        }

        // Detect overlapping timed events and calculate layout
        const processedTimedEvents = [];

        for (let col = 1; col <= 7; col++) {
            const columnEvents = timedEventsRaw.filter(e => e.gridColumn === col);

            // Group overlapping events
            const groups = [];
            columnEvents.forEach(event => {
                const eventEnd = event.gridRowStart + event.gridRowSpan;

                // Find a group this event overlaps with
                let foundGroup = groups.find(group =>
                    group.some(e => {
                        const eEnd = e.gridRowStart + e.gridRowSpan;
                        return !(eventEnd <= e.gridRowStart || event.gridRowStart >= eEnd);
                    })
                );

                if (foundGroup) {
                    foundGroup.push(event);
                } else {
                    groups.push([event]);
                }
            });

            // Process each group - stack overlapping events with z-index based on duration
            groups.forEach(group => {
                if (group.length === 1) {
                    processedTimedEvents.push({
                        ...group[0],
                        zIndex: 10,
                        leftOffset: 0,
                        widthPercent: 100,
                        hasBorder: false,
                        colorIndex: 0,
                    });
                } else {
                    // Sort by duration (longest first) so they get lower z-index
                    const sorted = [...group].sort((a, b) => b.gridRowSpan - a.gridRowSpan);

                    // Show max 5 events visually, rest get aggregated
                    const maxVisible = 5;
                    const visibleEvents = sorted.slice(0, maxVisible);

                    visibleEvents.forEach((event, idx) => {
                        // Lowest z-index (longest event) is full width, others progressively narrower
                        // Cap minimum width at 30%
                        const widthPercent = idx === 0 ? 100 : Math.max(30, 100 - idx * 15);
                        processedTimedEvents.push({
                            ...event,
                            zIndex: 10 + idx, // Longer events (first in sorted array) get lower z-index
                            leftOffset: idx * 6, // Stagger by 6px to show overlap
                            widthPercent,
                            hasBorder: idx > 0, // Add border to all events except the lowest z-index
                            colorIndex: idx, // Use idx for color variation
                            overflowCount:
                                idx === maxVisible - 1 && sorted.length > maxVisible ? sorted.length - maxVisible : 0,
                        });
                    });
                }
            });
        }

        return {
            allDayEvents: processedAllDayEvents,
            timedEvents: processedTimedEvents,
        };
    }, [date, events, weekDays]);

    // Calculate max all-day events in any single day
    const maxAllDayRows = useMemo(() => {
        const rowsPerDay = Array(7).fill(0);
        allDayEvents.forEach(event => {
            rowsPerDay[event.dayIndex] = Math.max(rowsPerDay[event.dayIndex], event.rowIndex + 1);
        });
        return Math.max(...rowsPerDay, 0);
    }, [allDayEvents]);

    // Find the first event to scroll to
    const firstTimedEvent = useMemo(() => {
        if (timedEvents.length === 0) return null;
        return timedEvents.reduce((earliest, event) =>
            !earliest || event.gridRowStart < earliest.gridRowStart ? event : earliest
        );
    }, [timedEvents]);

    // Auto-scroll to first event
    useEffect(() => {
        if (firstEventRef.current && timeslotContainerRef.current) {
            // Use setTimeout to ensure the DOM is fully rendered
            const timeoutId = setTimeout(() => {
                if (firstEventRef.current && timeslotContainerRef.current) {
                    const eventTop = firstEventRef.current.offsetTop;
                    // Scroll to event position minus 70px for context above
                    timeslotContainerRef.current.scrollTop = eventTop - 350;
                }
            }, 100);

            return () => clearTimeout(timeoutId);
        }
    }, [firstTimedEvent]);

    return (
        <div className="isolate flex flex-auto flex-col bg-white dark:bg-gray-900">
            <div className="z-30 flex-none bg-white shadow-sm ring-1 ring-black/5 dark:bg-gray-900 dark:shadow-none dark:ring-white/20">
                <div
                    style={{ width: '165%' }}
                    className="flex max-w-full flex-none flex-col sm:max-w-none sm:pr-8 md:max-w-full"
                >
                    <div className="grid grid-cols-7 text-sm/6 text-gray-500 sm:hidden dark:text-gray-400">
                        {weekDays.map(day => (
                            <button
                                key={day.date}
                                type="button"
                                className="flex cursor-pointer flex-col items-center pt-2 pb-3"
                            >
                                {day.dayName.charAt(0)}{' '}
                                <span
                                    className={`mt-1 flex size-8 items-center justify-center font-semibold ${
                                        day.isToday
                                            ? 'rounded-full bg-indigo-600 text-white dark:bg-indigo-500'
                                            : 'text-gray-900 dark:text-white'
                                    }`}
                                >
                                    {day.dayNumber}
                                </span>
                            </button>
                        ))}
                    </div>

                    <div className="-mr-px hidden grid-cols-7 divide-x divide-gray-100 border-r border-gray-100 text-sm/6 text-gray-500 sm:grid dark:divide-white/10 dark:border-white/10 dark:text-gray-400">
                        <div className="col-end-1 w-14" />
                        {weekDays.map(day => (
                            <div key={day.date} className="flex items-center justify-center py-3">
                                <span className={clsx('capitalize', day.isToday ? 'flex items-baseline' : '')}>
                                    {day.dayName}{' '}
                                    <span
                                        className={
                                            day.isToday
                                                ? 'ml-1.5 flex size-8 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white dark:bg-indigo-500'
                                                : 'items-center justify-center font-semibold text-gray-900 dark:text-white'
                                        }
                                    >
                                        {day.dayNumber}
                                    </span>
                                </span>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            <div style={{ width: '165%' }} className="flex max-w-full flex-none flex-col sm:max-w-none md:max-w-full">
                {/* All-day events section */}
                {maxAllDayRows > 0 && (
                    <div className="z-20 flex border-b border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">
                        <div className="sticky left-0 z-10 w-14 flex-none bg-gray-50 dark:bg-gray-800/50" />
                        <div className="grid flex-auto grid-cols-7 gap-px bg-gray-200 dark:bg-white/5">
                            {weekDays.map((day, dayIndex) => {
                                const dayAllDayEvents = allDayEvents.filter(e => e.dayIndex === dayIndex);
                                return (
                                    <div key={day.date} className="bg-white dark:bg-gray-900">
                                        <div
                                            style={{ minHeight: `${maxAllDayRows * 2}rem` }}
                                            className="flex flex-col gap-1 p-1"
                                        >
                                            {dayAllDayEvents.map(event => (
                                                <Link
                                                    key={event.id}
                                                    href={event.href}
                                                    className="group flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 hover:bg-purple-100 dark:bg-purple-600/15 dark:text-purple-300 dark:hover:bg-purple-600/20"
                                                >
                                                    <span className="truncate">{event.name}</span>
                                                </Link>
                                            ))}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                )}

                {/* Scrollable timeslot container */}
                <div
                    ref={timeslotContainerRef}
                    className="overflow-scroll"
                    style={{ maxHeight: 'calc(100vh - 22rem)' }}
                >
                    <div className="flex flex-auto">
                        <div className="sticky left-0 z-10 w-14 flex-none bg-white ring-1 ring-gray-100 dark:bg-gray-900 dark:ring-white/5" />
                        <div className="grid flex-auto grid-cols-1 grid-rows-1">
                            {/* Horizontal lines */}
                            <div
                                style={{ gridTemplateRows: 'repeat(48, minmax(3.5rem, 1fr))' }}
                                className="col-start-1 col-end-2 row-start-1 grid divide-y divide-gray-100 dark:divide-white/5"
                            >
                                <div className="row-end-1 h-7" />
                                {hours
                                    .map(hour => (
                                        <div key={`hour-${hour}`}>
                                            <div className="sticky left-0 z-20 -mt-2.5 -ml-14 w-14 pr-2 text-right text-xs/5 text-gray-400 dark:text-gray-500">
                                                {hour.toString().padStart(2, '0')}:00
                                            </div>
                                        </div>
                                    ))
                                    .flatMap((element, index) => [element, <div key={`half-${index}`} />])}
                            </div>

                            {/* Vertical lines */}
                            <div className="col-start-1 col-end-2 row-start-1 hidden grid-rows-1 divide-x divide-gray-100 sm:grid sm:grid-cols-7 dark:divide-white/5">
                                {Array.from({ length: 8 }, (_, i) => (
                                    <div
                                        key={`col-${i}`}
                                        className={`${i === 0 ? 'col-start-1' : `col-start-${i + 1}`} row-span-full ${i === 7 ? 'w-8' : ''}`}
                                    />
                                ))}
                            </div>

                            {/* Timed Events */}
                            <ol
                                style={{ gridTemplateRows: 'repeat(288, minmax(0, 1fr))' }}
                                className="col-start-1 col-end-2 row-start-1 grid grid-cols-1 sm:grid-cols-7 sm:pr-8"
                            >
                                {timedEvents.map(event => {
                                    const color = eventColors[event.colorIndex % eventColors.length];
                                    const isFirstEvent = firstTimedEvent && event.id === firstTimedEvent.id;
                                    return (
                                        <li
                                            key={event.id}
                                            ref={isFirstEvent ? firstEventRef : null}
                                            style={{
                                                gridRow: `${event.gridRowStart} / span ${event.gridRowSpan}`,
                                                gridColumnStart: event.gridColumn,
                                            }}
                                            className="relative mt-px flex"
                                        >
                                            <Link
                                                href={event.href}
                                                style={{
                                                    left: `${event.leftOffset || 0}px`,
                                                    width: `calc(${event.widthPercent}% - ${event.leftOffset || 0}px - 4px)`,
                                                    zIndex: event.zIndex || 10,
                                                }}
                                                className={clsx(
                                                    'group absolute top-1 bottom-1 flex flex-col overflow-y-auto rounded-lg p-2 text-xs/5 hover:z-50',
                                                    color.bg,
                                                    color.bgHover,
                                                    color.darkBg,
                                                    color.darkBgHover,
                                                    event.hasBorder && 'ring-2 ring-white dark:ring-gray-900'
                                                )}
                                            >
                                                <p
                                                    className={clsx(
                                                        'order-1 font-semibold',
                                                        color.text,
                                                        color.darkText
                                                    )}
                                                >
                                                    {event.name}
                                                </p>
                                                <p
                                                    className={clsx(
                                                        color.textSecondary,
                                                        color.textHover,
                                                        color.darkTextSecondary,
                                                        color.darkTextHover
                                                    )}
                                                >
                                                    <time dateTime={event.datetime}>{event.time}</time>
                                                </p>
                                                {event.overflowCount > 0 && (
                                                    <p
                                                        className={clsx(
                                                            'mt-1 text-xs font-medium',
                                                            color.text,
                                                            color.darkText
                                                        )}
                                                    >
                                                        +{event.overflowCount} more
                                                    </p>
                                                )}
                                            </Link>
                                        </li>
                                    );
                                })}
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default WeekView;
