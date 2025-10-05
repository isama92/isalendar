import { Link } from '@inertiajs/react';

const Index = ({ events }) => {
    const formatDate = dateString => {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow">
            <h2 className="mb-6 text-2xl font-semibold text-gray-900">Events</h2>

            <div className="space-y-4">
                {events.data.length === 0 ? (
                    <p className="text-gray-500">No events found.</p>
                ) : (
                    events.data.map(event => (
                        <div key={event.id} className="rounded-lg border border-gray-200 p-4 hover:bg-gray-50">
                            <div className="flex items-start justify-between">
                                <div className="flex-1">
                                    <h3 className="text-lg font-medium text-gray-900">{event.title}</h3>
                                    <div className="mt-1 text-sm text-gray-500">
                                        {event.all_day ? (
                                            <span>All day</span>
                                        ) : (
                                            <span>
                                                {formatDate(event.starts_at)} - {formatDate(event.ends_at)}
                                            </span>
                                        )}
                                    </div>
                                    {event.description && (
                                        <p className="mt-2 text-sm text-gray-600">{event.description}</p>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>

            {/* Pagination */}
            {events.links.length > 3 && (
                <div className="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                    <div className="flex flex-1 justify-between sm:hidden">
                        {events.prev_page_url ? (
                            <Link
                                href={events.prev_page_url}
                                className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Previous
                            </Link>
                        ) : (
                            <span className="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400">
                                Previous
                            </span>
                        )}
                        {events.next_page_url ? (
                            <Link
                                href={events.next_page_url}
                                className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Next
                            </Link>
                        ) : (
                            <span className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400">
                                Next
                            </span>
                        )}
                    </div>
                    <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p className="text-sm text-gray-700">
                                Showing <span className="font-medium">{events.from}</span> to{' '}
                                <span className="font-medium">{events.to}</span> of{' '}
                                <span className="font-medium">{events.total}</span> results
                            </p>
                        </div>
                        <div>
                            <nav className="isolate inline-flex -space-x-px rounded-md shadow-sm">
                                {events.links.map((link, index) => (
                                    <Link
                                        key={index}
                                        href={link.url || '#'}
                                        preserveScroll
                                        className={`relative inline-flex items-center px-4 py-2 text-sm font-medium ${
                                            link.active
                                                ? 'z-10 bg-blue-600 text-white focus:z-20'
                                                : 'bg-white text-gray-700 hover:bg-gray-50'
                                        } ${index === 0 ? 'rounded-l-md' : ''} ${
                                            index === events.links.length - 1 ? 'rounded-r-md' : ''
                                        } ${!link.url ? 'cursor-not-allowed opacity-50' : ''} border border-gray-300`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </nav>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Index;
