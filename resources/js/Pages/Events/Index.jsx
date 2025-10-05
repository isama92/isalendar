import {
    CheckIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    XMarkIcon,
} from '@heroicons/react/24/solid';
import { Link, router } from '@inertiajs/react';
import {
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import dayjs from 'dayjs';
import { useMemo, useState } from 'react';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';
import Pagination from '../../Components/Pagination';
import { formatDate, formatDateTime, formatWithFormat } from '../../utils/date';

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
                cell: info =>
                    info.row.original.all_day ? formatDate(info.getValue()) : formatDateTime(info.getValue()),
                filterFn: (row, id, value) => {
                    if (!value) return true;
                    return formatWithFormat(row.getValue(id), 'YYYY-MM-DD') === value;
                },
            },
            {
                accessorKey: 'ends_at',
                header: 'Ends At',
                cell: info => (info.row.original.all_day ? '' : formatDateTime(info.getValue())),
                filterFn: (row, id, value) => {
                    if (!value) return true;
                    return formatWithFormat(row.getValue(id), 'YYYY-MM-DD') === value;
                },
            },
            {
                id: 'actions',
                header: 'Actions',
                cell: info => (
                    <div className="flex items-center gap-2">
                        <Link
                            href={route('events.show', info.row.original.id)}
                            title="View"
                            className="text-gray-600 hover:text-gray-800"
                        >
                            <EyeIcon className="h-5 w-5" />
                        </Link>
                        <Link
                            href={route('events.edit', info.row.original.id)}
                            title="Edit"
                            className="text-blue-600 hover:text-blue-800"
                        >
                            <PencilIcon className="h-5 w-5" />
                        </Link>
                        <button
                            onClick={() => {
                                if (confirm('Are you sure you want to delete this event?')) {
                                    router.delete(route('events.destroy', info.row.original.id));
                                }
                            }}
                            title="Delete"
                            className="cursor-pointer text-red-600 hover:text-red-800"
                        >
                            <TrashIcon className="h-5 w-5" />
                        </button>
                    </div>
                ),
                enableSorting: false,
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
            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-2xl font-semibold text-gray-900">Events</h2>
                <Link
                    href={route('events.create')}
                    className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                >
                    Create Event
                </Link>
            </div>

            <div className="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                    <option value="true">All Day only</option>
                    <option value="false">Not All Day</option>
                </select>
                <DatePicker
                    selected={
                        table.getColumn('starts_at')?.getFilterValue()
                            ? dayjs(table.getColumn('starts_at')?.getFilterValue()).toDate()
                            : null
                    }
                    onChange={date =>
                        table.getColumn('starts_at')?.setFilterValue(date ? formatWithFormat(date, 'YYYY-MM-DD') : '')
                    }
                    dateFormat="dd/MM/yyyy"
                    placeholderText="Filter by starts at..."
                    isClearable
                    calendarStartDay={1}
                    withPortal
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                />
                <DatePicker
                    selected={
                        table.getColumn('ends_at')?.getFilterValue()
                            ? dayjs(table.getColumn('ends_at')?.getFilterValue()).toDate()
                            : null
                    }
                    onChange={date =>
                        table.getColumn('ends_at')?.setFilterValue(date ? formatWithFormat(date, 'YYYY-MM-DD') : '')
                    }
                    dateFormat="dd/MM/yyyy"
                    placeholderText="Filter by ends at..."
                    isClearable
                    calendarStartDay={1}
                    withPortal
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                />
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
