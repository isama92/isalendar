import ProviderLogo from '@/Components/ProviderLogo';
import { router } from '@inertiajs/react';
import { useState } from 'react';
const Create = ({ providers }) => {
    const [processing, setProcessing] = useState(false);

    const handleChooseProvider = providerId => {
        setProcessing(true);
        router.post(route('providers.store'), { provider_id: providerId });
    };

    return (
        <div className="mx-auto max-w-lg rounded-lg bg-white p-6 px-4 shadow sm:px-6 dark:bg-gray-800">
            <h2 className="mb-6 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                {__('providers.pick_provider')}
            </h2>

            <div>
                <ul role="list" className="divide-y divide-gray-100 dark:divide-white/5">
                    {providers.map(provider => (
                        <li key={provider.id} className="flex items-center justify-between gap-x-6 py-5">
                            <div className="flex min-w-0 items-center gap-x-4">
                                <ProviderLogo
                                    image_path={provider.image_path}
                                    alt={provider.name}
                                    className="size-7 flex-none rounded-full bg-gray-50 dark:bg-gray-800 dark:outline dark:-outline-offset-1 dark:outline-white/10"
                                />
                                <div className="min-w-0 flex-auto">
                                    <p className="text-sm/6 font-semibold text-gray-900 dark:text-white">
                                        {provider.name}
                                    </p>
                                    <p className="mt-1 truncate text-xs/5 text-gray-500 dark:text-gray-400">status</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                onClick={() => handleChooseProvider(provider.id)}
                                disabled={processing}
                                className="cursor-pointer rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 disabled:opacity-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"
                            >
                                {__('common.choose')}
                            </button>
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default Create;
