import { Link, useForm } from '@inertiajs/react';

const Create = ({ providers }) => {
    const { data, setData, post, processing, errors } = useForm({
        provider_id: '',
    });

    const handleSubmit = e => {
        e.preventDefault();
        post(route('providers.store'));
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h2 className="mb-6 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {__('providers.pick_provider')}
            </h2>

            <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid gap-4 sm:grid-cols-2">
                    {providers.map(provider => (
                        <label
                            key={provider.id}
                            className={`relative cursor-pointer rounded-lg border p-4 shadow-sm transition hover:shadow-md dark:bg-gray-700 ${
                                data.provider_id === provider.id
                                    ? 'border-blue-600 ring-2 ring-blue-600'
                                    : 'border-gray-300 dark:border-gray-600'
                            }`}
                        >
                            <input
                                type="radio"
                                name="provider"
                                value={provider.id}
                                checked={data.provider_id === provider.id}
                                onChange={() => setData('provider_id', provider.id)}
                                className="sr-only"
                            />
                            <div className="space-y-1">
                                <p className="text-lg font-medium text-gray-900 dark:text-gray-100">{provider.name}</p>
                            </div>
                        </label>
                    ))}
                </div>

                {errors.provider_id && <p className="text-sm text-red-600">{errors.provider_id}</p>}

                <div className="flex items-center justify-end gap-3">
                    <Link
                        href={route('calendars.index')}
                        className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        {__('common.cancel')}
                    </Link>
                    <button
                        type="submit"
                        disabled={processing}
                        className="cursor-pointer rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white capitalize hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                    >
                        {processing ? __('common.processing') : __('common.choose')}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default Create;
