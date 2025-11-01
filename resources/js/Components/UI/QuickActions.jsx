import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import { CalendarDaysIcon, CalendarIcon, ChevronDownIcon, PlusIcon, UserPlusIcon } from '@heroicons/react/20/solid';
import { Link } from '@inertiajs/react';

const QuickActions = () => {
    return (
        <Menu as="div" className="relative inline-block">
            <MenuButton className="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
                {__('actions.quick_actions')}
                <ChevronDownIcon aria-hidden="true" className="-mr-1 size-5 text-gray-400 dark:text-gray-500" />
            </MenuButton>

            <MenuItems
                transition
                className="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg outline-1 outline-black/5 transition data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in dark:divide-white/10 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
            >
                <div className="py-1">
                    <MenuItem>
                        <Link
                            href={route('events.batch.create')}
                            className="group flex items-center px-4 py-2 text-sm text-gray-700 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                        >
                            <CalendarDaysIcon
                                aria-hidden="true"
                                className="mr-3 size-5 text-gray-400 group-data-focus:text-gray-500 dark:text-gray-500 dark:group-data-focus:text-white"
                            />
                            {__('actions.batch_create_events')}
                        </Link>
                    </MenuItem>
                    <MenuItem>
                        <Link
                            href={route('events.create')}
                            className="group flex items-center px-4 py-2 text-sm text-gray-700 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                        >
                            <PlusIcon
                                aria-hidden="true"
                                className="mr-3 size-5 text-gray-400 group-data-focus:text-gray-500 dark:text-gray-500 dark:group-data-focus:text-white"
                            />
                            {__('actions.named.create', { name: __('events.event') })}
                        </Link>
                    </MenuItem>
                </div>
                <div className="py-1">
                    <MenuItem>
                        <Link
                            href={route('providers.create')}
                            className="group flex items-center px-4 py-2 text-sm text-gray-700 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                        >
                            <CalendarIcon
                                aria-hidden="true"
                                className="mr-3 size-5 text-gray-400 group-data-focus:text-gray-500 dark:text-gray-500 dark:group-data-focus:text-white"
                            />
                            {__('actions.named.add', { name: __('calendars.calendar') })}
                        </Link>
                    </MenuItem>
                    <MenuItem>
                        <Link
                            href={route('users.create')}
                            className="group flex items-center px-4 py-2 text-sm text-gray-700 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                        >
                            <UserPlusIcon
                                aria-hidden="true"
                                className="mr-3 size-5 text-gray-400 group-data-focus:text-gray-500 dark:text-gray-500 dark:group-data-focus:text-white"
                            />
                            {__('actions.named.create', { name: __('users.user') })}
                        </Link>
                    </MenuItem>
                </div>
            </MenuItems>
        </Menu>
    );
};

export default QuickActions;
