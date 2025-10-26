import {
    Dialog,
    DialogBackdrop,
    DialogPanel,
    DialogTitle,
    Listbox,
    ListboxButton,
    ListboxOption,
    ListboxOptions,
} from '@headlessui/react';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/react/24/solid';
import clsx from 'clsx';
import { useState } from 'react';

const EventTemplateSelectModal = ({ open, onClose, eventTemplates, onConfirm }) => {
    const [selectedTemplateId, setSelectedTemplateId] = useState(null);

    const selectedTemplate = eventTemplates.find(template => template.id === selectedTemplateId);

    const handleConfirm = () => {
        if (selectedTemplate) {
            onConfirm(selectedTemplate);
            setSelectedTemplateId(null);
        }
    };

    const handleClose = () => {
        setSelectedTemplateId(null);
        onClose();
    };

    return (
        <Dialog open={open} onClose={handleClose} className="relative z-10">
            <DialogBackdrop
                transition
                className="fixed inset-0 bg-gray-500/75 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in dark:bg-gray-900/75"
            />

            <div className="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div className="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <DialogPanel
                        transition
                        className="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-2xl sm:p-6 data-closed:sm:translate-y-0 data-closed:sm:scale-95 dark:bg-gray-800"
                    >
                        <div>
                            <div className="mt-3 text-center sm:mt-0 sm:text-left">
                                <DialogTitle
                                    as="h3"
                                    className="text-base font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    {__('events.use_event_template')}
                                </DialogTitle>

                                <div className="mt-6">
                                    <label
                                        htmlFor="template-select"
                                        className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                    >
                                        {__('events.select_template')}
                                    </label>
                                    <div className="mt-2">
                                        <Listbox value={selectedTemplateId} onChange={setSelectedTemplateId}>
                                            <div className="relative">
                                                <ListboxButton
                                                    id="template-select"
                                                    className="relative w-full cursor-default rounded-md border border-gray-300 bg-white py-2 pr-10 pl-3 text-left text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                                >
                                                    <span className="block min-h-5 truncate">
                                                        {selectedTemplate
                                                            ? selectedTemplate.name
                                                            : __('events.select_template')}
                                                    </span>
                                                    <span className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                        <ChevronUpDownIcon
                                                            aria-hidden="true"
                                                            className="size-5 text-gray-400 dark:text-gray-500"
                                                        />
                                                    </span>
                                                </ListboxButton>

                                                <ListboxOptions
                                                    transition
                                                    className="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-sm shadow-lg outline-1 outline-black/5 data-leave:transition data-leave:duration-100 data-leave:ease-in data-closed:data-leave:opacity-0 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
                                                >
                                                    {eventTemplates.map(template => (
                                                        <ListboxOption
                                                            key={template.id}
                                                            value={template.id}
                                                            className="group relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none data-focus:bg-indigo-600 data-focus:text-white data-focus:outline-hidden dark:text-white dark:data-focus:bg-indigo-500"
                                                        >
                                                            <span className="block truncate font-normal group-data-selected:font-semibold">
                                                                {template.name}
                                                            </span>
                                                            <span className="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 group-not-data-selected:hidden group-data-focus:text-white dark:text-indigo-400">
                                                                <CheckIcon aria-hidden="true" className="size-5" />
                                                            </span>
                                                        </ListboxOption>
                                                    ))}
                                                </ListboxOptions>
                                            </div>
                                        </Listbox>
                                    </div>
                                </div>

                                {selectedTemplate && (
                                    <div className="mt-6">
                                        <h4 className="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {__('events.template_preview')}
                                        </h4>
                                        <div className="mt-4 rounded-lg border border-gray-200 dark:border-white/10">
                                            <dl className="grid grid-cols-1 sm:grid-cols-2">
                                                <div className="border-t border-gray-100 px-4 py-4 sm:col-span-1 sm:px-4 dark:border-white/10">
                                                    <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                                        {__('calendars.calendar')}
                                                    </dt>
                                                    <dd className="mt-1 text-sm/6 text-gray-700 dark:text-gray-400">
                                                        {selectedTemplate.calendar?.name || '-'}
                                                    </dd>
                                                </div>
                                                <div className="border-t border-gray-100 px-4 py-4 sm:col-span-1 sm:px-4 dark:border-white/10">
                                                    <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                                        {__('events.title')}
                                                    </dt>
                                                    <dd className="mt-1 text-sm/6 text-gray-700 dark:text-gray-400">
                                                        {selectedTemplate.event_title || '-'}
                                                    </dd>
                                                </div>
                                                <div className="border-t border-gray-100 px-4 py-4 sm:col-span-1 sm:px-4 dark:border-white/10">
                                                    <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                                        {__('events.all_day')}
                                                    </dt>
                                                    <dd className="mt-1 text-sm/6 text-gray-700 dark:text-gray-400">
                                                        {selectedTemplate.event_all_day ? (
                                                            <span className="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 inset-ring-1 inset-ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:inset-ring-green-500/20">
                                                                {__('actions.yes')}
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 inset-ring-1 inset-ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:inset-ring-gray-400/20">
                                                                {__('actions.no')}
                                                            </span>
                                                        )}
                                                    </dd>
                                                </div>
                                                {!selectedTemplate.event_all_day && selectedTemplate.event_timezone && (
                                                    <div className="border-t border-gray-100 px-4 py-4 sm:col-span-1 sm:px-4 dark:border-white/10">
                                                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                                            {__('events.timezone')}
                                                        </dt>
                                                        <dd className="mt-1 text-sm/6 text-gray-700 dark:text-gray-400">
                                                            {selectedTemplate.event_timezone}
                                                        </dd>
                                                    </div>
                                                )}
                                                {selectedTemplate.event_description && (
                                                    <div
                                                        className={clsx(
                                                            'border-t border-gray-100 px-4 py-4 sm:px-4 dark:border-white/10',
                                                            !selectedTemplate.event_all_day &&
                                                                selectedTemplate.event_timezone
                                                                ? 'sm:col-span-2'
                                                                : 'sm:col-span-1'
                                                        )}
                                                    >
                                                        <dt className="text-sm/6 font-medium text-gray-900 dark:text-white">
                                                            {__('events.description')}
                                                        </dt>
                                                        <dd
                                                            className="prose prose-sm dark:prose-invert mt-1 max-w-none text-gray-700 dark:text-gray-400"
                                                            dangerouslySetInnerHTML={{
                                                                __html: selectedTemplate.event_description,
                                                            }}
                                                        />
                                                    </div>
                                                )}
                                            </dl>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                        <div className="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button
                                type="button"
                                onClick={handleConfirm}
                                disabled={!selectedTemplate}
                                className="inline-flex w-full cursor-pointer justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50 sm:ml-3 sm:w-auto dark:bg-indigo-500 dark:hover:bg-indigo-400"
                            >
                                {__('events.apply_template')}
                            </button>
                            <button
                                type="button"
                                data-autofocus
                                onClick={handleClose}
                                className="mt-3 inline-flex w-full cursor-pointer justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-gray-700 dark:text-gray-100 dark:inset-ring-gray-600 dark:hover:bg-gray-600"
                            >
                                {__('actions.cancel')}
                            </button>
                        </div>
                    </DialogPanel>
                </div>
            </div>
        </Dialog>
    );
};

export default EventTemplateSelectModal;
