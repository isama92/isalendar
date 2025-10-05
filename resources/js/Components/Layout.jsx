import { ArrowRightOnRectangleIcon, UserCircleIcon } from '@heroicons/react/24/outline';
import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

const Layout = ({ children }) => {
    const { user } = usePage().props;
    const [dropdownOpen, setDropdownOpen] = useState(false);

    return (
        <div className="flex min-h-screen bg-gray-50">
            {/* Sidebar */}
            {user && (
                <aside className="w-64 border-r border-gray-200 bg-white">
                    <div className="flex h-16 items-center justify-center border-b border-gray-200 px-6">
                        <img src="/assets/logo.png" alt="Isalendar" className="h-8" />
                    </div>
                    <nav className="p-4">
                        <Link
                            href={route('dashboard')}
                            className="mb-2 block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href={route('events.index')}
                            className="block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        >
                            Events
                        </Link>
                    </nav>
                </aside>
            )}

            {/* Main Content */}
            <div className="flex flex-1 flex-col">
                {/* Topbar */}
                <header className="border-b border-gray-200 bg-white">
                    {user && (
                        <div className="flex h-16 items-center justify-end px-6">
                            <div className="relative">
                                <button
                                    onClick={() => setDropdownOpen(!dropdownOpen)}
                                    className="flex cursor-pointer items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    <span>{user.name}</span>
                                    <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </button>

                                {dropdownOpen && (
                                    <>
                                        <div className="fixed inset-0 z-10" onClick={() => setDropdownOpen(false)} />
                                        <div className="absolute right-0 z-20 mt-2 w-48 rounded-md border border-gray-200 bg-white shadow-lg">
                                            <Link
                                                href={route('profile.edit')}
                                                className="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                onClick={() => setDropdownOpen(false)}
                                            >
                                                <UserCircleIcon className="h-5 w-5" />
                                                <span>Profile</span>
                                            </Link>
                                            <Link
                                                href={route('logout')}
                                                method="post"
                                                className="flex w-full cursor-pointer items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                                                onClick={() => setDropdownOpen(false)}
                                            >
                                                <ArrowRightOnRectangleIcon className="h-5 w-5" />
                                                <span>Logout</span>
                                            </Link>
                                        </div>
                                    </>
                                )}
                            </div>
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
