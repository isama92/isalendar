import CalendarSelect from '@/Components/CalendarSelect';
import Modal from '@/Components/Modal';
import ProviderLogo from '@/Components/ProviderLogo';
import Paginator from '@/Components/Tables/Paginator';
import Table from '@/Components/Tables/Table';
import {
    CheckIcon,
    DocumentDuplicateIcon,
    EyeIcon,
    PencilIcon,
    PlusIcon,
    TrashIcon,
    XMarkIcon,
} from '@heroicons/react/24/solid';
import { Link, router } from '@inertiajs/react';
import {
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { useMemo, useState } from 'react';

const Index = ({ eventTemplates, calendars }) => {
    const [sorting, setSorting] = useState([]);
    const [columnFilters, setColumnFilters] = useState([]);
    const [pagination, setPagination] = useState({ pageIndex: 0, pageSize: 10 });
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [templateToDelete, setTemplateToDelete] = useState(null);

    const columns = useMemo(
        () => [
            {
                header: __('event-templates.name'),
                accessorKey: 'name',
            },
            {
                header: __('event-templates.event_title'),
                accessorKey: 'event_title',
            },
            {
                header: __('event-templates.event_all_day'),
                accessorKey: 'event_all_day',
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
                header: __('event-templates.event_timezone'),
                accessorKey: 'event_timezone',
                cell: info => info.getValue() || '-',
            },
            {
                header: __('calendars.calendar'),
                id: 'calendar_name',
                accessorFn: row => row.calendar.name,
                cell: info => (
                    <div className="flex items-center gap-2">
                        <span>{info.getValue()}</span>
                        <ProviderLogo
                            image_path={info.row.original.calendar.provider.image_path}
                            title={info.row.original.calendar.provider.name}
                            className="h-5 w-5"
                        />
                    </div>
                ),
                filterFn: (row, id, value) => {
                    if (!value || value === 'all') return true;
                    return row.original.calendar.id.toString() === value.toString();
                },
            },
            {
                id: 'actions',
                header: __('actions.actions'),
                cell: info => (
                    <div className="flex items-center justify-end gap-3">
                        <Link
                            href={route('event-templates.show', info.row.original.id)}
                            title={__('actions.view')}
                            className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                        >
                            <EyeIcon className="h-5 w-5" />
                            <span className="sr-only">
                                {__('actions.view')}, {info.row.original.name}
                            </span>
                        </Link>
                        <Link
                            href={route('event-templates.edit', info.row.original.id)}
                            title={__('actions.edit')}
                            className="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            <PencilIcon className="h-5 w-5" />
                            <span className="sr-only">
                                {__('actions.edit')}, {info.row.original.name}
                            </span>
                        </Link>
                        <button
                            onClick={() => {
                                setTemplateToDelete(info.row.original);
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
        data: eventTemplates,
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
                        {__('event-templates.event_templates')}
                    </h1>
                </div>
                <div className="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <Link
                        href={route('event-templates.create')}
                        className="inline-flex items-center gap-x-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                    >
                        <PlusIcon aria-hidden="true" className="-ml-0.5 size-5" />
                        {__('actions.named.create', { name: __('event-templates.event_template') })}
                    </Link>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <input
                    type="text"
                    placeholder={__('actions.named.filter_by', { name: __('event-templates.name') })}
                    value={table.getColumn('name')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('name')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
                <input
                    type="text"
                    placeholder={__('actions.named.filter_by', { name: __('event-templates.event_title') })}
                    value={table.getColumn('event_title')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('event_title')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
                <select
                    value={table.getColumn('event_all_day')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('event_all_day')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('event-templates.all_templates')}</option>
                    <option value="true">{__('event-templates.all_day_only')}</option>
                    <option value="false">{__('event-templates.not_all_day')}</option>
                </select>
                <CalendarSelect
                    value={table.getColumn('calendar_name')?.getFilterValue() ?? 'all'}
                    onChange={value => table.getColumn('calendar_name')?.setFilterValue(value)}
                    calendars={calendars}
                    allowAll={true}
                    allText={__('actions.named.all', { name: __('calendars.calendar') })}
                />
            </div>

            <Table
                table={table}
                emptyIcon={DocumentDuplicateIcon}
                emptyMessage={__('event-templates.no_templates_found')}
            />

            <Paginator table={table} />

            <Modal
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={__('actions.named.delete', { name: __('event-templates.event_template') })}
                message={__('event-templates.delete_confirm_message')}
                confirmText={__('actions.delete')}
                cancelText={__('actions.cancel')}
                variant="danger"
                onConfirm={() => {
                    if (templateToDelete) {
                        router.delete(route('event-templates.destroy', templateToDelete.id));
                        setDeleteModalOpen(false);
                        setTemplateToDelete(null);
                    }
                }}
            />
        </div>
    );
};

export default Index;
