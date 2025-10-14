import { ChevronDownIcon } from '@heroicons/react/24/solid';
import { flexRender } from '@tanstack/react-table';
import clsx from 'clsx';

const Table = ({ table, emptyMessage, emptyIcon: EmptyIcon }) => {
    return (
        <div className="mt-8 flow-root">
            <div className="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div className="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    {table.getRowModel().rows.length === 0 ? (
                        <div className="text-center">
                            <EmptyIcon
                                aria-hidden="true"
                                className="mx-auto size-12 text-gray-400 dark:text-gray-500"
                            />
                            <p className="mt-4 text-sm text-gray-500 dark:text-gray-400">{emptyMessage}</p>
                        </div>
                    ) : (
                        <table className="relative min-w-full divide-y divide-gray-300 dark:divide-white/15">
                            <thead>
                                {table.getHeaderGroups().map(headerGroup => (
                                    <tr key={headerGroup.id}>
                                        {headerGroup.headers.map((header, index) => (
                                            <th
                                                key={header.id}
                                                scope="col"
                                                className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white"
                                            >
                                                {header.isPlaceholder ? null : header.column.getCanSort() ? (
                                                    <button
                                                        type="button"
                                                        onClick={header.column.getToggleSortingHandler()}
                                                        className="group inline-flex"
                                                    >
                                                        {flexRender(
                                                            header.column.columnDef.header,
                                                            header.getContext()
                                                        )}
                                                        <span
                                                            className={
                                                                header.column.getIsSorted()
                                                                    ? 'ml-2 flex-none rounded-sm bg-gray-100 text-gray-900 group-hover:bg-gray-200 dark:bg-gray-800 dark:text-white dark:group-hover:bg-gray-700'
                                                                    : 'invisible ml-2 flex-none rounded-sm text-gray-400 group-hover:visible group-focus:visible dark:text-gray-500'
                                                            }
                                                        >
                                                            <ChevronDownIcon
                                                                aria-hidden="true"
                                                                className={`size-5 ${
                                                                    header.column.getIsSorted() === 'asc'
                                                                        ? 'rotate-180'
                                                                        : ''
                                                                }`}
                                                            />
                                                        </span>
                                                    </button>
                                                ) : (
                                                    <span>
                                                        {flexRender(
                                                            header.column.columnDef.header,
                                                            header.getContext()
                                                        )}
                                                    </span>
                                                )}
                                                {index === headerGroup.headers.length - 1 && (
                                                    <span className="sr-only">{__('actions.actions')}</span>
                                                )}
                                            </th>
                                        ))}
                                    </tr>
                                ))}
                            </thead>
                            <tbody className="divide-y divide-gray-200 bg-white dark:divide-white/10 dark:bg-gray-900">
                                {table.getRowModel().rows.map(row => (
                                    <tr key={row.id} className="even:bg-gray-50 dark:even:bg-gray-800/50">
                                        {row.getVisibleCells().map((cell, index) => (
                                            <td
                                                key={cell.id}
                                                className={clsx(
                                                    'px-3 py-4 text-sm whitespace-nowrap',
                                                    index === 0
                                                        ? 'pr-3 pl-4 font-medium text-gray-900 sm:pl-3 dark:text-white'
                                                        : 'text-gray-500 dark:text-gray-400'
                                                )}
                                            >
                                                {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                            </td>
                                        ))}
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </div>
    );
};

export default Table;
