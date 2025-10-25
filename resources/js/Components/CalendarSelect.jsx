import ProviderLogo from '@/Components/ProviderLogo';
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/react';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/react/24/solid';
import clsx from 'clsx';

const CalendarSelect = ({
    value,
    onChange,
    calendars,
    allowAll = false,
    allText = null,
    error = null,
    id = null,
    name = null,
    className = '',
}) => {
    const selectedCalendar = calendars.find(cal => cal.id.toString() === value?.toString());

    return (
        <Listbox value={value} onChange={onChange}>
            <div className={clsx('relative', className)}>
                <ListboxButton
                    id={id}
                    name={name}
                    aria-invalid={!!error}
                    aria-describedby={error && id ? `${id}-error` : undefined}
                    className={clsx(
                        'relative w-full cursor-default rounded-md border bg-white py-2 pr-10 pl-3 text-left text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:bg-gray-700',
                        error
                            ? 'border-red-300 text-red-900 dark:border-red-500/50 dark:text-red-400'
                            : 'border-gray-300 text-gray-900 dark:border-gray-600 dark:text-gray-100'
                    )}
                >
                    <span className="flex min-h-5 items-center gap-2 truncate">
                        {!value || (allowAll && value === 'all') ? (
                            <span className="text-gray-900 dark:text-gray-100">{allText}</span>
                        ) : (
                            <>
                                <span className="truncate">{selectedCalendar?.name}</span>
                                {selectedCalendar?.provider && (
                                    <span
                                        className={clsx(
                                            'flex items-center gap-1',
                                            error
                                                ? 'text-red-700 dark:text-red-300'
                                                : 'text-gray-500 dark:text-gray-400'
                                        )}
                                    >
                                        <ProviderLogo
                                            image_path={selectedCalendar.provider.image_path}
                                            title={selectedCalendar.provider.name}
                                            className="h-4 w-4"
                                        />
                                    </span>
                                )}
                            </>
                        )}
                    </span>
                    <span className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                        <ChevronUpDownIcon
                            aria-hidden="true"
                            className={clsx(
                                'size-5',
                                error ? 'text-red-400 dark:text-red-500' : 'text-gray-400 dark:text-gray-500'
                            )}
                        />
                    </span>
                </ListboxButton>

                <ListboxOptions
                    transition
                    className="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-sm shadow-lg outline-1 outline-black/5 data-leave:transition data-leave:duration-100 data-leave:ease-in data-closed:data-leave:opacity-0 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
                >
                    {allowAll && (
                        <ListboxOption
                            value="all"
                            className="group relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none data-focus:bg-indigo-600 data-focus:text-white data-focus:outline-hidden dark:text-white dark:data-focus:bg-indigo-500"
                        >
                            <span className="block truncate font-normal group-data-selected:font-semibold">
                                {allText}
                            </span>
                            <span className="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 group-not-data-selected:hidden group-data-focus:text-white dark:text-indigo-400">
                                <CheckIcon aria-hidden="true" className="size-5" />
                            </span>
                        </ListboxOption>
                    )}
                    {calendars.map(calendar => (
                        <ListboxOption
                            key={calendar.id}
                            value={calendar.id.toString()}
                            className="group relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none data-focus:bg-indigo-600 data-focus:text-white data-focus:outline-hidden dark:text-white dark:data-focus:bg-indigo-500"
                        >
                            <span className="flex items-center gap-2 truncate font-normal group-data-selected:font-semibold">
                                <span className="truncate">{calendar.name}</span>
                                {calendar.provider && (
                                    <span className="flex items-center gap-1 text-gray-500 group-data-focus:text-indigo-200 dark:text-gray-400 dark:group-data-focus:text-indigo-100">
                                        <ProviderLogo
                                            image_path={calendar.provider.image_path}
                                            title={calendar.provider.name}
                                            className="h-4 w-4"
                                        />
                                    </span>
                                )}
                            </span>
                            <span className="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 group-not-data-selected:hidden group-data-focus:text-white dark:text-indigo-400">
                                <CheckIcon aria-hidden="true" className="size-5" />
                            </span>
                        </ListboxOption>
                    ))}
                </ListboxOptions>
            </div>
            {error && id && (
                <p id={`${id}-error`} role="alert" className="mt-2 text-sm text-red-600 dark:text-red-400">
                    {error}
                </p>
            )}
        </Listbox>
    );
};

export default CalendarSelect;
