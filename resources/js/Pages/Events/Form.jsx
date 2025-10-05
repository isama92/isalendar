import { useForm } from '@inertiajs/react';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';
import { formatWithFormat } from '../../utils/date';

const Form = ({ event = null }) => {
    const isEditing = !!event;

    const { data, setData, post, put, processing, errors, transform } = useForm({
        title: event?.title || '',
        all_day: event?.all_day || false,
        starts_at: event?.starts_at || '',
        ends_at: event?.ends_at || '',
        description: event?.description || '',
    });

    const handleSubmit = (e, redirectToShow = false) => {
        e.preventDefault();

        transform(data => {
            let submitData = { ...data };

            if (data.all_day) {
                submitData.ends_at = null;
            }

            submitData.redirect_to_show = redirectToShow;

            return submitData;
        });

        const options = {
            onFinish: () => transform(d => d),
        };

        if (isEditing) {
            put(route('events.update', event.id), options);
        } else {
            post(route('events.store'), options);
        }
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow">
            <h2 className="mb-6 text-2xl font-semibold text-gray-900">{isEditing ? 'Edit Event' : 'Create Event'}</h2>

            <form className="space-y-6">
                <div>
                    <label htmlFor="title" className="block text-sm font-medium text-gray-700">
                        Title
                    </label>
                    <input
                        type="text"
                        id="title"
                        value={data.title}
                        onChange={e => setData('title', e.target.value)}
                        className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                    />
                    {errors.title && <p className="mt-1 text-sm text-red-600">{errors.title}</p>}
                </div>

                <div>
                    <label className="flex items-center">
                        <input
                            type="checkbox"
                            checked={data.all_day}
                            onChange={e => setData('all_day', e.target.checked)}
                            className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span className="ml-2 text-sm text-gray-700">All Day</span>
                    </label>
                </div>

                <div className="flex flex-col gap-6 sm:flex-row">
                    <div>
                        <label htmlFor="starts_at" className="block text-sm font-medium text-gray-700">
                            Starts At
                        </label>
                        <DatePicker
                            id="starts_at"
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
                            className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                        />
                        {errors.starts_at && <p className="mt-1 text-sm text-red-600">{errors.starts_at}</p>}
                    </div>

                    {!data.all_day && (
                        <div>
                            <label htmlFor="ends_at" className="block text-sm font-medium text-gray-700">
                                Ends At
                            </label>
                            <DatePicker
                                id="ends_at"
                                selected={data.ends_at ? new Date(data.ends_at) : null}
                                onChange={date =>
                                    setData('ends_at', date ? formatWithFormat(date, 'YYYY-MM-DD HH:mm:ss') : '')
                                }
                                showTimeSelect
                                timeFormat="HH:mm"
                                timeIntervals={15}
                                dateFormat="dd/MM/yyyy HH:mm"
                                calendarStartDay={1}
                                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                            />
                            {errors.ends_at && <p className="mt-1 text-sm text-red-600">{errors.ends_at}</p>}
                        </div>
                    )}
                </div>

                <div>
                    <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                        Description
                    </label>
                    <textarea
                        id="description"
                        value={data.description}
                        onChange={e => setData('description', e.target.value)}
                        rows={4}
                        className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                    />
                    {errors.description && <p className="mt-1 text-sm text-red-600">{errors.description}</p>}
                </div>

                <div className="flex items-center justify-between gap-3">
                    <a
                        href={route('events.index')}
                        className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                    >
                        Cancel
                    </a>
                    <div className="flex gap-3">
                        <button
                            type="button"
                            onClick={e => handleSubmit(e, false)}
                            disabled={processing}
                            className="rounded-md border border-blue-600 bg-white px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                        >
                            {processing
                                ? isEditing
                                    ? 'Updating...'
                                    : 'Creating...'
                                : isEditing
                                  ? 'Update & Back to List'
                                  : 'Create & Back to List'}
                        </button>
                        <button
                            type="button"
                            onClick={e => handleSubmit(e, true)}
                            disabled={processing}
                            className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                        >
                            {processing
                                ? isEditing
                                    ? 'Updating...'
                                    : 'Creating...'
                                : isEditing
                                  ? 'Update & View'
                                  : 'Create & View'}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    );
};

export default Form;
