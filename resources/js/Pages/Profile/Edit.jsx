import { useForm, usePage } from '@inertiajs/react';

const Edit = ({ user }) => {
    const { flash } = usePage().props;
    const { data, setData, put, processing, errors } = useForm({
        name: user.name || '',
    });

    const handleSubmit = e => {
        e.preventDefault();
        put(route('profile.update'));
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow">
            <h2 className="mb-6 text-2xl font-semibold text-gray-900">Edit Profile</h2>

            {flash?.success && (
                <div className="mb-4 rounded-md bg-green-50 p-4">
                    <p className="text-sm text-green-800">{flash.success}</p>
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                    <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                        Name
                    </label>
                    <input
                        type="text"
                        id="name"
                        value={data.name}
                        onChange={e => setData('name', e.target.value)}
                        className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                    />
                    {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                </div>

                <div className="flex items-center justify-end gap-3">
                    <a
                        href={route('dashboard')}
                        className="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        disabled={processing}
                        className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                    >
                        {processing ? 'Updating...' : 'Update Profile'}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default Edit;
