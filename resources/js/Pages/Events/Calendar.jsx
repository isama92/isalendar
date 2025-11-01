import { ROUTES } from '@/constants';
import { useCalendarView } from '@/hooks';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import {
    ChevronDownIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    EllipsisHorizontalIcon,
    PlusIcon,
} from '@heroicons/react/20/solid';
import { TableCellsIcon } from '@heroicons/react/24/solid';
import { Link } from '@inertiajs/react';
import dayjs from 'dayjs';
import isBetween from 'dayjs/plugin/isBetween';
import timezone from 'dayjs/plugin/timezone';
import utc from 'dayjs/plugin/utc';
import { useMemo } from 'react';
import MonthView from './MonthView';
import WeekView from './WeekView';

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(isBetween);

const Calendar = ({ currentUser, events }) => {
    const { currentDate, view, viewTitle, handlePrevious, handleNext, handleToday, handleViewChange, CALENDAR_VIEWS } =
        useCalendarView();

    // Transform events data
    const transformedEvents = useMemo(() => {
        return events.map(event => {
            // Parse UTC times and convert to user timezone
            const startsAt = event.all_day
                ? dayjs(event.starts_at)
                : dayjs.utc(event.starts_at).tz(currentUser.settings.default_timezone);
            const endsAt = event.all_day
                ? dayjs(event.ends_at)
                : dayjs.utc(event.ends_at).tz(currentUser.settings.default_timezone);

            return {
                id: event.id,
                name: event.title,
                datetime: event.starts_at,
                time: event.all_day ? '' : startsAt.format('h:mm A'),
                href: route(ROUTES.EVENTS.SHOW, event.id),
                allDay: event.all_day,
                startsAt,
                endsAt,
                calendarName: event.calendar?.name,
            };
        });
    }, [events, currentUser.settings.default_timezone]);

    return (
        <div className="lg:flex lg:h-full lg:flex-col">
            <header className="flex items-center justify-between border-b border-gray-200 px-6 py-4 lg:flex-none dark:border-white/10 dark:bg-gray-800/50">
                <h1 className="text-base font-semibold text-gray-900 capitalize dark:text-white">
                    <time dateTime={currentDate.format('YYYY-MM')}>{viewTitle}</time>
                </h1>
                <div className="flex items-center">
                    <div className="relative flex items-center rounded-md bg-white shadow-xs outline -outline-offset-1 outline-gray-300 md:items-stretch dark:bg-white/10 dark:shadow-none dark:outline-white/5">
                        <button
                            type="button"
                            onClick={handlePrevious}
                            className="flex h-9 w-12 cursor-pointer items-center justify-center rounded-l-md pr-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pr-0 md:hover:bg-gray-50 dark:hover:text-white dark:md:hover:bg-white/10"
                        >
                            <span className="sr-only">
                                {view === CALENDAR_VIEWS.MONTH
                                    ? __('events.previous_month')
                                    : __('events.previous_week')}
                            </span>
                            <ChevronLeftIcon aria-hidden="true" className="size-5" />
                        </button>
                        <button
                            type="button"
                            onClick={handleToday}
                            className="hidden cursor-pointer px-3.5 text-sm font-semibold text-gray-900 hover:bg-gray-50 focus:relative md:block dark:text-white dark:hover:bg-white/10"
                        >
                            {__('events.today')}
                        </button>
                        <span className="relative -mx-px h-5 w-px bg-gray-300 md:hidden dark:bg-white/10" />
                        <button
                            type="button"
                            onClick={handleNext}
                            className="flex h-9 w-12 cursor-pointer items-center justify-center rounded-r-md pl-1 text-gray-400 hover:text-gray-500 focus:relative md:w-9 md:pl-0 md:hover:bg-gray-50 dark:hover:text-white dark:md:hover:bg-white/10"
                        >
                            <span className="sr-only">
                                {view === CALENDAR_VIEWS.MONTH ? __('events.next_month') : __('events.next_week')}
                            </span>
                            <ChevronRightIcon aria-hidden="true" className="size-5" />
                        </button>
                    </div>
                    <div className="hidden md:ml-4 md:flex md:items-center">
                        <Menu as="div" className="relative">
                            <MenuButton
                                type="button"
                                className="flex cursor-pointer items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 capitalize shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"
                            >
                                {view === CALENDAR_VIEWS.MONTH ? __('events.month_view') : __('events.week_view')}
                                <ChevronDownIcon
                                    aria-hidden="true"
                                    className="-mr-1 size-5 text-gray-400 dark:text-gray-500"
                                />
                            </MenuButton>

                            <MenuItems
                                transition
                                className="absolute right-0 z-10 mt-3 w-36 origin-top-right overflow-hidden rounded-md bg-white shadow-lg outline-1 outline-black/5 transition data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in dark:bg-gray-800 dark:-outline-offset-1 dark:outline-white/10"
                            >
                                <div className="py-1">
                                    <MenuItem>
                                        <button
                                            onClick={() => handleViewChange(CALENDAR_VIEWS.WEEK)}
                                            className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 capitalize data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                        >
                                            {__('events.week_view')}
                                        </button>
                                    </MenuItem>
                                    <MenuItem>
                                        <button
                                            onClick={() => handleViewChange(CALENDAR_VIEWS.MONTH)}
                                            className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 capitalize data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                        >
                                            {__('events.month_view')}
                                        </button>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </Menu>
                        <div className="ml-6 h-6 w-px bg-gray-300 dark:bg-white/10" />
                        <Link
                            href={route(ROUTES.EVENTS.INDEX)}
                            className="ml-6 inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"
                        >
                            <TableCellsIcon aria-hidden="true" className="-ml-0.5 size-5" />
                            {__('events.table')}
                        </Link>
                        <Link
                            href={route(ROUTES.EVENTS.CREATE)}
                            className="ml-3 inline-flex items-center gap-x-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                        >
                            <PlusIcon aria-hidden="true" className="-ml-0.5 size-5" />
                            {__('actions.named.create', { name: __('events.event') })}
                        </Link>
                    </div>
                    <Menu as="div" className="relative ml-6 md:hidden">
                        <MenuButton className="-mx-2 flex cursor-pointer items-center rounded-full border border-transparent p-2 text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-white">
                            <span className="sr-only">{__('common.open_menu')}</span>
                            <EllipsisHorizontalIcon aria-hidden="true" className="size-5" />
                        </MenuButton>

                        <MenuItems
                            transition
                            className="absolute right-0 z-10 mt-3 w-36 origin-top-right divide-y divide-gray-100 overflow-hidden rounded-md bg-white shadow-lg outline-1 outline-black/5 transition data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in dark:divide-white/10 dark:bg-gray-800 dark:-outline-offset-1 dark:outline-white/10"
                        >
                            <div className="py-1">
                                <MenuItem>
                                    <Link
                                        href={route(ROUTES.EVENTS.CREATE)}
                                        className="block px-4 py-2 text-sm text-gray-700 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                    >
                                        {__('events.create_event')}
                                    </Link>
                                </MenuItem>
                            </div>
                            <div className="py-1">
                                <MenuItem>
                                    <button
                                        onClick={handleToday}
                                        className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                    >
                                        {__('events.go_to_today')}
                                    </button>
                                </MenuItem>
                            </div>
                            <div className="py-1">
                                <MenuItem>
                                    <button
                                        onClick={() => handleViewChange(CALENDAR_VIEWS.WEEK)}
                                        className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                    >
                                        {__('events.week_view')}
                                    </button>
                                </MenuItem>
                                <MenuItem>
                                    <button
                                        onClick={() => handleViewChange(CALENDAR_VIEWS.MONTH)}
                                        className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                    >
                                        {__('events.month_view')}
                                    </button>
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </Menu>
                </div>
            </header>

            {view === CALENDAR_VIEWS.MONTH ? (
                <MonthView date={currentDate} events={transformedEvents} />
            ) : (
                <WeekView date={currentDate} events={transformedEvents} />
            )}
        </div>
    );
};

export default Calendar;
