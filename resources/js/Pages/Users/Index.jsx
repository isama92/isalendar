import Modal from '@/Components/Modal';
import Paginator from '@/Components/Tables/Paginator';
import Table from '@/Components/Tables/Table';
import { formatDate } from '@/utils/date';
import {
    ArrowPathIcon,
    EyeIcon,
    PaperAirplaneIcon,
    PencilIcon,
    PlusIcon,
    TrashIcon,
    UserGroupIcon,
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

const Index = ({ users, roles, currentUser }) => {
    const [sorting, setSorting] = useState([]);
    const [columnFilters, setColumnFilters] = useState([]);
    const [pagination, setPagination] = useState({ pageIndex: 0, pageSize: 10 });
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [restoreModalOpen, setRestoreModalOpen] = useState(false);
    const [passwordResetModalOpen, setPasswordResetModalOpen] = useState(false);
    const [userToAction, setUserToAction] = useState(null);

    const columns = useMemo(
        () => [
            {
                header: 'ID',
                accessorKey: 'id',
            },
            {
                header: __('users.first_name'),
                accessorKey: 'first_name',
            },
            {
                header: __('users.last_name'),
                accessorKey: 'last_name',
            },
            {
                header: __('users.email'),
                accessorKey: 'email',
            },
            {
                header: __('users.role'),
                id: 'role',
                accessorFn: row => row.roles?.[0]?.name || 'N/A',
                cell: info => roles.find(r => r.value === info.row.original.roles[0].name).label,
            },
            {
                header: __('users.created_at'),
                accessorKey: 'created_at',
                cell: info => formatDate(info.getValue()),
            },
            {
                header: __('users.deleted_at'),
                accessorKey: 'deleted_at',
                cell: info => (info.getValue() ? formatDate(info.getValue()) : '-'),
                filterFn: (row, id, value) => {
                    if (value === 'all') return true;
                    if (value === 'active') return !row.getValue(id);
                    if (value === 'deleted') return !!row.getValue(id);
                    return true;
                },
            },
            {
                id: 'actions',
                header: __('actions.actions'),
                cell: info => {
                    const user = info.row.original;
                    const isDeleted = !!user.deleted_at;
                    const isCurrentUser = user.id === currentUser.id;

                    return (
                        <div className="flex items-center justify-end gap-3">
                            <Link
                                href={route('users.show', user.id)}
                                title={__('actions.named.view', { name: __('users.user') })}
                                className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                <EyeIcon className="h-5 w-5" />
                                <span className="sr-only">
                                    {__('actions.view')}, {user.first_name} {user.last_name}
                                </span>
                            </Link>
                            {!isCurrentUser && isDeleted ? (
                                <button
                                    onClick={() => {
                                        setUserToAction(user);
                                        setRestoreModalOpen(true);
                                    }}
                                    title={__('actions.named.restore', { name: __('users.user') })}
                                    className="cursor-pointer text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                >
                                    <ArrowPathIcon className="h-5 w-5" />
                                    <span className="sr-only">
                                        {__('actions.restore')}, {user.first_name} {user.last_name}
                                    </span>
                                </button>
                            ) : (
                                <>
                                    <Link
                                        href={route('users.edit', user.id)}
                                        title={__('actions.named.edit', { name: __('users.user') })}
                                        className="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        <PencilIcon className="h-5 w-5" />
                                        <span className="sr-only">
                                            {__('actions.edit')}, {user.first_name} {user.last_name}
                                        </span>
                                    </Link>
                                    {!isCurrentUser && (
                                        <button
                                            onClick={() => {
                                                setUserToAction(user);
                                                setDeleteModalOpen(true);
                                            }}
                                            title={__('actions.named.delete', { name: __('users.user') })}
                                            className="cursor-pointer text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            <TrashIcon className="h-5 w-5" />
                                            <span className="sr-only">
                                                {__('actions.delete')}, {user.first_name} {user.last_name}
                                            </span>
                                        </button>
                                    )}
                                    <button
                                        onClick={() => {
                                            setUserToAction(user);
                                            setPasswordResetModalOpen(true);
                                        }}
                                        title={__('users.send_password_reset')}
                                        className="cursor-pointer text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        <PaperAirplaneIcon className="h-5 w-5" />
                                        <span className="sr-only">
                                            {__('users.send_password_reset')}, {user.first_name} {user.last_name}
                                        </span>
                                    </button>
                                </>
                            )}
                        </div>
                    );
                },
                enableSorting: false,
            },
        ],
        []
    );

    const table = useReactTable({
        data: users,
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
                    <h1 className="text-base font-semibold text-gray-900 dark:text-white">{__('users.users')}</h1>
                </div>
                <div className="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <Link
                        href={route('users.create')}
                        className="inline-flex items-center gap-x-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                    >
                        <PlusIcon aria-hidden="true" className="-ml-0.5 size-5" />
                        {__('actions.named.create', { name: __('users.user') })}
                    </Link>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <input
                    type="text"
                    placeholder={__('actions.named.filter_by', { name: __('users.first_name') })}
                    value={table.getColumn('first_name')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('first_name')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
                <input
                    type="text"
                    placeholder={__('actions.named.filter_by', { name: __('users.email') })}
                    value={table.getColumn('email')?.getFilterValue() ?? ''}
                    onChange={e => table.getColumn('email')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                />
                <select
                    value={table.getColumn('deleted_at')?.getFilterValue() ?? 'all'}
                    onChange={e => table.getColumn('deleted_at')?.setFilterValue(e.target.value)}
                    className="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="all">{__('users.all_users')}</option>
                    <option value="active">{__('common.active_only')}</option>
                    <option value="deleted">{__('common.deleted_only')}</option>
                </select>
            </div>

            <Table table={table} emptyIcon={UserGroupIcon} emptyMessage={__('users.no_users_found')} />

            <Paginator table={table} />

            <Modal
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={__('actions.named.delete', { name: __('users.user') })}
                message={__('users.delete_confirm_message')}
                confirmText={__('actions.delete')}
                cancelText={__('actions.cancel')}
                variant="danger"
                onConfirm={() => {
                    if (userToAction) {
                        router.delete(route('users.destroy', userToAction.id));
                        setDeleteModalOpen(false);
                        setUserToAction(null);
                    }
                }}
            />

            <Modal
                open={restoreModalOpen}
                onClose={() => setRestoreModalOpen(false)}
                title={__('actions.named.restore', { name: __('users.user') })}
                message={__('users.restore_confirm_message')}
                confirmText={__('actions.restore')}
                cancelText={__('actions.cancel')}
                variant="success"
                onConfirm={() => {
                    if (userToAction) {
                        router.delete(route('users.destroy', userToAction.id));
                        setRestoreModalOpen(false);
                        setUserToAction(null);
                    }
                }}
            />

            <Modal
                open={passwordResetModalOpen}
                onClose={() => setPasswordResetModalOpen(false)}
                title={__('users.send_password_reset')}
                message={__('users.send_password_reset_confirm_message')}
                confirmText={__('users.send_password_reset')}
                cancelText={__('actions.cancel')}
                variant="warning"
                onConfirm={() => {
                    if (userToAction) {
                        router.post(route('users.send-password-reset', userToAction.id));
                        setPasswordResetModalOpen(false);
                        setUserToAction(null);
                    }
                }}
            />
        </div>
    );
};

export default Index;
