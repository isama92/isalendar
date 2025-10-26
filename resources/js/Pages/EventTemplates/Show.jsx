import Modal from '@/Components/Modal';
import ProviderLogo from '@/Components/ProviderLogo';
import { PencilIcon, TrashIcon } from '@heroicons/react/20/solid';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

const Show = ({ eventTemplate }) => {
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);

    const handleDelete = () => {
        setDeleteModalOpen(true);
    };

    return (
        <div className="rounded-lg bg-white shadow dark:bg-gray-800">
            <div className="flex items-center justify-between px-4 py-6 sm:px-6">
                <div>
                    <h3 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                        {__('event-templates.template_details')}
                    </h3>
                    <p className="mt-1 max-w-2xl text-sm/6 text-gray-500 dark:text-gray-400">
                        {__('event-templates.template_information')}
                    </p>
                </div>
                <div className="flex gap-3">
                    <Link
                        href={route('event-templates.edit', eventTemplate.id)}
                        className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                    >
                        <PencilIcon aria-hidden="true" className="-ml-0.5 size-5" />
                        {__('actions.edit')}
                    </Link>
                    <button
                        onClick={handleDelete}
                        type="button"
                        className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-red-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                    >
                        <TrashIcon aria-hidden="true" className="-ml-0.5 size-5" />
                        {__('actions.delete')}
                    </button>
                </div>
            </div>
            <div className="border-t border-gray-100 dark:border-white/10">
                <dl className="grid grid-cols-1 sm:grid-cols-2">
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                            {__('event-templates.name')}
                        </dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                            {eventTemplate.name}
                        </dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                            {__('calendars.calendar')}
                        </dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                            <div className="flex items-center gap-2">
                                <span>{eventTemplate.calendar.name}</span>
                                <ProviderLogo
                                    image_path={eventTemplate.calendar.provider.image_path}
                                    title={eventTemplate.calendar.provider.name}
                                    className="h-5 w-5"
                                />
                            </div>
                        </dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                            {__('event-templates.event_title')}
                        </dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                            {eventTemplate.event_title}
                        </dd>
                    </div>
                    <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                            {__('event-templates.event_all_day')}
                        </dt>
                        <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                            {eventTemplate.event_all_day ? __('actions.yes') : __('actions.no')}
                        </dd>
                    </div>
                    {!eventTemplate.event_all_day && eventTemplate.event_timezone && (
                        <div className="border-t border-gray-100 px-4 py-6 sm:col-span-1 sm:px-6 dark:border-white/10">
                            <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                {__('event-templates.event_timezone')}
                            </dt>
                            <dd className="mt-1 text-sm/6 text-gray-700 sm:mt-2 dark:text-gray-400">
                                {eventTemplate.event_timezone}
                            </dd>
                        </div>
                    )}
                    {eventTemplate.event_description && (
                        <div className="border-t border-gray-100 px-4 py-6 sm:col-span-2 sm:px-6 dark:border-white/10">
                            <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                {__('event-templates.event_description')}
                            </dt>
                            <dd
                                className="prose prose-sm dark:prose-invert mt-1 max-w-none text-gray-700 sm:mt-2 dark:text-gray-400"
                                dangerouslySetInnerHTML={{ __html: eventTemplate.event_description }}
                            />
                        </div>
                    )}
                </dl>
            </div>

            <Modal
                open={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
                title={__('actions.named.delete', { name: __('event-templates.event_template') })}
                message={__('event-templates.delete_confirm_message')}
                confirmText={__('actions.delete')}
                cancelText={__('actions.cancel')}
                variant="danger"
                onConfirm={() => {
                    router.delete(route('event-templates.destroy', eventTemplate.id));
                    setDeleteModalOpen(false);
                }}
            />
        </div>
    );
};

export default Show;
