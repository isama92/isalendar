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
        <div className="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
            <div className="flex flex-1 justify-between sm:hidden">
                <button
                    onClick={() => goToPage(data.current_page - 1)}
                    disabled={!data.prev_page_url}
                    className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400"
                >
                    {__('common.previous')}
                </button>
                <button
                    onClick={() => goToPage(data.current_page + 1)}
                    disabled={!data.next_page_url}
                    className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400"
                >
                    {__('common.next')}
                </button>
            </div>
            <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p className="text-sm text-gray-700">
                        {__('common.showing')} <span className="font-medium">{data.from}</span> {__('common.to')}{' '}
                        <span className="font-medium">{data.to}</span> {__('common.of')}{' '}
                        <span className="font-medium">{data.total}</span> {__('common.results')}
                    </p>
                </div>
                <div>
                    <nav className="isolate inline-flex -space-x-px rounded-md shadow-sm">
                        <button
                            onClick={() => goToPage(1)}
                            disabled={!data.prev_page_url}
                            className="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {__('common.first')}
                        </button>
                        <button
                            onClick={() => goToPage(data.current_page - 1)}
                            disabled={!data.prev_page_url}
                            className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {__('common.previous')}
                        </button>
                        <span className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700">
                            {__('common.page')} {data.current_page} {__('common.of')} {data.last_page}
                        </span>
                        <button
                            onClick={() => goToPage(data.current_page + 1)}
                            disabled={!data.next_page_url}
                            className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {__('common.next')}
                        </button>
                        <button
                            onClick={() => goToPage(data.last_page)}
                            disabled={!data.next_page_url}
                            className="relative inline-flex items-center rounded-r-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {__('common.last')}
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    );
};

export default Pagination;
