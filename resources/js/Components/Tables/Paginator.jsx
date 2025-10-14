import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/24/solid';

const Paginator = ({ table }) => {
    if (table.getPageCount() <= 1) {
        return null;
    }

    const currentPage = table.getState().pagination.pageIndex;
    const pageCount = table.getPageCount();

    const getPageNumbers = () => {
        const pages = [];
        const maxVisible = 7;

        if (pageCount <= maxVisible) {
            // Show all pages if there are few enough
            for (let i = 0; i < pageCount; i++) {
                pages.push(i);
            }
        } else {
            pages.push(0);

            if (currentPage <= 3) {
                pages.push(1, 2, 3, 4, 'ellipsis', pageCount - 1);
            } else if (currentPage >= pageCount - 4) {
                pages.push('ellipsis', pageCount - 5, pageCount - 4, pageCount - 3, pageCount - 2, pageCount - 1);
            } else {
                pages.push('ellipsis', currentPage - 1, currentPage, currentPage + 1, 'ellipsis', pageCount - 1);
            }
        }

        return pages;
    };

    const pageNumbers = getPageNumbers();

    return (
        <div className="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 dark:border-white/10 dark:bg-transparent">
            <div className="flex flex-1 justify-between sm:hidden">
                <button
                    onClick={() => table.previousPage()}
                    disabled={!table.getCanPreviousPage()}
                    className="relative inline-flex cursor-pointer items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10"
                >
                    {__('pagination.previous')}
                </button>
                <button
                    onClick={() => table.nextPage()}
                    disabled={!table.getCanNextPage()}
                    className="relative ml-3 inline-flex cursor-pointer items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10"
                >
                    {__('pagination.next')}
                </button>
            </div>
            <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p className="text-sm text-gray-700 dark:text-gray-300">
                        {__('pagination.showing')}{' '}
                        <span className="font-medium">
                            {table.getState().pagination.pageIndex * table.getState().pagination.pageSize + 1}
                        </span>{' '}
                        {__('pagination.to')}{' '}
                        <span className="font-medium">
                            {Math.min(
                                (table.getState().pagination.pageIndex + 1) * table.getState().pagination.pageSize,
                                table.getFilteredRowModel().rows.length
                            )}
                        </span>{' '}
                        {__('pagination.of')}{' '}
                        <span className="font-medium">{table.getFilteredRowModel().rows.length}</span>{' '}
                        {__('pagination.results')}
                    </p>
                </div>
                <div>
                    <nav
                        aria-label="Pagination"
                        className="isolate inline-flex -space-x-px rounded-md shadow-xs dark:shadow-none"
                    >
                        <button
                            onClick={() => table.previousPage()}
                            disabled={!table.getCanPreviousPage()}
                            className="relative inline-flex cursor-pointer items-center rounded-l-md px-2 py-2 text-gray-400 inset-ring inset-ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:cursor-not-allowed disabled:opacity-50 dark:inset-ring-gray-700 dark:hover:bg-white/5"
                        >
                            <span className="sr-only">{__('pagination.previous')}</span>
                            <ChevronLeftIcon aria-hidden="true" className="size-5" />
                        </button>
                        {pageNumbers.map((pageNumber, index) => {
                            if (pageNumber === 'ellipsis') {
                                return (
                                    <span
                                        key={`ellipsis-${index}`}
                                        className="relative inline-flex cursor-default items-center px-4 py-2 text-sm font-semibold text-gray-700 inset-ring inset-ring-gray-300 select-none focus:outline-offset-0 dark:text-gray-400 dark:inset-ring-gray-700"
                                    >
                                        ...
                                    </span>
                                );
                            }

                            const isCurrentPage = pageNumber === currentPage;
                            const isHiddenOnMobile =
                                pageNumber !== 0 &&
                                pageNumber !== pageCount - 1 &&
                                Math.abs(pageNumber - currentPage) > 1;

                            return (
                                <button
                                    key={pageNumber}
                                    onClick={() => table.setPageIndex(pageNumber)}
                                    aria-current={isCurrentPage ? 'page' : undefined}
                                    className={
                                        isCurrentPage
                                            ? 'relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:focus-visible:outline-indigo-500'
                                            : `relative cursor-pointer ${isHiddenOnMobile ? 'hidden md:inline-flex' : 'inline-flex'} items-center px-4 py-2 text-sm font-semibold text-gray-900 inset-ring inset-ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 dark:text-gray-200 dark:inset-ring-gray-700 dark:hover:bg-white/5`
                                    }
                                >
                                    {pageNumber + 1}
                                </button>
                            );
                        })}
                        <button
                            onClick={() => table.nextPage()}
                            disabled={!table.getCanNextPage()}
                            className="relative inline-flex cursor-pointer items-center rounded-r-md px-2 py-2 text-gray-400 inset-ring inset-ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:cursor-not-allowed disabled:opacity-50 dark:inset-ring-gray-700 dark:hover:bg-white/5"
                        >
                            <span className="sr-only">{__('pagination.next')}</span>
                            <ChevronRightIcon aria-hidden="true" className="size-5" />
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    );
};

export default Paginator;
