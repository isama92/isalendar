import Modal from '@/Components/Modal';
import Paginator from '@/Components/Tables/Paginator';
import Table from '@/Components/Tables/Table';
import { CalendarIcon, CheckIcon, LockClosedIcon, LockOpenIcon, TrashIcon, XMarkIcon } from '@heroicons/react/24/solid';
import { Link, router } from '@inertiajs/react';
import {
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import clsx from 'clsx';
import { useMemo, useState } from 'react';

const Index = ({ calendars, providers }) => {
    const [sorting, setSorting] = useState([]);
    const [columnFilters, setColumnFilters] = useState([]);
    const [pagination, setPagination] = useState({ pageIndex: 0, pageSize: 10 });
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [calendarToDelete, setCalendarToDelete] = useState(null);
    const [toggleModalOpen, setToggleModalOpen] = useState(false);
    const [calendarToToggle, setCalendarToToggle] = useState(null);

    const columns = useMemo(
        () => [
            {
                header: __('calendars.name'),
                accessorKey: 'name',
            },
            {
                header: __('calendars.enabled'),
                accessorKey: 'enabled',
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
                header: __('calendars.events_count'),
                id: 'events_count',
                accessorFn: row => row.events_count,
            },
            {
                header: __('providers.provider'),
                id: 'provider_name',
                enableSorting: false,
                accessorFn: row => row.provider.name,
                filterFn: (row, id, value) => {
                    if (value === 'all') return true;
                    return row.original.provider.id.toString() === value;
                },
            },
            {
                id: 'actions',
                header: __('actions.actions'),
                cell: info => (
                    <div className="flex items-center justify-end gap-3">
                        <button
                            onClick={() => {
                                setCalendarToToggle(info.row.original);
                                setToggleModalOpen(true);
                            }}
                            title={
                                info.row.original.enabled
                                    ? __('calendars.disable_calendar_sync')
                                    : __('calendars.enable_calendar_sync')
                            }
                            className={clsx(
                                'cursor-pointer',
                                info.row.original.enabled
                                    ? 'text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300'
                                    : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300'
                            )}
                        >
                            {info.row.original.enabled ? (
                                <LockClosedIcon className="h-5 w-5" />
                            ) : (
                                <LockOpenIcon className="h-5 w-5" />
                            )}
                            <span className="sr-only">
                                {info.row.original.enabled
                                    ? __('calendars.disable_calendar_sync')
                                    : __('calendars.enable_calendar_sync')}
                                , {info.row.original.name}
                            </span>
                        </button>
                        <button
                            onClick={() => {
                                setCalendarToDelete(info.row.original);
                                setDeleteModalOpen(true);
                            }}
                            title={__('actions.delete')}
                            className="cursor-pointer text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                        >
                            <TrashIcon className="h-5 w-5" />
                            <span className="sr-only">
                                {__('actions.delete')}, {info.row.original.name}
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
        data: calendars,
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
                    <h1 className="text-base font-semibold text-gray-900 dark:text-white">
                        {__('calendars.calendars')}
                    </h1>
                </div>
                <div className="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <Link
                        href={route('providers.create')}
                        className="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                    >
                        {__('actions.named.create', { name: __('calendars.calendar') })}
                    </Link>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <input
                    type="text"
                    placeholder={__('actions.named.filter_by', { name: __('calendars.name') })}
                    value={table.getColumn('name')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('name')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
                <select
                    value={table.getColumn('enabled')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('enabled')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('actions.named.all', { name: __('calendars.calendars') })}</option>
                    <option value="true">{__('calendars.enabled_only')}</option>
                    <option value="false">{__('calendars.disabled_only')}</option>
                </select>
                <select
                    value={table.getColumn('provider_name')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('provider_name')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('actions.named.all', { name: __('providers.providers') })}</option>
                    {providers.map(provider => (
                        <option key={provider.id} value={provider.id}>
                            {provider.name}
                        </option>
                    ))}
                </select>
            </div>

            <Table table={table} emptyIcon={CalendarIcon} emptyMessage={__('calendars.no_calendars_found')} />

            <Paginator table={table} />

            <Modal
                open={toggleModalOpen}
                onClose={() => setToggleModalOpen(false)}
                title={
                    calendarToToggle?.enabled
                        ? __('calendars.disable_calendar_sync')
                        : __('calendars.enable_calendar_sync')
                }
                message={
                    calendarToToggle?.enabled
                        ? __('calendars.disable_confirm_message')
                        : __('calendars.enable_confirm_message')
                }
                confirmText={calendarToToggle?.enabled ? __('actions.disable') : __('actions.enable')}
                cancelText={__('actions.cancel')}
                variant="warning"
                onConfirm={() => {
                    if (calendarToToggle) {
                        router.put(route('calendars.update', calendarToToggle.id), {
                            enabled: !calendarToToggle.enabled,
                        });
                        setToggleModalOpen(false);
                        setCalendarToToggle(null);
                    }
                }}
            />

            <Modal
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={__('calendars.delete_calendar')}
                message={__('calendars.delete_confirm_message')}
                confirmText={__('actions.delete')}
                cancelText={__('actions.cancel')}
                variant="danger"
                onConfirm={() => {
                    if (calendarToDelete) {
                        router.delete(route('calendars.destroy', calendarToDelete.id));
                        setDeleteModalOpen(false);
                        setCalendarToDelete(null);
                    }
                }}
            />
        </div>
    );
};

export default Index;
