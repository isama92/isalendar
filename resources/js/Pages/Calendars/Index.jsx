import Modal from '@/Components/Modal';
import Pagination from '@/Components/Pagination';
import {
    CheckIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    LockClosedIcon,
    LockOpenIcon,
    TrashIcon,
    XMarkIcon,
} from '@heroicons/react/24/solid';
import { PlusIcon } from '@heroicons/react/24/solid/index.js';
import { Link, router } from '@inertiajs/react';
import {
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { useMemo, useState } from 'react';

const Index = ({ calendars, providers }) => {
    const [sorting, setSorting] = useState([]);
    const [columnFilters, setColumnFilters] = useState([]);
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
                header: __('calendars.provider_name'),
                id: 'provider_name',
                accessorFn: row => row.connector.provider.name,
                filterFn: (row, id, value) => {
                    if (value === 'all') return true;
                    return row.original.id.toString() === value;
                },
            },
            {
                id: 'actions',
                header: __('actions.actions'),
                cell: info => (
                    <div className="flex items-center gap-2">
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
                            className={
                                info.row.original.enabled
                                    ? 'cursor-pointer text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300'
                                    : 'cursor-pointer text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300'
                            }
                        >
                            {info.row.original.enabled ? (
                                <LockClosedIcon className="h-5 w-5" />
                            ) : (
                                <LockOpenIcon className="h-5 w-5" />
                            )}
                        </button>
                        <button
                            onClick={() => {
                                setCalendarToDelete(info.row.original);
                                setDeleteModalOpen(true);
                            }}
                            title={__('actions.delete')}
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
        data: calendars.data,
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
        <div className="rounded-lg bg-white p-6 shadow sm:p-6 dark:bg-gray-800">
            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 className="text-2xl font-semibold text-gray-900 sm:text-2xl dark:text-gray-100">
                    {__('calendars.calendars')}
                </h2>
                <Link
                    href={route('providers.create')}
                    className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                >
                    <PlusIcon aria-hidden="true" className="-ml-0.5 size-5" />
                    {__('calendars.create_calendar')}
                </Link>
            </div>

            <div className="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <input
                    type="text"
                    placeholder={__('actions.named.filter_by', { name: __('calendars.name') })}
                    value={table.getColumn('name')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('name')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
                <select
                    value={table.getColumn('enabled')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('enabled')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('actions.named.all', { name: __('calendars.calendars') })}</option>
                    <option value="true">{__('calendars.enabled_only')}</option>
                    <option value="false">{__('calendars.disabled_only')}</option>
                </select>
                <select
                    value={table.getColumn('provider_name')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('provider_name')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('actions.named.all', { name: __('providers.providers') })}</option>
                    {providers.map(provider => (
                        <option key={provider.id} value={provider.id}>
                            {provider.name}
                        </option>
                    ))}
                </select>
            </div>

            <div className="-mx-4 overflow-x-auto sm:mx-0">
                {table.getRowModel().rows.length === 0 ? (
                    <p className="px-4 text-gray-500 sm:px-0 dark:text-gray-400">
                        {__('calendars.no_calendars_found')}
                    </p>
                ) : (
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead className="bg-gray-50 dark:bg-gray-700">
                            {table.getHeaderGroups().map(headerGroup => (
                                <tr key={headerGroup.id}>
                                    {headerGroup.headers.map(header => (
                                        <th
                                            key={header.id}
                                            className="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase sm:px-6 dark:text-gray-300"
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
                                            className="px-4 py-4 text-sm whitespace-nowrap text-gray-900 sm:px-6 dark:text-gray-100"
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

            <Pagination data={calendars} routeName="calendars.index" />

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
