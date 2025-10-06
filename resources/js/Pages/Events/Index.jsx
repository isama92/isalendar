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
                header: __('events.title'),
                cell: info => info.getValue(),
            },
            {
                accessorKey: 'all_day',
                header: __('events.all_day'),
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
                header: __('events.starts_at'),
                cell: info =>
                    info.row.original.all_day ? formatDate(info.getValue()) : formatDateTime(info.getValue()),
                filterFn: (row, id, value) => {
                    if (!value) return true;
                    return formatWithFormat(row.getValue(id), 'YYYY-MM-DD') === value;
                },
            },
            {
                accessorKey: 'ends_at',
                header: __('events.ends_at'),
                cell: info => (info.row.original.all_day ? '' : formatDateTime(info.getValue())),
                filterFn: (row, id, value) => {
                    if (!value) return true;
                    return formatWithFormat(row.getValue(id), 'YYYY-MM-DD') === value;
                },
            },
            {
                id: 'actions',
                header: __('common.actions'),
                cell: info => (
                    <div className="flex items-center gap-2">
                        <Link
                            href={route('events.show', info.row.original.id)}
                            title={__('common.view')}
                            className="text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100"
                        >
                            <EyeIcon className="h-5 w-5" />
                        </Link>
                        <Link
                            href={route('events.edit', info.row.original.id)}
                            title={__('common.edit')}
                            className="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            <PencilIcon className="h-5 w-5" />
                        </Link>
                        <button
                            onClick={() => {
                                if (confirm(__('events.delete_confirm'))) {
                                    router.delete(route('events.destroy', info.row.original.id));
                                }
                            }}
                            title={__('common.delete')}
                            className="cursor-pointer text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
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
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{__('events.events')}</h2>
                <Link
                    href={route('events.create')}
                    className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                >
                    {__('events.create_event')}
                </Link>
            </div>

            <div className="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <input
                    type="text"
                    placeholder={__('events.filter_by_title')}
                    value={table.getColumn('title')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('title')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
                <select
                    value={table.getColumn('all_day')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('all_day')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('events.all_events')}</option>
                    <option value="true">{__('events.all_day_only')}</option>
                    <option value="false">{__('events.not_all_day')}</option>
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
                    placeholderText={__('events.filter_by_starts_at')}
                    isClearable
                    calendarStartDay={1}
                    withPortal
                    className="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
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
                    placeholderText={__('events.filter_by_ends_at')}
                    isClearable
                    calendarStartDay={1}
                    withPortal
                    className="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
            </div>

            <div className="overflow-x-auto">
                {table.getRowModel().rows.length === 0 ? (
                    <p className="text-gray-500 dark:text-gray-400">{__('events.no_events_found')}</p>
                ) : (
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead className="bg-gray-50 dark:bg-gray-700">
                            {table.getHeaderGroups().map(headerGroup => (
                                <tr key={headerGroup.id}>
                                    {headerGroup.headers.map(header => (
                                        <th
                                            key={header.id}
                                            className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
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
                        <tbody className="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            {table.getRowModel().rows.map(row => (
                                <tr key={row.id} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    {row.getVisibleCells().map(cell => (
                                        <td
                                            key={cell.id}
                                            className="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100"
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

            <Pagination data={events} routeName="events.index" />
        </div>
    );
};

export default Index;
