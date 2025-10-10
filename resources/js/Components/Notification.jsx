import { Transition } from '@headlessui/react';
import { XMarkIcon } from '@heroicons/react/20/solid';
import { CheckCircleIcon } from '@heroicons/react/24/outline';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const Notification = () => {
    const { flash } = usePage().props;
    const [show, setShow] = useState(false);
    const [message, setMessage] = useState('');

    useEffect(() => {
        if (flash.success) {
            setMessage(flash.success);
            setShow(true);

            const timer = setTimeout(() => {
                setShow(false);
            }, 5000);

            return () => clearTimeout(timer);
        }
    }, [flash.success]);

    if (!message) {
        return null;
    }

    return (
        <div
            aria-live="assertive"
            className="pointer-events-none fixed inset-0 z-50 flex items-end px-4 py-6 sm:items-start sm:p-6"
        >
            <div className="flex w-full flex-col items-center gap-4 sm:items-end">
                <Transition show={show}>
                    <div className="pointer-events-auto w-full max-w-sm rounded-lg bg-white shadow-lg outline-1 outline-black/5 transition data-closed:opacity-0 data-enter:transform data-enter:duration-300 data-enter:ease-out data-closed:data-enter:translate-y-2 data-leave:duration-100 data-leave:ease-in data-closed:data-enter:sm:translate-x-2 data-closed:data-enter:sm:translate-y-0 dark:bg-gray-800">
                        <div className="p-4">
                            <div className="flex items-start">
                                <div className="shrink-0">
                                    <CheckCircleIcon aria-hidden="true" className="size-6 text-green-400" />
                                </div>
                                <div className="ml-3 w-0 flex-1 pt-0.5">
                                    <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{message}</p>
                                </div>
                                <div className="ml-4 flex shrink-0">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setShow(false);
                                        }}
                                        className="inline-flex rounded-md text-gray-400 hover:text-gray-500 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 dark:text-gray-500 dark:hover:text-gray-400"
                                    >
                                        <span className="sr-only">Close</span>
                                        <XMarkIcon aria-hidden="true" className="size-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    );
};

export default Notification;
