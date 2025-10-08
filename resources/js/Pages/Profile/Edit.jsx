import { useForm } from '@inertiajs/react';

const Edit = ({ user, flash }) => {
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

                <div className="flex items-center justify-end gap-3">
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
