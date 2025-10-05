import { TrashIcon } from '@heroicons/react/24/solid';
import { Link, router } from '@inertiajs/react';
import { formatDate, formatDateTime } from '../../utils/date';

const Show = ({ event }) => {
    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this event?')) {
            router.delete(route('events.destroy', event.id));
        }
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow">
            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-2xl font-semibold text-gray-900">{event.title}</h2>
                <div className="flex gap-3">
                    <Link
                        href={route('events.edit', event.id)}
                        className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Edit
                    </Link>
                    <button
                        onClick={handleDelete}
                        title="Delete"
                        className="inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        <TrashIcon className="h-4 w-4" />
                        Delete
                    </button>
                </div>
            </div>

            <dl className="space-y-4">
                <div>
                    <dt className="text-sm font-medium text-gray-500">Type</dt>
                    <dd className="mt-1 text-sm text-gray-900">
                        {event.all_day ? 'All day event' : 'Scheduled event'}
                    </dd>
                </div>

                {!event.all_day && (
                    <>
                        <div>
                            <dt className="text-sm font-medium text-gray-500">Starts At</dt>
                            <dd className="mt-1 text-sm text-gray-900">{formatDateTime(event.starts_at)}</dd>
                        </div>

                        <div>
                            <dt className="text-sm font-medium text-gray-500">Ends At</dt>
                            <dd className="mt-1 text-sm text-gray-900">{formatDateTime(event.ends_at)}</dd>
                        </div>
                    </>
                )}

                {event.all_day && (
                    <div>
                        <dt className="text-sm font-medium text-gray-500">Date</dt>
                        <dd className="mt-1 text-sm text-gray-900">{formatDate(event.starts_at)}</dd>
                    </div>
                )}

                {event.description && (
                    <div>
                        <dt className="text-sm font-medium text-gray-500">Description</dt>
                        <dd className="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{event.description}</dd>
                    </div>
                )}
            </dl>

            <div className="mt-6">
                <Link
                    href={route('events.index')}
                    className="text-sm text-blue-600 hover:text-blue-800"
                >
                    ← Back to Events
                </Link>
            </div>
        </div>
    );
};

export default Show;
