import { TrashIcon } from '@heroicons/react/24/solid';
import { Link, router } from '@inertiajs/react';
import { formatDate, formatDateTime } from '../../utils/date';

const Show = ({ event }) => {
    const handleDelete = () => {
        if (confirm(__('events.delete_confirm'))) {
            router.delete(route('events.destroy', event.id));
        }
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{event.title}</h2>
                <div className="flex gap-3">
                    <Link
                        href={route('events.edit', event.id)}
                        className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        {__('common.edit')}
                    </Link>
                    <button
                        onClick={handleDelete}
                        title={__('common.delete')}
                        className="inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none"
                    >
                        <TrashIcon className="h-4 w-4" />
                        {__('common.delete')}
                    </button>
                </div>
            </div>

            <dl className="space-y-4">
                <div>
                    <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">{__('events.all_day')}</dt>
                    <dd className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {event.all_day ? __('common.yes') : __('common.no')}
                    </dd>
                </div>

                {!event.all_day && (
                    <>
                        <div>
                            <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {__('events.starts_at')}
                            </dt>
                            <dd className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {formatDateTime(event.starts_at)}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {__('events.ends_at')}
                            </dt>
                            <dd className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {formatDateTime(event.ends_at)}
                            </dd>
                        </div>
                    </>
                )}

                {event.all_day && (
                    <div>
                        <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">{__('events.date')}</dt>
                        <dd className="mt-1 text-sm text-gray-900 dark:text-gray-100">{formatDate(event.starts_at)}</dd>
                    </div>
                )}

                {event.description && (
                    <div>
                        <dt className="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {__('events.description')}
                        </dt>
                        <dd className="mt-1 text-sm whitespace-pre-wrap text-gray-900 dark:text-gray-100">
                            {event.description}
                        </dd>
                    </div>
                )}
            </dl>

            <div className="mt-6">
                <Link
                    href={route('events.index')}
                    className="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                >
                    ← {__('common.back_to')} {__('events.events')}
                </Link>
            </div>
        </div>
    );
};

export default Show;
