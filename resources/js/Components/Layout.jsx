import { Link, usePage } from '@inertiajs/react';
import LanguageSwitcher from './LanguageSwitcher';
import UserDropdown from './UserDropdown';

const Layout = ({ children }) => {
    const { user } = usePage().props;

    return (
        <div className="flex min-h-screen bg-gray-50 dark:bg-gray-900">
            {/* Sidebar */}
            {user && (
                <aside className="w-64 border-r border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    <div className="flex h-16 items-center justify-center border-b border-gray-200 px-6 dark:border-gray-700">
                        <img src="/assets/logo.png" alt="Isalendar" className="h-8" />
                    </div>
                    <nav className="p-4">
                        <Link
                            href={route('dashboard')}
                            className="mb-2 block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            {__('dashboard.dashboard')}
                        </Link>
                        <Link
                            href={route('events.index')}
                            className="block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            {__('events.events')}
                        </Link>
                    </nav>
                </aside>
            )}

            {/* Main Content */}
            <div className="flex flex-1 flex-col">
                {/* Topbar */}
                <header className="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    {user && (
                        <div className="flex h-16 items-center justify-end gap-2 px-6">
                            <LanguageSwitcher />
                            <UserDropdown />
                        </div>
                    )}
                </header>

                {/* Page Content */}
                <main className="flex-1 p-6">{children}</main>
            </div>
        </div>
    );
};

export default Layout;
