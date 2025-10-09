import { useTheme } from '@/Contexts/ThemeContext';
import { ArrowRightOnRectangleIcon, MoonIcon, SunIcon, UserCircleIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import { useState } from 'react';

const UserDropdown = ({ user }) => {
    const { theme, toggleTheme } = useTheme();
    const [dropdownOpen, setDropdownOpen] = useState(false);

    return (
        <div className="relative">
            <button
                onClick={() => setDropdownOpen(!dropdownOpen)}
                className="flex cursor-pointer items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                <span>{user.name}</span>
                <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {dropdownOpen && (
                <>
                    <div className="fixed inset-0 z-10" onClick={() => setDropdownOpen(false)} />
                    <div className="absolute right-0 z-20 mt-2 w-48 rounded-md border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <button
                            onClick={() => {
                                toggleTheme();
                                setDropdownOpen(false);
                            }}
                            className="flex w-full cursor-pointer items-center gap-2 px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            {theme === 'light' ? <MoonIcon className="h-5 w-5" /> : <SunIcon className="h-5 w-5" />}
                            <span>{theme === 'light' ? __('common.dark_mode') : __('common.light_mode')}</span>
                        </button>
                        <Link
                            href={route('profile.edit')}
                            className="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                            onClick={() => setDropdownOpen(false)}
                        >
                            <UserCircleIcon className="h-5 w-5" />
                            <span>{__('common.profile')}</span>
                        </Link>
                        <Link
                            href={route('logout')}
                            method="post"
                            className="flex w-full cursor-pointer items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                            onClick={() => setDropdownOpen(false)}
                        >
                            <ArrowRightOnRectangleIcon className="h-5 w-5" />
                            <span>{__('auth.logout')}</span>
                        </Link>
                    </div>
                </>
            )}
        </div>
    );
};

export default UserDropdown;
