import TimezoneSelect from '@/Components/TimezoneSelect';
import UserPhoto from '@/Components/UserPhoto';
import { useForm } from '@inertiajs/react';
import { useRef, useState } from 'react';

const Edit = ({ user, timezones }) => {
    const { data, setData, post, processing, reset, transform, errors } = useForm({
        first_name: user.first_name || '',
        last_name: user.last_name || '',
        default_timezone: user.settings.default_timezone,
        current_password: '',
        password: '',
        password_confirmation: '',
        photo: null,
    });

    const [photoPreview, setPhotoPreview] = useState(null);
    const fileInputRef = useRef(null);

    const handlePhotoChange = e => {
        const file = e.target.files[0];
        if (file) {
            setData('photo', file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setPhotoPreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = e => {
        e.preventDefault();

        transform(() => {
            return {
                ...data,
                _method: 'put',
            };
        });

        post(route('profile.update'), {
            onSuccess: () => {
                setPhotoPreview(null);
                reset('photo', 'current_password', 'password', 'password_confirmation');
            },
        });
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <form>
                <div className="space-y-12">
                    <div className="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
                        <div>
                            <h2 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                                {__('profile.profile')}
                            </h2>
                        </div>

                        <div className="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                            <div className="sm:col-span-3">
                                <label
                                    htmlFor="first_name"
                                    className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                >
                                    {__('profile.first_name')}
                                </label>
                                <div className="mt-2">
                                    <input
                                        id="first_name"
                                        name="first_name"
                                        type="text"
                                        placeholder={__('profile.first_name')}
                                        autoComplete="given-name"
                                        value={data.first_name}
                                        onChange={e => setData('first_name', e.target.value)}
                                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                                    />
                                    {errors.first_name && (
                                        <p
                                            id="first_name-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {errors.first_name}
                                        </p>
                                    )}
                                </div>
                            </div>
                            <div className="sm:col-span-3">
                                <label
                                    htmlFor="last_name"
                                    className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                >
                                    {__('profile.last_name')}
                                </label>
                                <div className="mt-2">
                                    <input
                                        id="last_name"
                                        name="last_name"
                                        type="text"
                                        placeholder={__('profile.last_name')}
                                        autoComplete="family-name"
                                        value={data.last_name}
                                        onChange={e => setData('last_name', e.target.value)}
                                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                                    />
                                    {errors.last_name && (
                                        <p
                                            id="last_name-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {errors.last_name}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="col-span-full">
                                <label
                                    htmlFor="photo"
                                    className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                >
                                    {__('profile.photo')}
                                </label>
                                <div className="mt-2 flex items-center gap-x-3">
                                    {photoPreview ? (
                                        <img
                                            src={photoPreview}
                                            alt={__('profile.photo')}
                                            className="size-12 rounded-full object-cover"
                                        />
                                    ) : (
                                        <UserPhoto user={user} className="size-12" iconClassName="size-12" />
                                    )}
                                    <input
                                        ref={fileInputRef}
                                        type="file"
                                        id="photo"
                                        name="photo"
                                        accept="image/*"
                                        onChange={handlePhotoChange}
                                        className="hidden"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => fileInputRef.current?.click()}
                                        className="cursor-pointer rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20"
                                    >
                                        {__('actions.choose_image')}
                                    </button>
                                </div>
                                {errors.photo && (
                                    <p
                                        id="photo-error"
                                        role="alert"
                                        className="mt-2 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {errors.photo}
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
                        <div>
                            <h2 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                                {__('profile.app_settings')}
                            </h2>
                        </div>

                        <div className="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                            <div className="sm:col-span-4">
                                <label
                                    htmlFor="default_timezone"
                                    className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                >
                                    {__('profile.default_timezone')}
                                </label>
                                <div className="mt-2">
                                    <TimezoneSelect
                                        id="default_timezone"
                                        name="default_timezone"
                                        value={data.default_timezone}
                                        onChange={option => setData('default_timezone', option?.value)}
                                        options={timezones}
                                        error={errors.default_timezone}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3 dark:border-white/10">
                        <div>
                            <h2 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                                {__('profile.security')}
                            </h2>
                            <p className="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">
                                {__('profile.leave_blank_to_keep_current')}
                            </p>
                        </div>

                        <div className="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                            <div className="sm:col-span-4">
                                <label
                                    htmlFor="current_password"
                                    className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                >
                                    {__('profile.current_password')}
                                </label>
                                <div className="mt-2">
                                    <input
                                        id="current_password"
                                        name="current_password"
                                        type="password"
                                        autoComplete="current-password"
                                        value={data.current_password}
                                        onChange={e => setData('current_password', e.target.value)}
                                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                                    />
                                    {errors.current_password && (
                                        <p
                                            id="current_password-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {errors.current_password}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="sm:col-span-4">
                                <label
                                    htmlFor="password"
                                    className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                >
                                    {__('profile.new_password')}
                                </label>
                                <div className="mt-2">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        autoComplete="new-password"
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                                    />
                                    {errors.password && (
                                        <p
                                            id="password-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {errors.password}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="sm:col-span-4">
                                <label
                                    htmlFor="password_confirmation"
                                    className="block text-sm/6 font-medium text-gray-900 dark:text-white"
                                >
                                    {__('profile.confirm_password')}
                                </label>
                                <div className="mt-2">
                                    <input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        autoComplete="new-password"
                                        value={data.password_confirmation}
                                        onChange={e => setData('password_confirmation', e.target.value)}
                                        className="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"
                                    />
                                    {errors.password_confirmation && (
                                        <p
                                            id="password_confirmation-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {errors.password_confirmation}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="mt-6 flex items-center justify-end gap-x-6">
                    <button
                        type="button"
                        onClick={handleSubmit}
                        disabled={processing}
                        className="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:focus-visible:outline-indigo-500"
                    >
                        {processing ? __('common.updating') : __('common.update')}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default Edit;
