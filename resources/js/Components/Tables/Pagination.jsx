import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/24/solid';

const Pagination = ({ table }) => {
    if (table.getPageCount() <= 1) {
        return null;
    }

    return (
        <div className="mt-6 flex items-center justify-between border-t border-gray-300 pt-6 dark:border-white/15">
            <div className="flex flex-1 justify-between sm:hidden">
                <button
                    onClick={() => table.previousPage()}
                    disabled={!table.getCanPreviousPage()}
                    className="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-900 dark:text-white dark:ring-white/15 dark:hover:bg-gray-800"
                >
                    {__('pagination.previous')}
                </button>
                <button
                    onClick={() => table.nextPage()}
                    disabled={!table.getCanNextPage()}
                    className="relative ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-900 dark:text-white dark:ring-white/15 dark:hover:bg-gray-800"
                >
                    {__('pagination.next')}
                </button>
            </div>
            <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p className="text-sm text-gray-700 dark:text-gray-400">
                        {__('pagination.showing')}{' '}
                        <span className="font-medium text-gray-900 dark:text-white">
                            {table.getState().pagination.pageIndex * table.getState().pagination.pageSize + 1}
                        </span>{' '}
                        {__('pagination.to')}{' '}
                        <span className="font-medium text-gray-900 dark:text-white">
                            {Math.min(
                                (table.getState().pagination.pageIndex + 1) * table.getState().pagination.pageSize,
                                table.getFilteredRowModel().rows.length
                            )}
                        </span>{' '}
                        {__('pagination.of')}{' '}
                        <span className="font-medium text-gray-900 dark:text-white">
                            {table.getFilteredRowModel().rows.length}
                        </span>{' '}
                        {__('pagination.results')}
                    </p>
                </div>
                <div className="flex items-center gap-x-1.5">
                    <button
                        onClick={() => table.setPageIndex(0)}
                        disabled={!table.getCanPreviousPage()}
                        className="relative inline-flex cursor-pointer items-center rounded-md bg-white px-2.5 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-900 dark:text-white dark:ring-white/15 dark:hover:bg-gray-800"
                    >
                        <ChevronLeftIcon className="h-5 w-5" />
                        <ChevronLeftIcon className="-ml-3 h-5 w-5" />
                    </button>
                    <button
                        onClick={() => table.previousPage()}
                        disabled={!table.getCanPreviousPage()}
                        className="relative inline-flex cursor-pointer items-center rounded-md bg-white px-2.5 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-900 dark:text-white dark:ring-white/15 dark:hover:bg-gray-800"
                    >
                        <ChevronLeftIcon className="h-5 w-5" />
                    </button>
                    <span className="text-sm font-semibold text-gray-900 dark:text-white">
                        {table.getState().pagination.pageIndex + 1} / {table.getPageCount()}
                    </span>
                    <button
                        onClick={() => table.nextPage()}
                        disabled={!table.getCanNextPage()}
                        className="relative inline-flex cursor-pointer items-center rounded-md bg-white px-2.5 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-900 dark:text-white dark:ring-white/15 dark:hover:bg-gray-800"
                    >
                        <ChevronRightIcon className="h-5 w-5" />
                    </button>
                    <button
                        onClick={() => table.setPageIndex(table.getPageCount() - 1)}
                        disabled={!table.getCanNextPage()}
                        className="relative inline-flex cursor-pointer items-center rounded-md bg-white px-2.5 py-2 text-sm font-semibold text-gray-900 ring-1 ring-gray-300 ring-inset hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-900 dark:text-white dark:ring-white/15 dark:hover:bg-gray-800"
                    >
                        <ChevronRightIcon className="h-5 w-5" />
                        <ChevronRightIcon className="-ml-3 h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Pagination;
