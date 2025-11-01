import CalendarSelect from '@/Components/Forms/CalendarSelect';
import Checkbox from '@/Components/Forms/Checkbox';
import DateTimePicker from '@/Components/Forms/DateTimePicker';
import DurationSuggestions from '@/Components/Forms/DurationSuggestions';
import RichTextEditor from '@/Components/Forms/RichTextEditor';
import TimezoneSelect from '@/Components/Forms/TimezoneSelect';
import clsx from 'clsx';
import { getEndsAtMinDate } from '../eventFormHelpers';

const EventFormFields = ({
    data,
    setData,
    errors,
    calendars,
    timezones,
    isEditing,
    handleStartsAtChange,
    handleSuggestionClick,
}) => {
    return (
        <div className="space-y-6">
            {/* Calendar */}
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

            {/* Title */}
            <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                <label htmlFor="title" className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white">
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

            {/* All Day */}
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

            {/* Starts At */}
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
                        <p id="starts_at-error" role="alert" className="mt-2 text-sm text-red-600 dark:text-red-400">
                            {errors.starts_at}
                        </p>
                    )}
                </div>
            </div>

            {/* Ends At */}
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
                    <DurationSuggestions
                        title={data.title}
                        startsAt={data.starts_at}
                        isAllDay={data.all_day}
                        onSelect={handleSuggestionClick}
                        isEditing={isEditing}
                    />
                </div>
            </div>

            {/* Timezone */}
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

            {/* Description */}
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
                        <p id="description-error" role="alert" className="mt-2 text-sm text-red-600 dark:text-red-400">
                            {errors.description}
                        </p>
                    )}
                </div>
            </div>
        </div>
    );
};

export default EventFormFields;
