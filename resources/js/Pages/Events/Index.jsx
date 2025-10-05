import { CheckIcon, ChevronDownIcon, ChevronUpIcon, XMarkIcon } from '@heroicons/react/24/solid';
import {
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { useMemo, useState } from 'react';
import Pagination from '../../Components/Pagination';
import { formatDateTime } from '../../utils/date';

const Index = ({ events }) => {
    const [sorting, setSorting] = useState([]);
    const [columnFilters, setColumnFilters] = useState([]);

    const columns = useMemo(
        () => [
            {
                accessorKey: 'title',
                header: 'Title',
                cell: info => info.getValue(),
            },
            {
                accessorKey: 'all_day',
                header: 'All Day',
                cell: info =>
                    info.getValue() ? (
                        <CheckIcon className="h-5 w-5 text-green-500" />
                    ) : (
                        <XMarkIcon className="h-5 w-5 text-gray-400" />
                    ),
                filterFn: (row, id, value) => {
                    if (value === 'all') return true;
                    return row.getValue(id) === (value === 'true');
                },
            },
            {
                accessorKey: 'starts_at',
                header: 'Starts At',
                cell: info => (info.row.original.all_day ? '' : formatDateTime(info.getValue())),
            },
            {
                accessorKey: 'ends_at',
                header: 'Ends At',
                cell: info => (info.row.original.all_day ? '' : formatDateTime(info.getValue())),
            },
        ],
        []
    );

    const table = useReactTable({
        data: events.data,
        columns,
        state: {
            sorting,
            columnFilters,
        },
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
    });

    return (
        <div className="rounded-lg bg-white p-6 shadow">
            <h2 className="mb-6 text-2xl font-semibold text-gray-900">Events</h2>

            <div className="mb-4 flex gap-4">
                <input
                    type="text"
                    placeholder="Filter by title..."
                    value={table.getColumn('title')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('title')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                />
                <select
                    value={table.getColumn('all_day')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('all_day')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                >
                    <option value="all">All events</option>
                    <option value="true">All day only</option>
                    <option value="false">Not all day</option>
                </select>
            </div>

            <div className="overflow-x-auto">
                {table.getRowModel().rows.length === 0 ? (
                    <p className="text-gray-500">No events found.</p>
                ) : (
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            {table.getHeaderGroups().map(headerGroup => (
                                <tr key={headerGroup.id}>
                                    {headerGroup.headers.map(header => (
                                        <th
                                            key={header.id}
                                            className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase"
                                        >
                                            {header.isPlaceholder ? null : (
                                                <div
                                                    className={`flex items-center gap-2 ${
                                                        header.column.getCanSort() ? 'cursor-pointer select-none' : ''
                                                    }`}
                                                    onClick={header.column.getToggleSortingHandler()}
                                                >
                                                    {flexRender(header.column.columnDef.header, header.getContext())}
                                                    {{
                                                        asc: <ChevronUpIcon className="h-4 w-4" />,
                                                        desc: <ChevronDownIcon className="h-4 w-4" />,
                                                    }[header.column.getIsSorted()] ?? null}
                                                </div>
                                            )}
                                        </th>
                                    ))}
                                </tr>
                            ))}
                        </thead>
                        <tbody className="divide-y divide-gray-200 bg-white">
                            {table.getRowModel().rows.map(row => (
                                <tr key={row.id} className="hover:bg-gray-50">
                                    {row.getVisibleCells().map(cell => (
                                        <td key={cell.id} className="px-6 py-4 text-sm whitespace-nowrap text-gray-900">
                                            {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                        </td>
                                    ))}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>

            <Pagination data={events} routeName="events.index" />
        </div>
    );
};

export default Index;
