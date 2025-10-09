import { formatWithFormat } from '@/utils/date';
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from '@headlessui/react';
import { ChevronUpDownIcon } from '@heroicons/react/16/solid';
import { CheckIcon } from '@heroicons/react/20/solid';
import { useForm } from '@inertiajs/react';
import clsx from 'clsx';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';

const Form = ({ calendars, event = null }) => {
    const isEditing = !!event;

    const { data, setData, post, put, processing, errors } = useForm({
        calendar_id: event?.calendar?.id || '',
        title: event?.title || '',
        all_day: event?.all_day || false,
        starts_at: event?.starts_at || '',
        ends_at: event?.ends_at || '',
        description: event?.description || '',
    });

    const handleSubmit = e => {
        e.preventDefault();

        if (isEditing) {
            put(route('events.update', event.id));
            return;
        }

        post(route('events.store'));
    };

    const selected = calendars.find(cal => cal.id === data.calendar_id);

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <form className="space-y-6">
                <div className="space-y-12 sm:space-y-16">
                    {/* header */}
                    <div>
                        <h2 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                            {isEditing ? __('events.edit_event') : __('events.create_event')}
                        </h2>
                        <p className="mt-1 max-w-2xl text-sm/6 text-gray-600 dark:text-gray-400">
                            {__('events.event_will_be_uploaded_after_save')}
                        </p>
                    </div>

                    {/* field */}
                    <div className="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:border-t-gray-900/10 sm:pb-0 dark:border-white/10 dark:sm:divide-white/10 dark:sm:border-t-white/10">
                        {/* calendar */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="calendar_id"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('calendars.calendar')}
                            </label>
                            <Listbox value={data.calendar_id} onChange={value => setData('calendar_id', value)}>
                                <div className="relative mt-2 sm:col-span-2 sm:mt-0 sm:max-w-md">
                                    <ListboxButton
                                        id="calendar_id"
                                        name="calendar_id"
                                        aria-invalid={!!errors.calendar_id}
                                        aria-describedby={errors.calendar_id ? 'calendar_id-error' : ''}
                                        className={clsx(
                                            'grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6 dark:bg-white/5 dark:outline-white/10',
                                            errors.calendar_id
                                                ? 'text-red-900 dark:text-red-400'
                                                : 'text-gray-900 dark:text-white'
                                        )}
                                    >
                                        <span className="col-start-1 row-start-1 flex w-full gap-2 pr-6">
                                            <span className="truncate">{selected?.name}</span>
                                            <span
                                                className={clsx(
                                                    'truncate',
                                                    errors.calendar_id
                                                        ? 'text-red-700 dark:text-red-300'
                                                        : 'text-gray-500 dark:text-gray-400'
                                                )}
                                            >
                                                {selected?.name}
                                            </span>
                                        </span>
                                        <ChevronUpDownIcon
                                            aria-hidden="true"
                                            className="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4 dark:text-gray-400"
                                        />
                                    </ListboxButton>

                                    <ListboxOptions
                                        transition
                                        className="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg outline-1 outline-black/5 data-leave:transition data-leave:duration-100 data-leave:ease-in data-closed:data-leave:opacity-0 sm:text-sm dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
                                    >
                                        {calendars.map(calendar => (
                                            <ListboxOption
                                                key={calendar.id}
                                                value={calendar.id}
                                                className="group relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none data-focus:bg-indigo-600 data-focus:text-white data-focus:outline-hidden dark:text-white dark:data-focus:bg-indigo-500"
                                            >
                                                <div className="flex">
                                                    <span className="truncate font-normal group-data-selected:font-semibold">
                                                        {calendar.name}
                                                    </span>
                                                    <span className="ml-2 truncate text-gray-500 group-data-focus:text-indigo-200 dark:text-gray-400 dark:group-data-focus:text-indigo-100">
                                                        {calendar.name}
                                                    </span>
                                                </div>

                                                <span className="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 group-not-data-selected:hidden group-data-focus:text-white dark:text-indigo-400">
                                                    <CheckIcon aria-hidden="true" className="size-5" />
                                                </span>
                                            </ListboxOption>
                                        ))}
                                    </ListboxOptions>
                                    {errors.calendar_id && (
                                        <p
                                            id="calendar_id-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {errors.calendar_id}
                                        </p>
                                    )}
                                </div>
                            </Listbox>
                        </div>
                    </div>
                </div>

                {/* title */}
                <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                    <label
                        htmlFor="title"
                        className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                    >
                        {__('events.title')}
                    </label>
                    <div className="mt-2 sm:col-span-2 sm:mt-0">
                        <div className="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 sm:max-w-md dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                            <input
                                id="title"
                                name="title"
                                type="text"
                                placeholder={__('events.title')}
                                value={data.title}
                                onChange={e => setData('title', e.target.value)}
                                aria-invalid={!!errors.title}
                                aria-describedby={errors.title ? 'title-error' : ''}
                                className={clsx(
                                    'block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base focus:outline-none sm:text-sm/6 dark:bg-transparent',
                                    errors.title
                                        ? 'text-red-900 placeholder:text-red-300 dark:text-red-400 dark:placeholder:text-red-400/70'
                                        : 'text-gray-900 placeholder:text-gray-400 dark:text-white dark:placeholder:text-gray-500'
                                )}
                            />
                        </div>
                        {errors.title && (
                            <p id="title-error" role="alert" className="mt-2 text-sm text-red-600 dark:text-red-400">
                                {errors.title}
                            </p>
                        )}
                    </div>
                </div>

                {/* all day */}
                <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                    <label className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white">
                        {__('events.all_day')}
                    </label>
                    <div className="mt-2 sm:col-span-2 sm:mt-0">
                        <div className="flex gap-3">
                            <div className="flex h-6 shrink-0 items-center">
                                <div className="group grid size-4 grid-cols-1">
                                    <input
                                        id="all_day"
                                        name="all_day"
                                        type="checkbox"
                                        checked={data.all_day}
                                        onChange={e => setData('all_day', e.target.checked)}
                                        className="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto"
                                    />
                                    <svg
                                        fill="none"
                                        viewBox="0 0 14 14"
                                        className="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25"
                                    >
                                        <path
                                            d="M3 8L6 11L11 3.5"
                                            strokeWidth={2}
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            className="opacity-0 group-has-checked:opacity-100"
                                        />
                                        <path
                                            d="M3 7H11"
                                            strokeWidth={2}
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            className="opacity-0 group-has-indeterminate:opacity-100"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* starts at */}
                <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                    <label
                        htmlFor="starts_at"
                        className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                    >
                        {__('events.starts_at')}
                    </label>
                    <div className="mt-2 sm:col-span-2 sm:mt-0">
                        <DatePicker
                            id="starts_at"
                            name="starts_at"
                            selected={data.starts_at ? new Date(data.starts_at) : null}
                            onChange={date =>
                                setData(
                                    'starts_at',
                                    date
                                        ? formatWithFormat(date, data.all_day ? 'YYYY-MM-DD' : 'YYYY-MM-DD HH:mm:ss')
                                        : ''
                                )
                            }
                            showTimeSelect={!data.all_day}
                            timeFormat="HH:mm"
                            timeIntervals={15}
                            dateFormat={data.all_day ? 'dd/MM/yyyy' : 'dd/MM/yyyy HH:mm'}
                            calendarStartDay={1}
                            aria-invalid={!!errors.starts_at}
                            aria-describedby={errors.starts_at ? 'starts_at-error' : ''}
                            wrapperClassName="w-full"
                            className={clsx(
                                'block w-full rounded-md bg-white px-3 py-1.5 outline-1 -outline-offset-1 focus:outline-2 focus:-outline-offset-2 sm:max-w-md sm:text-sm/6 dark:bg-white/5',
                                errors.starts_at
                                    ? 'text-red-900 outline-red-300 placeholder:text-red-300 focus:outline-red-600 dark:text-red-400 dark:outline-red-500/50 dark:placeholder:text-red-400/70 dark:focus:outline-red-400'
                                    : 'text-base text-gray-900 outline-gray-300 placeholder:text-gray-400 focus:outline-indigo-600 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500'
                            )}
                        />
                        {errors.starts_at && (
                            <p
                                id="starts_at-error"
                                role="alert"
                                className="mt-2 text-sm text-red-600 dark:text-red-400"
                            >
                                {errors.starts_at}
                            </p>
                        )}
                    </div>
                </div>

                {/* ends at */}
                <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                    <label
                        htmlFor="ends_at"
                        className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                    >
                        {__('events.ends_at')}
                    </label>
                    <div className="mt-2 sm:col-span-2 sm:mt-0">
                        <DatePicker
                            id="ends_at"
                            name="ends_at"
                            selected={data.ends_at ? new Date(data.ends_at) : null}
                            onChange={date =>
                                setData(
                                    'ends_at',
                                    date
                                        ? formatWithFormat(date, data.all_day ? 'YYYY-MM-DD' : 'YYYY-MM-DD HH:mm:ss')
                                        : ''
                                )
                            }
                            showTimeSelect={!data.all_day}
                            timeFormat="HH:mm"
                            timeIntervals={15}
                            dateFormat={data.all_day ? 'dd/MM/yyyy' : 'dd/MM/yyyy HH:mm'}
                            calendarStartDay={1}
                            aria-invalid={!!errors.ends_at}
                            aria-describedby={errors.ends_at ? 'ends_at-error' : ''}
                            wrapperClassName="w-full"
                            className={clsx(
                                'block w-full rounded-md bg-white px-3 py-1.5 outline-1 -outline-offset-1 focus:outline-2 focus:-outline-offset-2 sm:max-w-md sm:text-sm/6 dark:bg-white/5',
                                errors.ends_at
                                    ? 'text-red-900 outline-red-300 placeholder:text-red-300 focus:outline-red-600 dark:text-red-400 dark:outline-red-500/50 dark:placeholder:text-red-400/70 dark:focus:outline-red-400'
                                    : 'text-base text-gray-900 outline-gray-300 placeholder:text-gray-400 focus:outline-indigo-600 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500'
                            )}
                        />
                        {errors.ends_at && (
                            <p id="ends_at-error" role="alert" className="mt-2 text-sm text-red-600 dark:text-red-400">
                                {errors.ends_at}
                            </p>
                        )}
                    </div>
                </div>

                {/* description */}
                <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                    <label
                        htmlFor="description"
                        className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                    >
                        {__('events.description')}
                    </label>
                    <div className="mt-2 sm:col-span-2 sm:mt-0">
                        <textarea
                            id="description"
                            name="description"
                            value={data.description}
                            onChange={e => setData('description', e.target.value)}
                            rows={3}
                            aria-invalid={!!errors.description}
                            aria-describedby={errors.description ? 'description-error' : ''}
                            className={clsx(
                                'block w-full rounded-md bg-white px-3 py-1.5 text-base outline-1 -outline-offset-1 focus:outline-2 focus:-outline-offset-2 sm:max-w-2xl sm:text-sm/6 dark:bg-white/5',
                                errors.description
                                    ? 'text-red-900 outline-red-300 placeholder:text-red-300 focus:outline-red-600 dark:text-red-400 dark:outline-red-500/50 dark:placeholder:text-red-400/70 dark:focus:outline-red-400'
                                    : 'text-gray-900 outline-gray-300 placeholder:text-gray-400 focus:outline-indigo-600 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500'
                            )}
                        />
                        {errors.description && (
                            <p
                                id="description-error"
                                role="alert"
                                className="mt-2 text-sm text-red-600 dark:text-red-400"
                            >
                                {errors.description}
                            </p>
                        )}
                    </div>
                </div>

                {/* actions */}
                <div className="mt-6 flex items-center justify-end gap-x-6">
                    <button
                        type="button"
                        onClick={() => window.history.back()}
                        className="cursor-pointer text-sm/6 font-semibold text-gray-900 dark:text-white"
                    >
                        {__('common.back')}
                    </button>
                    <button
                        type="button"
                        onClick={handleSubmit}
                        disabled={processing}
                        className="inline-flex cursor-pointer justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                    >
                        {processing
                            ? isEditing
                                ? __('common.updating')
                                : __('common.creating')
                            : isEditing
                              ? __('common.update')
                              : __('common.create')}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default Form;
