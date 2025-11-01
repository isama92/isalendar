import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import { ChevronDownIcon } from '@heroicons/react/20/solid';

const EventFormActions = ({ processing, isEditing, onSubmit }) => {
    return (
        <div className="mt-6 flex items-center justify-end gap-x-6">
            <button
                type="button"
                onClick={() => window.history.back()}
                className="cursor-pointer text-sm/6 font-semibold text-gray-900 dark:text-white"
            >
                {__('actions.back')}
            </button>
            <div className="inline-flex rounded-md shadow-xs dark:shadow-none">
                <button
                    type="button"
                    onClick={e => onSubmit(e)}
                    disabled={processing}
                    className="relative inline-flex cursor-pointer items-center rounded-l-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white inset-ring-1 inset-ring-indigo-600 hover:bg-indigo-500 focus:z-10 dark:bg-indigo-500 dark:inset-ring-indigo-500 dark:hover:bg-indigo-400"
                >
                    {processing
                        ? isEditing
                            ? __('actions.updating')
                            : __('actions.creating')
                        : isEditing
                          ? __('actions.update_and_view')
                          : __('actions.create_and_new')}
                </button>
                <Menu as="div" className="relative -ml-px block">
                    <MenuButton
                        disabled={processing}
                        className="relative inline-flex cursor-pointer items-center rounded-r-md bg-indigo-600 px-2 py-2 text-white inset-ring-1 inset-ring-indigo-600 hover:bg-indigo-500 focus:z-10 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-indigo-500 dark:inset-ring-indigo-500 dark:hover:bg-indigo-400"
                    >
                        <span className="sr-only">Open options</span>
                        <ChevronDownIcon aria-hidden="true" className="size-5" />
                    </MenuButton>
                    <MenuItems
                        anchor="bottom end"
                        transition
                        className="z-10 w-56 rounded-md bg-white shadow-lg outline-1 outline-black/5 transition data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
                    >
                        <div className="py-1">
                            {isEditing ? (
                                <>
                                    <MenuItem>
                                        <button
                                            type="button"
                                            onClick={e => onSubmit(e, 'edit')}
                                            disabled={processing}
                                            className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                        >
                                            {__('actions.update_and_stay')}
                                        </button>
                                    </MenuItem>
                                    <MenuItem>
                                        <button
                                            type="button"
                                            onClick={e => onSubmit(e, 'index')}
                                            disabled={processing}
                                            className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                        >
                                            {__('actions.update_and_list')}
                                        </button>
                                    </MenuItem>
                                </>
                            ) : (
                                <>
                                    <MenuItem>
                                        <button
                                            type="button"
                                            onClick={e => onSubmit(e, 'show')}
                                            disabled={processing}
                                            className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                        >
                                            {__('actions.create_and_view')}
                                        </button>
                                    </MenuItem>
                                    <MenuItem>
                                        <button
                                            type="button"
                                            onClick={e => onSubmit(e, 'index')}
                                            disabled={processing}
                                            className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                        >
                                            {__('actions.create_and_list')}
                                        </button>
                                    </MenuItem>
                                </>
                            )}
                        </div>
                    </MenuItems>
                </Menu>
            </div>
        </div>
    );
};

export default EventFormActions;
