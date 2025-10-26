import CalendarSelect from '@/Components/CalendarSelect';
import Checkbox from '@/Components/Checkbox';
import DateTimePicker from '@/Components/DateTimePicker';
import RichTextEditor from '@/Components/RichTextEditor';
import TimezoneSelect from '@/Components/TimezoneSelect';
import { formatWithFormatAndTimezone, subtractDays } from '@/utils/date';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import { ChevronDownIcon } from '@heroicons/react/20/solid';
import { useForm } from '@inertiajs/react';
import clsx from 'clsx';
import {
    calculateDefaultEndsAt,
    formatEndsAtForSubmission,
    formatStartsAtForSubmission,
    getEndsAtMinDate,
    shouldClearEndsAt,
} from './eventFormHelpers';

const Form = ({ currentUser, calendars, timezones, event = null }) => {
    const isEditing = !!event;

    const convertUtcToEventTimezone = (utcDate, timezone, allDay) => {
        if (!utcDate) return '';
        if (allDay) return utcDate;
        return formatWithFormatAndTimezone(utcDate, timezone, 'YYYY-MM-DD HH:mm:ss');
    };

    const { data, setData, post, put, processing, transform, errors, reset } = useForm({
        calendar_id: event?.calendar?.id || '',
        title: event?.title || '',
        all_day: event?.all_day || false,
        starts_at: event ? convertUtcToEventTimezone(event.starts_at, event.timezone, event.all_day) : '',
        ends_at: event
            ? event.all_day
                ? subtractDays(event.ends_at, 1, 'YYYY-MM-DD')
                : convertUtcToEventTimezone(event.ends_at, event.timezone, event.all_day)
            : '',
        timezone: event?.timezone || currentUser.settings.default_timezone,
        description: event?.description || '',
    });

    const handleStartsAtChange = value => {
        setData('starts_at', value);

        if (!value) {
            return;
        }

        if (!data.ends_at) {
            setData('ends_at', calculateDefaultEndsAt(value, data.all_day));
            return;
        }

        if (shouldClearEndsAt(value, data.ends_at)) {
            setData('ends_at', calculateDefaultEndsAt(value, data.all_day));
        }
    };

    const handleSubmit = (e, redirectTo = null) => {
        e.preventDefault();

        const defaultRedirect = isEditing ? 'show' : 'new';
        const actualRedirect = redirectTo || defaultRedirect;

        transform(() => {
            return {
                ...data,
                starts_at: formatStartsAtForSubmission(data.starts_at, data.all_day),
                ends_at: formatEndsAtForSubmission(data.ends_at, data.all_day),
                redirect_to: actualRedirect,
            };
        });

        if (isEditing) {
            put(route('events.update', event.id));
            return;
        }

        post(route('events.store'), {
            onSuccess: () => {
                if (actualRedirect === 'new') {
                    reset();
                }
            },
        });
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <form className="space-y-6">
                <div className="space-y-12 sm:space-y-16">
                    {/* header */}
                    <div>
                        <h2 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                            {isEditing
                                ? __('actions.named.edit', { name: __('events.event') })
                                : __('actions.named.create', { name: __('events.event') })}
                        </h2>
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
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <CalendarSelect
                                    value={data.calendar_id}
                                    onChange={value => setData('calendar_id', parseInt(value))}
                                    calendars={calendars}
                                    error={errors.calendar_id}
                                    id="calendar_id"
                                    name="calendar_id"
                                    className="sm:max-w-md"
                                />
                            </div>
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
                            <Checkbox
                                id="all_day"
                                name="all_day"
                                checked={data.all_day}
                                onChange={e => setData('all_day', e.target.checked)}
                            />
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
                        <DateTimePicker
                            id="starts_at"
                            name="starts_at"
                            minuteInterval={5}
                            value={data.starts_at}
                            onChange={handleStartsAtChange}
                            allDay={data.all_day}
                            error={errors.starts_at}
                            className="sm:max-w-md"
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
                        <DateTimePicker
                            id="ends_at"
                            name="ends_at"
                            minuteInterval={5}
                            value={data.ends_at}
                            onChange={value => setData('ends_at', value)}
                            allDay={data.all_day}
                            error={errors.ends_at}
                            className="sm:max-w-md"
                            minDate={getEndsAtMinDate(data.starts_at)}
                        />
                        {errors.ends_at && (
                            <p id="ends_at-error" role="alert" className="mt-2 text-sm text-red-600 dark:text-red-400">
                                {errors.ends_at}
                            </p>
                        )}
                    </div>
                </div>

                {/* timezone */}
                {!data.all_day && (
                    <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                        <label
                            htmlFor="timezone"
                            className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                        >
                            {__('events.timezone')}
                        </label>
                        <div className="mt-2 sm:col-span-2 sm:mt-0">
                            <TimezoneSelect
                                id="timezone"
                                name="timezone"
                                value={data.timezone}
                                onChange={option => setData('timezone', option?.value)}
                                options={timezones}
                                error={errors.timezone}
                                className="sm:max-w-md"
                            />
                        </div>
                    </div>
                )}

                {/* description */}
                <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                    <label
                        htmlFor="description"
                        className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                    >
                        {__('events.description')}
                    </label>
                    <div className="mt-2 sm:col-span-2 sm:mt-0">
                        <RichTextEditor
                            id="description"
                            name="description"
                            value={data.description}
                            onChange={value => setData('description', value)}
                            error={errors.description}
                            placeholder={__('events.description')}
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
                        {__('actions.back')}
                    </button>
                    <div className="inline-flex rounded-md shadow-xs dark:shadow-none">
                        <button
                            type="button"
                            onClick={e => handleSubmit(e)}
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
                                                    onClick={e => handleSubmit(e, 'edit')}
                                                    disabled={processing}
                                                    className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                                >
                                                    {__('actions.update_and_stay')}
                                                </button>
                                            </MenuItem>
                                            <MenuItem>
                                                <button
                                                    type="button"
                                                    onClick={e => handleSubmit(e, 'index')}
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
                                                    onClick={e => handleSubmit(e, 'show')}
                                                    disabled={processing}
                                                    className="block w-full cursor-pointer px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                                >
                                                    {__('actions.create_and_view')}
                                                </button>
                                            </MenuItem>
                                            <MenuItem>
                                                <button
                                                    type="button"
                                                    onClick={e => handleSubmit(e, 'index')}
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
            </form>
        </div>
    );
};

export default Form;
