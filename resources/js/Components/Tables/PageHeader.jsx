import { Link } from '@inertiajs/react';

const PageHeader = ({ title, actionHref, actionLabel }) => {
    return (
        <div className="sm:flex sm:items-center">
            <div className="sm:flex-auto">
                <h1 className="text-base font-semibold text-gray-900 dark:text-white">{title}</h1>
            </div>
            <div className="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <Link
                    href={actionHref}
                    className="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                >
                    {actionLabel}
                </Link>
            </div>
        </div>
    );
};

export default PageHeader;
