import Checkbox from '@/Components/Checkbox';
import { LockClosedIcon } from '@heroicons/react/24/solid';
import { Link, useForm } from '@inertiajs/react';

const Login = ({ oidcEnabled, oidcButtonLabel }) => {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = e => {
        e.preventDefault();
        post(route('login.submit'));
    };

    return (
        <>
            <form onSubmit={submit} className="space-y-6">
                <div>
                    <label htmlFor="email" className="block text-sm/6 font-medium text-gray-900 dark:text-gray-100">
                        {__('auth.email')}
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
                    <div className="flex items-center justify-between">
                        <label
                            htmlFor="password"
                            className="block text-sm/6 font-medium text-gray-900 dark:text-gray-100"
                        >
                            {__('auth.password')}
                        </label>
                    </div>
                    <div className="mt-2">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            value={data.password}
                            onChange={e => setData('password', e.target.value)}
                            required
                            autoComplete="current-password"
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

                <div className="flex items-center justify-between">
                    <div className="flex gap-3">
                        <Checkbox
                            id="remember-me"
                            name="remember-me"
                            checked={data.remember}
                            onChange={e => setData('remember', e.target.checked)}
                        />
                        <label htmlFor="remember-me" className="block text-sm/6 text-gray-900 dark:text-gray-300">
                            {__('auth.remember_me')}
                        </label>
                    </div>

                    <div className="text-sm/6">
                        <Link
                            href={route('password.request')}
                            className="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            {__('auth.forgot_password')}
                        </Link>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        disabled={processing}
                        className="flex w-full cursor-pointer justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500"
                    >
                        {processing ? __('auth.logging_in') : __('auth.login')}
                    </button>
                </div>
            </form>

            {oidcEnabled && (
                <div className="mt-6">
                    <div className="relative">
                        <div className="absolute inset-0 flex items-center">
                            <div className="w-full border-t border-gray-300 dark:border-gray-700" />
                        </div>
                        <div className="relative flex justify-center text-sm/6">
                            <span className="bg-white px-2 text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                {__('auth.or_continue_with')}
                            </span>
                        </div>
                    </div>

                    <div className="mt-6">
                        <Link
                            href={route('auth.oidc.connect')}
                            className="flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white dark:bg-white/10 dark:text-white dark:ring-white/20 dark:hover:bg-white/20 dark:focus-visible:outline-white/20"
                        >
                            <LockClosedIcon className="h-5 w-5" />
                            {oidcButtonLabel}
                        </Link>
                    </div>
                </div>
            )}
        </>
    );
};

export default Login;
