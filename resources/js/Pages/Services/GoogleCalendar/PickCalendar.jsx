import { useForm } from '@inertiajs/react';

const PickCalendar = ({ calendars }) => {
    const { data, setData, post, processing, errors } = useForm({
        calendar_id: '',
    });

    const handleSubmit = e => {
        e.preventDefault();
        post(route('services.google.calendar.update'));
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h2 className="mb-6 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {__('profile.pick_calendar')}
            </h2>

            <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid gap-4 sm:grid-cols-2">
                    {calendars.map(calendar => (
                        <label
                            key={calendar.id}
                            className={`relative cursor-pointer rounded-lg border p-4 shadow-sm transition hover:shadow-md dark:bg-gray-700 ${
                                data.calendar_id === calendar.id
                                    ? 'border-blue-600 ring-2 ring-blue-600'
                                    : 'border-gray-300 dark:border-gray-600'
                            }`}
                        >
                            <input
                                type="radio"
                                name="calendar"
                                value={calendar.id}
                                checked={data.calendar_id === calendar.id}
                                onChange={() => setData('calendar_id', calendar.id)}
                                className="sr-only"
                            />
                            <div className="space-y-1">
                                <p className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {calendar.summary}
                                </p>
                                <p className="text-sm text-gray-600 dark:text-gray-300">
                                    {calendar.description || '—'}
                                </p>
                                <p className="text-xs text-gray-500 capitalize dark:text-gray-400">
                                    {__('profile.timezone')}: {calendar.timezone}
                                </p>
                            </div>
                        </label>
                    ))}
                </div>

                {errors.calendar_id && <p className="text-sm text-red-600">{errors.calendar_id}</p>}

                <div className="flex items-center justify-end gap-3">
                    <button
                        type="submit"
                        disabled={processing}
                        className="cursor-pointer rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white capitalize hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                    >
                        {processing ? __('common.updating') : __('common.choose')}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default PickCalendar;
