import { router, useForm, usePage } from '@inertiajs/react';

const Edit = ({ user, calendar }) => {
    const { flash } = usePage().props;
    const { data, setData, put, processing, errors } = useForm({
        name: user.name || '',
    });

    const handleSubmit = e => {
        e.preventDefault();
        put(route('profile.update'));
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h2 className="mb-6 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {__('profile.edit_profile')}
            </h2>

            {flash?.success && (
                <div className="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <p className="text-sm text-green-800 dark:text-green-300">{flash.success}</p>
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {__('profile.name')}
                    </label>
                    <input
                        type="text"
                        id="name"
                        value={data.name}
                        onChange={e => setData('name', e.target.value)}
                        className="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    />
                    {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                </div>

                {/* Google Calendar Integration */}
                <div className="rounded-md border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                    <h3 className="mb-3 text-lg font-medium text-gray-900 dark:text-gray-100">
                        {__('profile.google_calendar_integration')}
                    </h3>
                    {calendar ? (
                        <div className="space-y-4">
                            <p className="text-sm font-medium text-green-700 dark:text-green-400">
                                {__('profile.google_calendar_status_connected')}
                            </p>

                            {/* Calendar details */}
                            <div className="mx-auto max-w-xl">
                                <div className="rounded-xl bg-gray-800 p-6 text-white shadow-lg">
                                    <h2 className="text-xl font-semibold">{calendar.external_calendar_name}</h2>
                                    <p className="mt-1 text-gray-300">{calendar.external_calendar_description}</p>
                                    <p className="mt-2 text-sm text-gray-400 capitalize">
                                        {__('profile.timezone')}: {calendar.external_calendar_timezone}
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                onClick={() => {
                                    if (confirm(__('profile.google_calendar_disconnect_confirm'))) {
                                        router.post(route('services.google.disconnect'));
                                    }
                                }}
                                className="cursor-pointer rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none dark:border-red-600 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"
                            >
                                {__('profile.google_calendar_disconnect')}
                            </button>
                        </div>
                    ) : (
                        <div className="space-y-3">
                            <p className="text-sm text-gray-600 dark:text-gray-400">
                                {__('profile.google_calendar_status_disconnected')}
                            </p>
                            <a
                                href={route('services.google.connect')}
                                className="inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                            >
                                {__('profile.google_calendar_connect')}
                            </a>
                        </div>
                    )}
                </div>

                <div className="flex items-center justify-end gap-3">
                    <a
                        href={route('dashboard')}
                        className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        {__('common.cancel')}
                    </a>
                    <button
                        type="submit"
                        disabled={processing}
                        className="cursor-pointer rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                    >
                        {processing ? __('common.updating') : __('profile.update_profile')}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default Edit;
