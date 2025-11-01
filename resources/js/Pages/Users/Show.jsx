import Modal from '@/Components/Modal';
import { formatDateTime } from '@/Utils/date';
import { ArrowPathIcon, PaperAirplaneIcon, PencilIcon, TrashIcon } from '@heroicons/react/20/solid';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

const Show = ({ user, roles, currentUser }) => {
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [restoreModalOpen, setRestoreModalOpen] = useState(false);
    const [passwordResetModalOpen, setPasswordResetModalOpen] = useState(false);
    const isDeleted = !!user.deleted_at;
    const isCurrentUser = user.id === currentUser.id;

    const role = roles.find(r => r.value === user.roles[0].name);

    const handleDelete = () => {
        setDeleteModalOpen(true);
    };

    const handleRestore = () => {
        setRestoreModalOpen(true);
    };

    const handleSendPasswordReset = () => {
        setPasswordResetModalOpen(true);
    };

    return (
        <div className="rounded-lg bg-white shadow dark:bg-gray-800">
            <div className="flex items-center justify-between px-4 py-6 sm:px-6">
                <div>
                    <h3 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                        {__('users.user_details')}
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm/6 text-gray-500 dark:text-gray-400">
                        {__('users.user_information')}
                    </p>
                </div>
                <div className="flex gap-3">
                    {!isCurrentUser && isDeleted ? (
                        <button
                            onClick={handleRestore}
                            type="button"
                            className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-green-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-green-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600"
                        >
                            <ArrowPathIcon aria-hidden="true" className="-ml-0.5 size-5" />
                            {__('actions.restore')}
                        </button>
                    ) : (
                        <>
                            <Link
                                href={route('users.edit', user.id)}
                                className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                            >
                                <PencilIcon aria-hidden="true" className="-ml-0.5 size-5" />
                                {__('actions.edit')}
                            </Link>
                            <button
                                onClick={handleSendPasswordReset}
                                type="button"
                                className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            >
                                <PaperAirplaneIcon aria-hidden="true" className="-ml-0.5 size-5" />
                                {__('users.send_password_reset')}
                            </button>
                            {!isCurrentUser && (
                                <button
                                    onClick={handleDelete}
                                    type="button"
                                    className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-red-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                                >
                                    <TrashIcon aria-hidden="true" className="-ml-0.5 size-5" />
                                    {__('actions.delete')}
                                </button>
                            )}
                        </>
                    )}
                </div>
            </div>
            <div className="border-t border-gray-100 dark:border-white/10">
                <dl className="grid grid-cols-1 sm:grid-cols-2">
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                            {__('users.first_name')}
                        </dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">{user.first_name}</dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">{__('users.last_name')}</dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">{user.last_name}</dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">{__('users.email')}</dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">{user.email}</dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">{__('users.role')}</dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">{role.label}</dd>
                    </div>
                    {user.email_verified_at && (
                        <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                            <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                {__('users.email_verified_at')}
                            </dt>
                            <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                                {formatDateTime(user.email_verified_at)}
                            </dd>
                        </div>
                    )}
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                            {__('users.created_at')}
                        </dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                            {formatDateTime(user.created_at)}
                        </dd>
                    </div>
                </dl>
            </div>

            <Modal
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={__('actions.named.delete', { name: __('users.user') })}
                message={__('users.delete_confirm_message')}
                confirmText={__('actions.delete')}
                cancelText={__('actions.cancel')}
                variant="danger"
                onConfirm={() => {
                    router.delete(route('users.destroy', user.id));
                    setDeleteModalOpen(false);
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
                    router.delete(route('users.destroy', user.id));
                    setRestoreModalOpen(false);
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
                    router.post(route('users.send-password-reset', user.id));
                    setPasswordResetModalOpen(false);
                }}
            />
        </div>
    );
};

export default Show;
