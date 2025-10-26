import CalendarSelect from '@/Components/CalendarSelect';
import Checkbox from '@/Components/Checkbox';
import DateTimePicker from '@/Components/DateTimePicker';
import DurationSuggestions from '@/Components/DurationSuggestions';
import EventTemplateSelectModal from '@/Components/EventTemplateSelectModal';
import ProgressSteps from '@/Components/ProgressSteps';
import RichTextEditor from '@/Components/RichTextEditor';
import TimezoneSelect from '@/Components/TimezoneSelect';
import { getBatchEventSetupSteps } from '@/utils/batchEventSetupSteps';
import { DATE_FORMAT, subtractDays } from '@/utils/date';
import { MinusCircleIcon, PlusCircleIcon } from '@heroicons/react/20/solid';
import { router } from '@inertiajs/react';
import clsx from 'clsx';
import { useState } from 'react';
import {
    calculateDefaultEndsAt,
    calculateEndsAtFromDuration,
    formatEndsAtForSubmission,
    formatStartsAtForSubmission,
    getEndsAtMinDate,
    shouldClearEndsAt,
} from './eventFormHelpers';

const CreateBatch = ({ currentUser, calendars, timezones, eventTemplates = [] }) => {
    const [currentStep, setCurrentStep] = useState(1);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState({});
    const [templateModalOpen, setTemplateModalOpen] = useState(false);

    // Step 1 data: Common event details
    const [calendarId, setCalendarId] = useState('');
    const [title, setTitle] = useState('');
    const [allDay, setAllDay] = useState(false);
    const [timezone, setTimezone] = useState(currentUser?.settings?.default_timezone || '');
    const [description, setDescription] = useState('');

    // Step 2 data: Multiple date/time entries
    const [dateEntries, setDateEntries] = useState([{ starts_at: '', ends_at: '' }]);

    const handleAddDateEntry = () => {
        setDateEntries([...dateEntries, { starts_at: '', ends_at: '' }]);
    };

    const handleRemoveDateEntry = index => {
        if (dateEntries.length > 1) {
            setDateEntries(dateEntries.filter((_, i) => i !== index));
        }
    };

    const updateEndsAtForEntry = (entry, startsAt) => {
        if (!entry.ends_at) {
            entry.ends_at = calculateDefaultEndsAt(startsAt, allDay);
            return;
        }

        if (shouldClearEndsAt(startsAt, entry.ends_at)) {
            entry.ends_at = calculateDefaultEndsAt(startsAt, allDay);
        }
    };

    const handleDateEntryChange = (index, field, value) => {
        const newEntries = [...dateEntries];
        newEntries[index][field] = value;

        if (field === 'starts_at' && value) {
            updateEndsAtForEntry(newEntries[index], value);
        }

        setDateEntries(newEntries);
    };

    const handleSuggestionClick = (index, durationMinutes) => {
        const entry = dateEntries[index];

        if (!entry.starts_at) {
            return;
        }

        let calculatedEndsAt = calculateEndsAtFromDuration(entry.starts_at, durationMinutes, allDay);

        // For all-day events, subtract 1 day to match frontend display (DB stores exclusive end date)
        if (allDay && calculatedEndsAt) {
            calculatedEndsAt = subtractDays(calculatedEndsAt, 1, DATE_FORMAT);
        }

        handleDateEntryChange(index, 'ends_at', calculatedEndsAt);
    };

    const handleTemplateSelect = template => {
        setCalendarId(template.calendar_id || '');
        setTitle(template.event_title || '');
        setAllDay(template.event_all_day || false);
        setTimezone(template.event_timezone || currentUser?.settings?.default_timezone || '');
        setDescription(template.event_description || '');
        setTemplateModalOpen(false);
    };

    const validateStep1 = () => {
        const newErrors = {};

        if (!calendarId) {
            newErrors.calendar_id = __('validation.required', { attribute: __('calendars.calendar') });
        }

        if (!title.trim()) {
            newErrors.title = __('validation.required', { attribute: __('events.title') });
        }

        if (!allDay && !timezone) {
            newErrors.timezone = __('validation.required', { attribute: __('events.timezone') });
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleNextStep = () => {
        if (validateStep1()) {
            setCurrentStep(2);
            setErrors({});
        }
    };

    const handleBackToStep1 = () => {
        setCurrentStep(1);
        setErrors({});
    };

    const handleSubmit = e => {
        e.preventDefault();

        if (currentStep === 1) {
            handleNextStep();
            return;
        }

        setProcessing(true);

        const formattedDates = dateEntries.map(entry => ({
            starts_at: formatStartsAtForSubmission(entry.starts_at, allDay),
            ends_at: formatEndsAtForSubmission(entry.ends_at, allDay),
        }));

        router.post(
            route('events.batch.store'),
            {
                calendar_id: calendarId,
                title,
                all_day: allDay,
                timezone: allDay ? null : timezone,
                description,
                dates: formattedDates,
            },
            {
                onError: errors => {
                    setErrors(errors);
                    setProcessing(false);
                },
                onFinish: () => {
                    setProcessing(false);
                },
            }
        );
    };

    return (
        <>
            <ProgressSteps
                steps={getBatchEventSetupSteps({
                    step1Href: currentStep === 2 ? route('events.batch.create') : undefined,
                })}
                currentStep={currentStep}
            />

            <div className="mx-auto mt-8 max-w-5xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                    <div className="flex items-center justify-between">
                        <h2 className="text-base/7 font-semibold text-gray-900 dark:text-gray-100">
                            {currentStep === 1 ? __('events.batch_event_details') : __('events.batch_event_dates')}
                        </h2>
                        {currentStep === 1 && (
                            <button
                                type="button"
                                onClick={() => setTemplateModalOpen(true)}
                                disabled={eventTemplates.length === 0}
                                className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-700 dark:text-gray-100 dark:inset-ring-gray-600 dark:hover:bg-gray-600"
                            >
                                {__('events.use_event_template')}
                            </button>
                        )}
                    </div>

                    <form
                        onSubmit={handleSubmit}
                        className="mt-10 space-y-6 border-t border-gray-900/10 pt-10 dark:border-white/10"
                    >
                        {currentStep === 1 ? (
                            <>
                                {/* Calendar */}
                                <div>
                                    <label
                                        htmlFor="calendar_id"
                                        className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                    >
                                        {__('calendars.calendar')}
                                    </label>
                                    <div className="mt-2">
                                        <CalendarSelect
                                            value={calendarId}
                                            onChange={value => setCalendarId(parseInt(value))}
                                            calendars={calendars}
                                            error={errors.calendar_id}
                                            id="calendar_id"
                                            name="calendar_id"
                                        />
                                    </div>
                                </div>

                                {/* Title */}
                                <div>
                                    <label
                                        htmlFor="title"
                                        className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                    >
                                        {__('events.title')}
                                    </label>
                                    <div className="mt-2">
                                        <div className="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                                            <input
                                                id="title"
                                                name="title"
                                                type="text"
                                                placeholder={__('events.title')}
                                                value={title}
                                                onChange={e => setTitle(e.target.value)}
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
                                            <p
                                                id="title-error"
                                                role="alert"
                                                className="mt-2 text-sm text-red-600 dark:text-red-400"
                                            >
                                                {errors.title}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                {/* All Day */}
                                <div>
                                    <label className="block text-sm/6 font-medium text-gray-900 dark:text-white">
                                        {__('events.all_day')}
                                    </label>
                                    <div className="mt-2">
                                        <Checkbox
                                            id="all_day"
                                            name="all_day"
                                            checked={allDay}
                                            onChange={e => setAllDay(e.target.checked)}
                                        />
                                    </div>
                                </div>

                                {/* Timezone */}
                                {!allDay && (
                                    <div>
                                        <label
                                            htmlFor="timezone"
                                            className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                        >
                                            {__('events.timezone')}
                                        </label>
                                        <div className="mt-2">
                                            <TimezoneSelect
                                                id="timezone"
                                                name="timezone"
                                                value={timezone}
                                                onChange={option => setTimezone(option?.value)}
                                                options={timezones}
                                                error={errors.timezone}
                                            />
                                        </div>
                                    </div>
                                )}

                                {/* Description */}
                                <div>
                                    <label
                                        htmlFor="description"
                                        className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                    >
                                        {__('events.description')}
                                    </label>
                                    <div className="mt-2">
                                        <RichTextEditor
                                            id="description"
                                            name="description"
                                            value={description}
                                            onChange={value => setDescription(value)}
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
                            </>
                        ) : (
                            <>
                                {/* Date Entries */}
                                <div className="space-y-4">
                                    {dateEntries.map((entry, index) => (
                                        <div
                                            key={index}
                                            className="relative rounded-lg border border-gray-200 p-4 dark:border-white/10"
                                        >
                                            {dateEntries.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() => handleRemoveDateEntry(index)}
                                                    className="absolute top-2 right-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                                    aria-label={__('actions.remove')}
                                                >
                                                    <MinusCircleIcon className="size-6" />
                                                </button>
                                            )}

                                            <div className="space-y-4">
                                                {/* Starts At */}
                                                <div>
                                                    <label
                                                        htmlFor={`starts_at_${index}`}
                                                        className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                                    >
                                                        {__('events.starts_at')}
                                                    </label>
                                                    <div className="mt-2">
                                                        <DateTimePicker
                                                            id={`starts_at_${index}`}
                                                            name={`starts_at_${index}`}
                                                            minuteInterval={5}
                                                            value={entry.starts_at}
                                                            onChange={value =>
                                                                handleDateEntryChange(index, 'starts_at', value)
                                                            }
                                                            allDay={allDay}
                                                            error={errors[`dates.${index}.starts_at`]}
                                                        />
                                                        {errors[`dates.${index}.starts_at`] && (
                                                            <p
                                                                id={`starts_at_${index}-error`}
                                                                role="alert"
                                                                className="mt-2 text-sm text-red-600 dark:text-red-400"
                                                            >
                                                                {errors[`dates.${index}.starts_at`]}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Ends At */}
                                                <div>
                                                    <label
                                                        htmlFor={`ends_at_${index}`}
                                                        className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                                    >
                                                        {__('events.ends_at')}
                                                    </label>
                                                    <div className="mt-2">
                                                        <DateTimePicker
                                                            id={`ends_at_${index}`}
                                                            name={`ends_at_${index}`}
                                                            minuteInterval={5}
                                                            value={entry.ends_at}
                                                            onChange={value =>
                                                                handleDateEntryChange(index, 'ends_at', value)
                                                            }
                                                            allDay={allDay}
                                                            error={errors[`dates.${index}.ends_at`]}
                                                            minDate={getEndsAtMinDate(entry.starts_at)}
                                                        />
                                                        {errors[`dates.${index}.ends_at`] && (
                                                            <p
                                                                id={`ends_at_${index}-error`}
                                                                role="alert"
                                                                className="mt-2 text-sm text-red-600 dark:text-red-400"
                                                            >
                                                                {errors[`dates.${index}.ends_at`]}
                                                            </p>
                                                        )}
                                                        <DurationSuggestions
                                                            title={title}
                                                            startsAt={entry.starts_at}
                                                            isAllDay={allDay}
                                                            onSelect={durationMinutes =>
                                                                handleSuggestionClick(index, durationMinutes)
                                                            }
                                                            isEditing={false}
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}

                                    {/* Add Date Entry Button */}
                                    <button
                                        type="button"
                                        onClick={handleAddDateEntry}
                                        className="flex w-full items-center justify-center gap-2 rounded-md border-2 border-dashed border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-900 hover:border-gray-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:hover:border-white/20"
                                    >
                                        <PlusCircleIcon className="size-5" />
                                        {__('actions.add_date')}
                                    </button>

                                    {errors.dates && (
                                        <p role="alert" className="text-sm text-red-600 dark:text-red-400">
                                            {errors.dates}
                                        </p>
                                    )}
                                </div>
                            </>
                        )}

                        {/* Actions */}
                        <div className="mt-6 flex items-center justify-end gap-x-6">
                            {currentStep === 1 ? (
                                <>
                                    <button
                                        type="button"
                                        onClick={() => window.history.back()}
                                        className="cursor-pointer text-sm/6 font-semibold text-gray-900 dark:text-white"
                                    >
                                        {__('actions.cancel')}
                                    </button>
                                    <button
                                        type="submit"
                                        className="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400"
                                    >
                                        {__('actions.next')}
                                    </button>
                                </>
                            ) : (
                                <>
                                    <button
                                        type="button"
                                        onClick={handleBackToStep1}
                                        disabled={processing}
                                        className="cursor-pointer text-sm/6 font-semibold text-gray-900 disabled:opacity-50 dark:text-white"
                                    >
                                        {__('actions.back')}
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 dark:bg-indigo-500 dark:hover:bg-indigo-400"
                                    >
                                        {processing ? __('actions.creating') : __('actions.create')}
                                    </button>
                                </>
                            )}
                        </div>
                    </form>
                </div>
            </div>

            <EventTemplateSelectModal
                open={templateModalOpen}
                onClose={() => setTemplateModalOpen(false)}
                eventTemplates={eventTemplates}
                onConfirm={handleTemplateSelect}
            />
        </>
    );
};

export default CreateBatch;
