import CalendarSelect from '@/Components/CalendarSelect';
import Checkbox from '@/Components/Checkbox';
import RichTextEditor from '@/Components/RichTextEditor';
import TimezoneSelect from '@/Components/TimezoneSelect';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import { ChevronDownIcon } from '@heroicons/react/20/solid';
import { useForm } from '@inertiajs/react';
import clsx from 'clsx';

const Form = ({ currentUser, calendars, timezones, eventTemplate = null }) => {
    const isEditing = !!eventTemplate;

    const { data, setData, post, put, processing, transform, errors, reset } = useForm({
        calendar_id: eventTemplate?.calendar_id || '',
        name: eventTemplate?.name || '',
        event_title: eventTemplate?.event_title || '',
        event_all_day: eventTemplate?.event_all_day || false,
        event_timezone: eventTemplate?.event_timezone || currentUser.settings.default_timezone,
        event_description: eventTemplate?.event_description || '',
    });

    const handleSubmit = (e, redirectTo = null) => {
        e.preventDefault();

        const defaultRedirect = isEditing ? 'show' : 'new';
        const actualRedirect = redirectTo || defaultRedirect;

        transform(() => {
            return {
                ...data,
                redirect_to: actualRedirect,
            };
        });

        if (isEditing) {
            put(route('event-templates.update', eventTemplate.id));
            return;
        }

        post(route('event-templates.store'), {
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
                                ? __('actions.named.edit', { name: __('event-templates.event_template') })
                                : __('actions.named.create', { name: __('event-templates.event_template') })}
                        </h2>
                    </div>

                    {/* fields */}
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

                        {/* template name */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="name"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('event-templates.name')}
                            </label>
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <div className="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 sm:max-w-md dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                                    <input
                                        id="name"
                                        name="name"
                                        type="text"
                                        placeholder={__('event-templates.name')}
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        aria-invalid={!!errors.name}
                                        aria-describedby={errors.name ? 'name-error' : ''}
                                        className={clsx(
                                            'block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base focus:outline-none sm:text-sm/6 dark:bg-transparent',
                                            errors.name
                                                ? 'text-red-900 placeholder:text-red-300 dark:text-red-400 dark:placeholder:text-red-400/70'
                                                : 'text-gray-900 placeholder:text-gray-400 dark:text-white dark:placeholder:text-gray-500'
                                        )}
                                    />
                                </div>
                                {errors.name && (
                                    <p
                                        id="name-error"
                                        role="alert"
                                        className="mt-2 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {errors.name}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* event title */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="event_title"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('event-templates.event_title')}
                            </label>
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <div className="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 sm:max-w-md dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                                    <input
                                        id="event_title"
                                        name="event_title"
                                        type="text"
                                        placeholder={__('event-templates.event_title')}
                                        value={data.event_title}
                                        onChange={e => setData('event_title', e.target.value)}
                                        aria-invalid={!!errors.event_title}
                                        aria-describedby={errors.event_title ? 'event_title-error' : ''}
                                        className={clsx(
                                            'block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base focus:outline-none sm:text-sm/6 dark:bg-transparent',
                                            errors.event_title
                                                ? 'text-red-900 placeholder:text-red-300 dark:text-red-400 dark:placeholder:text-red-400/70'
                                                : 'text-gray-900 placeholder:text-gray-400 dark:text-white dark:placeholder:text-gray-500'
                                        )}
                                    />
                                </div>
                                {errors.event_title && (
                                    <p
                                        id="event_title-error"
                                        role="alert"
                                        className="mt-2 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {errors.event_title}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* all day */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white">
                                {__('event-templates.event_all_day')}
                            </label>
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <div className="flex gap-3">
                                    <Checkbox
                                        id="event_all_day"
                                        name="event_all_day"
                                        checked={data.event_all_day}
                                        onChange={e => setData('event_all_day', e.target.checked)}
                                    />
                                </div>
                            </div>
                        </div>

                        {/* timezone */}
                        {!data.event_all_day && (
                            <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                                <label
                                    htmlFor="event_timezone"
                                    className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                                >
                                    {__('event-templates.event_timezone')}
                                </label>
                                <div className="mt-2 sm:col-span-2 sm:mt-0">
                                    <TimezoneSelect
                                        id="event_timezone"
                                        name="event_timezone"
                                        value={data.event_timezone}
                                        onChange={option => setData('event_timezone', option?.value)}
                                        options={timezones}
                                        error={errors.event_timezone}
                                        className="sm:max-w-md"
                                    />
                                </div>
                            </div>
                        )}

                        {/* description */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="event_description"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('event-templates.event_description')}
                            </label>
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <RichTextEditor
                                    id="event_description"
                                    name="event_description"
                                    value={data.event_description}
                                    onChange={value => setData('event_description', value)}
                                    error={errors.event_description}
                                    placeholder={__('event-templates.event_description')}
                                />
                                {errors.event_description && (
                                    <p
                                        id="event_description-error"
                                        role="alert"
                                        className="mt-2 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {errors.event_description}
                                    </p>
                                )}
                            </div>
                        </div>
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
