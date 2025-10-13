import Modal from '@/Components/Modal';
import Pagination from '@/Components/Tables/Pagination';
import Table from '@/Components/Tables/Table';
import {
    formatDate,
    formatDateTimeWithTimezone,
    formatWithFormat,
    formatWithFormatAndTimezone,
    subtractDays,
} from '@/utils/date';
import { CheckIcon, EyeIcon, PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/react/24/solid';
import { Link, router } from '@inertiajs/react';
import {
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import dayjs from 'dayjs';
import { useMemo, useState } from 'react';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';

const Index = ({ events, providers }) => {
    const [sorting, setSorting] = useState([]);
    const [columnFilters, setColumnFilters] = useState([]);
    const [pagination, setPagination] = useState({ pageIndex: 0, pageSize: 10 });
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [eventToDelete, setEventToDelete] = useState(null);

    const columns = useMemo(
        () => [
            {
                header: __('events.title'),
                accessorKey: 'title',
            },
            {
                header: __('events.all_day'),
                accessorKey: 'all_day',
                enableSorting: false,
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
                header: __('events.timezone'),
                accessorKey: 'timezone',
            },
            {
                header: __('events.starts_at'),
                accessorKey: 'starts_at',
                cell: info =>
                    info.row.original.all_day
                        ? formatDate(info.getValue())
                        : formatDateTimeWithTimezone(info.getValue(), info.row.original.timezone),
                filterFn: (row, id, value) => {
                    if (!value) return true;
                    const timezone = row.original.timezone;
                    return timezone
                        ? formatWithFormatAndTimezone(row.getValue(id), 'YYYY-MM-DD', timezone) === value
                        : formatWithFormat(row.getValue(id), 'YYYY-MM-DD') === value;
                },
            },
            {
                header: __('events.ends_at'),
                accessorKey: 'ends_at',
                cell: info =>
                    info.row.original.all_day
                        ? formatDate(subtractDays(info.getValue(), 1, 'YYYY-MM-DD'))
                        : formatDateTimeWithTimezone(info.getValue(), info.row.original.timezone),
                filterFn: (row, id, value) => {
                    if (!value) return true;
                    const timezone = row.original.timezone;
                    return timezone
                        ? formatWithFormatAndTimezone(row.getValue(id), 'YYYY-MM-DD', timezone) === value
                        : formatWithFormat(row.getValue(id), 'YYYY-MM-DD') === value;
                },
            },
            {
                header: __('events.calendar_name'),
                id: 'calendar_name',
                accessorFn: row => row.calendar.name,
            },
            {
                header: __('providers.provider'),
                id: 'provider_name',
                enableSorting: false,
                accessorFn: row => row.calendar.provider.name,
                filterFn: (row, id, value) => {
                    if (value === 'all') return true;
                    return row.original.calendar.provider.id.toString() === value;
                },
            },
            {
                id: 'actions',
                header: __('actions.actions'),
                cell: info => (
                    <div className="flex items-center justify-end gap-3">
                        <Link
                            href={route('events.show', info.row.original.id)}
                            title={__('actions.view')}
                            className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                        >
                            <EyeIcon className="h-5 w-5" />
                            <span className="sr-only">
                                {__('actions.view')}, {info.row.original.title}
                            </span>
                        </Link>
                        <Link
                            href={route('events.edit', info.row.original.id)}
                            title={__('actions.edit')}
                            className="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            <PencilIcon className="h-5 w-5" />
                            <span className="sr-only">
                                {__('actions.edit')}, {info.row.original.title}
                            </span>
                        </Link>
                        <button
                            onClick={() => {
                                setEventToDelete(info.row.original);
                                setDeleteModalOpen(true);
                            }}
                            title={__('actions.delete')}
                            className="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                        >
                            <TrashIcon className="h-5 w-5" />
                            <span className="sr-only">
                                {__('actions.delete')}, {info.row.original.title}
                            </span>
                        </button>
                    </div>
                ),
                enableSorting: false,
            },
        ],
        []
    );

    const table = useReactTable({
        data: events,
        columns,
        state: {
            sorting,
            columnFilters,
            pagination,
        },
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        onPaginationChange: setPagination,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
    });

    return (
        <div className="px-4 sm:px-6 lg:px-8">
            <div className="sm:flex sm:items-center">
                <div className="sm:flex-auto">
                    <h1 className="text-base font-semibold text-gray-900 dark:text-white">{__('events.events')}</h1>
                </div>
                <div className="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <Link
                        href={route('events.create')}
                        className="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                    >
                        {__('actions.named.create', { name: __('events.event') })}
                    </Link>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <input
                    type="text"
                    placeholder={__('actions.named.filter_by', { name: __('events.title') })}
                    value={table.getColumn('title')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('title')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
                <select
                    value={table.getColumn('all_day')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('all_day')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('events.all_events')}</option>
                    <option value="true">{__('events.all_day_only')}</option>
                    <option value="false">{__('events.not_all_day')}</option>
                </select>
                <select
                    value={table.getColumn('provider_name')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('provider_name')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('actions.named.all', { name: __('providers.provider') })}</option>
                    {providers.map(provider => (
                        <option key={provider.id} value={provider.id}>
                            {provider.name}
                        </option>
                    ))}
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
                    placeholderText={__('actions.named.filter_by', { name: __('events.starts_at') })}
                    isClearable
                    calendarStartDay={1}
                    withPortal
                    className="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
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
                    placeholderText={__('actions.named.filter_by', { name: __('events.ends_at') })}
                    isClearable
                    calendarStartDay={1}
                    withPortal
                    className="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
            </div>

            <Table table={table} emptyMessage={__('events.no_events_found')} />

            <Pagination table={table} />

            <Modal
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={__('actions.named.delete', { name: __('events.event') })}
                message={__('events.delete_confirm_message')}
                confirmText={__('actions.delete')}
                cancelText={__('actions.cancel')}
                variant="danger"
                onConfirm={() => {
                    if (eventToDelete) {
                        router.delete(route('events.destroy', eventToDelete.id));
                        setDeleteModalOpen(false);
                        setEventToDelete(null);
                    }
                }}
            />
        </div>
    );
};

export default Index;
