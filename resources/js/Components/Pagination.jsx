import { router } from '@inertiajs/react';

const Pagination = ({ data, routeName }) => {
    if (data.links.length <= 3) {
        return null;
    }

    const goToPage = page => {
        router.get(
            route(routeName),
            { page },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    return (
        <div className="mt-6 flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
            <div className="flex flex-1 justify-between sm:hidden">
                <button
                    onClick={() => goToPage(data.current_page - 1)}
                    disabled={!data.prev_page_url}
                    className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:disabled:bg-gray-800 dark:disabled:text-gray-500"
                >
                    {__('pagination.previous')}
                </button>
                <button
                    onClick={() => goToPage(data.current_page + 1)}
                    disabled={!data.next_page_url}
                    className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:disabled:bg-gray-800 dark:disabled:text-gray-500"
                >
                    {__('pagination.next')}
                </button>
            </div>
            <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p className="text-sm text-gray-700 dark:text-gray-300">
                        {__('pagination.showing')} <span className="font-medium">{data.from}</span>{' '}
                        {__('pagination.to')} <span className="font-medium">{data.to}</span> {__('pagination.of')}{' '}
                        <span className="font-medium">{data.total}</span> {__('pagination.results')}
                    </p>
                </div>
                <div>
                    <nav className="isolate inline-flex -space-x-px rounded-md shadow-sm">
                        <button
                            onClick={() => goToPage(1)}
                            disabled={!data.prev_page_url}
                            className="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            {__('pagination.first')}
                        </button>
                        <button
                            onClick={() => goToPage(data.current_page - 1)}
                            disabled={!data.prev_page_url}
                            className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            {__('pagination.previous')}
                        </button>
                        <span className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            {__('pagination.page')} {data.current_page} {__('pagination.of')} {data.last_page}
                        </span>
                        <button
                            onClick={() => goToPage(data.current_page + 1)}
                            disabled={!data.next_page_url}
                            className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            {__('pagination.next')}
                        </button>
                        <button
                            onClick={() => goToPage(data.last_page)}
                            disabled={!data.next_page_url}
                            className="relative inline-flex items-center rounded-r-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            {__('pagination.last')}
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    );
};

export default Pagination;
