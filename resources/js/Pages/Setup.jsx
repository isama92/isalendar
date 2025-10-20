import { useForm } from '@inertiajs/react';

const Setup = () => {
    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = e => {
        e.preventDefault();
        post(route('setup.submit'));
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div>
                <label htmlFor="first_name" className="block text-sm/6 font-medium text-gray-900 dark:text-gray-100">
                    {__('users.first_name')}
                </label>
                <div className="mt-2">
                    <input
                        id="first_name"
                        name="first_name"
                        type="text"
                        value={data.first_name}
                        onChange={e => setData('first_name', e.target.value)}
                        required
                        autoComplete="given-name"
                        aria-invalid={!!errors.first_name}
                        aria-describedby={errors.first_name ? 'first-name-error' : ''}
                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                    />
                    {errors.first_name && (
                        <p id="first-name-error" role="alert" className="mt-1 text-sm text-red-600">
                            {errors.first_name}
                        </p>
                    )}
                </div>
            </div>

            <div>
                <label htmlFor="last_name" className="block text-sm/6 font-medium text-gray-900 dark:text-gray-100">
                    {__('users.last_name')}
                </label>
                <div className="mt-2">
                    <input
                        id="last_name"
                        name="last_name"
                        type="text"
                        value={data.last_name}
                        onChange={e => setData('last_name', e.target.value)}
                        required
                        autoComplete="family-name"
                        aria-invalid={!!errors.last_name}
                        aria-describedby={errors.last_name ? 'last-name-error' : ''}
                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                    />
                    {errors.last_name && (
                        <p id="last-name-error" role="alert" className="mt-1 text-sm text-red-600">
                            {errors.last_name}
                        </p>
                    )}
                </div>
            </div>

            <div>
                <label htmlFor="email" className="block text-sm/6 font-medium text-gray-900 dark:text-gray-100">
                    {__('users.email')}
                </label>
                <div className="mt-2">
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value={data.email}
                        onChange={e => setData('email', e.target.value)}
                        required
                        autoComplete="email"
                        aria-invalid={!!errors.email}
                        aria-describedby={errors.email ? 'email-error' : ''}
                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                    />
                    {errors.email && (
                        <p id="email-error" role="alert" className="mt-1 text-sm text-red-600">
                            {errors.email}
                        </p>
                    )}
                </div>
            </div>

            <div>
                <label htmlFor="password" className="block text-sm/6 font-medium text-gray-900 dark:text-gray-100">
                    {__('users.password')}
                </label>
                <div className="mt-2">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        value={data.password}
                        onChange={e => setData('password', e.target.value)}
                        required
                        autoComplete="new-password"
                        aria-invalid={!!errors.password}
                        aria-describedby={errors.password ? 'password-error' : ''}
                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                    />
                    {errors.password && (
                        <p id="password-error" role="alert" className="mt-1 text-sm text-red-600">
                            {errors.password}
                        </p>
                    )}
                </div>
            </div>

            <div>
                <label
                    htmlFor="password_confirmation"
                    className="block text-sm/6 font-medium text-gray-900 dark:text-gray-100"
                >
                    {__('users.password_confirmation')}
                </label>
                <div className="mt-2">
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        value={data.password_confirmation}
                        onChange={e => setData('password_confirmation', e.target.value)}
                        required
                        autoComplete="new-password"
                        aria-invalid={!!errors.password_confirmation}
                        aria-describedby={errors.password_confirmation ? 'password-confirmation-error' : ''}
                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                    />
                    {errors.password_confirmation && (
                        <p id="password-confirmation-error" role="alert" className="mt-1 text-sm text-red-600">
                            {errors.password_confirmation}
                        </p>
                    )}
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    disabled={processing}
                    className="flex w-full cursor-pointer justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                >
                    {processing ? __('actions.creating') : __('actions.named.create', { name: __('users.user') })}
                </button>
            </div>
        </form>
    );
};

export default Setup;
