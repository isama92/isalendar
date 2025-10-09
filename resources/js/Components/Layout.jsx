import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import LogoDark from '../../images/logo-dark.png';
import LogoLight from '../../images/logo-light.png';
import { useTheme } from '../Contexts/ThemeContext.jsx';
import LanguageSwitcher from './LanguageSwitcher';
import UserDropdown from './UserDropdown';

const Layout = ({ children }) => {
    const { user, locale, connectorEnabled } = usePage().props;
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const { theme } = useTheme();
    const logo = theme === 'dark' ? LogoDark : LogoLight;

    return (
        <div className="flex min-h-screen bg-gray-50 dark:bg-gray-900">
            {/* Sidebar */}
            {user && (
                <>
                    {/* Mobile overlay */}
                    {isSidebarOpen && (
                        <div
                            className="bg-opacity-75 fixed inset-0 z-40 bg-gray-600 lg:hidden"
                            onClick={() => setIsSidebarOpen(false)}
                        />
                    )}

                    {/* Sidebar */}
                    <aside
                        className={`fixed inset-y-0 left-0 z-50 w-64 transform border-r border-gray-200 bg-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 dark:border-gray-700 dark:bg-gray-800 ${
                            isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
                        }`}
                    >
                        <Link
                            href={route('dashboard')}
                            onClick={() => setIsSidebarOpen(false)}
                            className="flex h-16 items-center justify-center border-b border-gray-200 px-6 dark:border-gray-700"
                        >
                            <img src={logo} alt="Isalendar" className="h-8" />
                        </Link>
                        <nav className="p-4">
                            <Link
                                href={route('dashboard')}
                                onClick={() => setIsSidebarOpen(false)}
                                className="mb-2 block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                {__('dashboard.dashboard')}
                            </Link>
                            <Link
                                href={route('calendars.index')}
                                onClick={() => setIsSidebarOpen(false)}
                                className="block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                {__('calendars.calendars')}
                            </Link>
                            {connectorEnabled && (
                                <Link
                                    href={route('events.index')}
                                    onClick={() => setIsSidebarOpen(false)}
                                    className="block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    {__('events.events')}
                                </Link>
                            )}
                        </nav>
                    </aside>
                </>
            )}

            {/* Main Content */}
            <div className="flex min-w-0 flex-1 flex-col">
                {/* Topbar */}
                <header className="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    {user && (
                        <div className="relative flex h-16 w-full items-center px-6">
                            <button
                                onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                                className="cursor-pointer rounded-md p-2 text-gray-600 hover:bg-gray-100 lg:hidden dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                </svg>
                            </button>
                            <Link
                                href={route('dashboard')}
                                className="hidden flex-1 justify-center min-[400px]:flex sm:absolute sm:left-1/2 sm:-translate-x-1/2 lg:hidden"
                            >
                                <img src={logo} alt="Isalendar" className="h-8" />
                            </Link>
                            <div className="ml-auto flex items-center gap-2">
                                <LanguageSwitcher locale={locale} />
                                <UserDropdown user={user} />
                            </div>
                        </div>
                    )}
                </header>

                {/* Page Content */}
                <main className="min-w-0 flex-1 p-6">{children}</main>
            </div>
        </div>
    );
};

export default Layout;
