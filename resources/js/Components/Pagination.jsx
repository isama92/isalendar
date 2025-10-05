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
                    Previous
                </button>
                <button
                    onClick={() => goToPage(data.current_page + 1)}
                    disabled={!data.next_page_url}
                    className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400"
                >
                    Next
                </button>
            </div>
            <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p className="text-sm text-gray-700">
                        Showing <span className="font-medium">{data.from}</span> to{' '}
                        <span className="font-medium">{data.to}</span> of{' '}
                        <span className="font-medium">{data.total}</span> results
                    </p>
                </div>
                <div>
                    <nav className="isolate inline-flex -space-x-px rounded-md shadow-sm">
                        <button
                            onClick={() => goToPage(1)}
                            disabled={!data.prev_page_url}
                            className="relative inline-flex items-center rounded-l-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            First
                        </button>
                        <button
                            onClick={() => goToPage(data.current_page - 1)}
                            disabled={!data.prev_page_url}
                            className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <span className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700">
                            Page {data.current_page} of {data.last_page}
                        </span>
                        <button
                            onClick={() => goToPage(data.current_page + 1)}
                            disabled={!data.next_page_url}
                            className="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Next
                        </button>
                        <button
                            onClick={() => goToPage(data.last_page)}
                            disabled={!data.next_page_url}
                            className="relative inline-flex items-center rounded-r-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Last
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    );
};

export default Pagination;
