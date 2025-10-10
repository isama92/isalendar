import LogoDark from '@/../images/logo-dark.png';
import LogoLight from '@/../images/logo-light.png';
import LanguageSwitcher from '@/Components/LanguageSwitcher';
import ThemeSwitcher from '@/Components/ThemeSwitcher';
import UserDropdown from '@/Components/UserDropdown';
import { Dialog, DialogBackdrop, DialogPanel, TransitionChild } from '@headlessui/react';
import { Bars3Icon } from '@heroicons/react/20/solid';
import { XMarkIcon } from '@heroicons/react/24/outline';
import { Link, usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { useState } from 'react';

const Layout = ({ children }) => {
    const { appName, user, connectorEnabled } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const currentRoute = route().current();

    const navigation = [
        { name: __('dashboard.dashboard'), href: route('dashboard'), current: currentRoute === 'dashboard' },
        {
            name: __('calendars.calendars'),
            href: route('calendars.index'),
            current: currentRoute === 'calendars.index',
        },
        ...(connectorEnabled
            ? [{ name: __('events.events'), href: route('events.index'), current: currentRoute === 'events.index' }]
            : []),
    ];

    return (
        <>
            <div>
                <Dialog open={sidebarOpen} onClose={setSidebarOpen} className="relative z-50 xl:hidden">
                    <DialogBackdrop
                        transition
                        className="fixed inset-0 bg-gray-900/80 transition-opacity duration-300 ease-linear data-closed:opacity-0"
                    />

                    <div className="fixed inset-0 flex">
                        <DialogPanel
                            transition
                            className="relative mr-16 flex w-full max-w-xs flex-1 transform transition duration-300 ease-in-out data-closed:-translate-x-full"
                        >
                            <TransitionChild>
                                <div className="absolute top-0 left-full flex w-16 justify-center pt-5 duration-300 ease-in-out data-closed:opacity-0">
                                    <button
                                        type="button"
                                        onClick={() => setSidebarOpen(false)}
                                        className="-m-2.5 p-2.5"
                                    >
                                        <span className="sr-only">Close sidebar</span>
                                        <XMarkIcon aria-hidden="true" className="size-6 text-white" />
                                    </button>
                                </div>
                            </TransitionChild>

                            {/* Sidebar component for mobile */}
                            <div className="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 dark:bg-gray-800">
                                <div className="relative flex h-16 shrink-0 items-center">
                                    <Link href={route('dashboard')} onClick={() => setSidebarOpen(false)}>
                                        <img src={LogoLight} alt={appName} className="h-8 w-auto dark:hidden" />
                                        <img src={LogoDark} alt={appName} className="h-8 w-auto not-dark:hidden" />
                                    </Link>
                                </div>
                                <nav className="relative flex flex-1 flex-col">
                                    <ul role="list" className="flex flex-1 flex-col gap-y-7">
                                        <li>
                                            <div className="flex items-center justify-center">
                                                <LanguageSwitcher />
                                                <ThemeSwitcher className="ml-5" />
                                            </div>
                                        </li>
                                        <li>
                                            <ul role="list" className="-mx-2 space-y-1">
                                                {navigation.map(item => (
                                                    <li key={item.name}>
                                                        <Link
                                                            href={item.href}
                                                            onClick={() => setSidebarOpen(false)}
                                                            className={clsx(
                                                                'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                                                item.current
                                                                    ? 'bg-gray-100 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400'
                                                                    : 'text-gray-700 hover:bg-gray-100 hover:text-indigo-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-indigo-400'
                                                            )}
                                                        >
                                                            {item.name}
                                                        </Link>
                                                    </li>
                                                ))}
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </DialogPanel>
                    </div>
                </Dialog>

                {/* Static sidebar for desktop */}
                {user && (
                    <div className="hidden xl:fixed xl:inset-y-0 xl:z-50 xl:flex xl:w-72 xl:flex-col">
                        {/* Sidebar component for desktop */}
                        <div className="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                            <div className="flex h-16 shrink-0 items-center">
                                <Link href={route('dashboard')}>
                                    <img src={LogoLight} alt={appName} className="h-8 w-auto dark:hidden" />
                                    <img src={LogoDark} alt={appName} className="h-8 w-auto not-dark:hidden" />
                                </Link>
                            </div>
                            <nav className="flex flex-1 flex-col">
                                <ul role="list" className="flex flex-1 flex-col gap-y-7">
                                    <li>
                                        <div className="flex items-center justify-center">
                                            <LanguageSwitcher />
                                            <ThemeSwitcher className="ml-5" />
                                        </div>
                                    </li>
                                    <li>
                                        <ul role="list" className="-mx-2 space-y-1">
                                            {navigation.map(item => (
                                                <li key={item.name}>
                                                    <Link
                                                        href={item.href}
                                                        className={clsx(
                                                            'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                                            item.current
                                                                ? 'bg-gray-100 text-indigo-600 dark:bg-gray-700 dark:text-indigo-400'
                                                                : 'text-gray-700 hover:bg-gray-100 hover:text-indigo-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-indigo-400'
                                                        )}
                                                    >
                                                        {item.name}
                                                    </Link>
                                                </li>
                                            ))}
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                )}

                <div className={clsx(user ? 'xl:pl-72' : '')}>
                    {/* Sticky header */}
                    {user && (
                        <div className="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-6 border-b border-gray-200 bg-white px-4 shadow-xs sm:px-6 lg:px-8 dark:border-gray-700 dark:bg-gray-800">
                            <button
                                type="button"
                                onClick={() => setSidebarOpen(true)}
                                className="-m-2.5 p-2.5 text-gray-900 xl:hidden dark:text-gray-100"
                            >
                                <span className="sr-only">Open sidebar</span>
                                <Bars3Icon aria-hidden="true" className="size-5" />
                            </button>

                            <div className="flex flex-1 items-center justify-end gap-x-4 self-stretch lg:gap-x-6">
                                <div className="flex items-center gap-2">
                                    <UserDropdown user={user} />
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Page Content */}
                    <main className="container mx-auto my-3 sm:px-6 lg:px-8">{children}</main>
                </div>
            </div>
        </>
    );
};

export default Layout;
