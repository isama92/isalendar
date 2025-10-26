import UserPhoto from '@/Components/UserPhoto';
import { ArrowLeftEndOnRectangleIcon, UserCircleIcon } from '@heroicons/react/20/solid';
import { Link } from '@inertiajs/react';
import { useState } from 'react';

const UserDropdown = () => {
    const [dropdownOpen, setDropdownOpen] = useState(false);

    return (
        <div className="relative">
            <button
                onClick={() => setDropdownOpen(!dropdownOpen)}
                className="flex cursor-pointer items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                <span className="sr-only">{__('profile.profile')}</span>
                <UserPhoto className="size-8" iconClassName="size-8" />
            </button>

            {dropdownOpen && (
                <>
                    <div
                        className="fixed inset-0 z-10"
                        onClick={() => setDropdownOpen(false)}
                        onKeyDown={e => {
                            if (e.key === 'Escape') {
                                setDropdownOpen(false);
                            }
                        }}
                        role="button"
                        tabIndex={-1}
                        aria-label="Close dropdown"
                    />
                    <div className="absolute right-0 z-20 mt-2 w-48 rounded-md border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <Link
                            href={route('profile.edit')}
                            className="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                            onClick={() => setDropdownOpen(false)}
                        >
                            <UserCircleIcon className="h-5 w-5" />
                            <span>{__('profile.profile')}</span>
                        </Link>
                        <Link
                            href={route('logout')}
                            method="post"
                            className="flex w-full cursor-pointer items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                            onClick={() => setDropdownOpen(false)}
                        >
                            <ArrowLeftEndOnRectangleIcon className="h-5 w-5" />
                            <span>{__('auth.logout')}</span>
                        </Link>
                    </div>
                </>
            )}
        </div>
    );
};

export default UserDropdown;
