import Modal from '@/Components/Modal';
import { formatDate, formatDateTimeWithTimezone, subtractDays } from '@/utils/date';
import { PencilIcon, TrashIcon } from '@heroicons/react/20/solid';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

const Show = ({ event }) => {
    const formatStartsAt = event.all_day ? formatDate : date => formatDateTimeWithTimezone(date, event.timezone);
    const formatEndsAt = event.all_day
        ? date => formatDate(subtractDays(date, 1, 'YYYY-MM-DD'))
        : date => formatDateTimeWithTimezone(date, event.timezone);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);

    const handleDelete = () => {
        setDeleteModalOpen(true);
    };

    return (
        <div className="rounded-lg bg-white shadow dark:bg-gray-800">
            <div className="flex items-center justify-between px-4 py-6 sm:px-6">
                <div>
                    <h3 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                        {__('events.event_details')}
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm/6 text-gray-500 dark:text-gray-400">
                        {__('events.event_information')}
                    </p>
                </div>
                <div className="flex gap-3">
                    <Link
                        href={route('events.edit', event.id)}
                        className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                    >
                        <PencilIcon aria-hidden="true" className="-ml-0.5 size-5" />
                        {__('common.edit')}
                    </Link>
                    <button
                        onClick={handleDelete}
                        type="button"
                        className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-red-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                    >
                        <TrashIcon aria-hidden="true" className="-ml-0.5 size-5" />
                        {__('common.delete')}
                    </button>
                </div>
            </div>
            <div className="border-t border-gray-100 dark:border-white/10">
                <dl className="grid grid-cols-1 sm:grid-cols-2">
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">{__('events.title')}</dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">{event.title}</dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                            {__('calendars.calendar')}
                        </dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                            {event.calendar.name}
                        </dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                            {__('events.starts_at')}
                        </dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                            {formatStartsAt(event.starts_at)}
                        </dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">{__('events.ends_at')}</dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                            {formatEndsAt(event.ends_at)}
                        </dd>
                    </div>
                    {!event.all_day && event.timezone && (
                        <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                            <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                {__('events.timezone')}
                            </dt>
                            <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                                {event.timezone}
                            </dd>
                        </div>
                    )}
                    {event.description && (
                        <div className="border-t border-gray-100 px-4 py-6 sm:col-span-2 sm:px-6 dark:border-white/10">
                            <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                {__('events.description')}
                            </dt>
                            <dd className="mt-1 text-sm/6 whitespace-pre-wrap text-gray-700 sm:mt-2 dark:text-gray-400">
                                {event.description}
                            </dd>
                        </div>
                    )}
                </dl>
            </div>

            <Modal
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={__('events.delete_event')}
                message={__('events.delete_confirm_message')}
                confirmText={__('common.delete')}
                cancelText={__('common.cancel')}
                variant="danger"
                onConfirm={() => {
                    router.delete(route('events.destroy', event.id));
                    setDeleteModalOpen(false);
                }}
            />
        </div>
    );
};

export default Show;
