import {
    Listbox,
    ListboxButton,
    ListboxOption,
    ListboxOptions,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
} from '@headlessui/react';
import { ChevronUpDownIcon } from '@heroicons/react/16/solid';
import { CheckIcon, ChevronDownIcon } from '@heroicons/react/20/solid';
import { useForm } from '@inertiajs/react';
import clsx from 'clsx';

const Form = ({ roles, user = null }) => {
    const isEditing = !!user;

    const { data, setData, post, put, processing, transform, errors, reset } = useForm({
        first_name: user?.first_name || '',
        last_name: user?.last_name || '',
        email: user?.email || '',
        password: '',
        role: user?.roles?.[0]?.name || '',
    });

    const handleSubmit = (e, redirectTo = null) => {
        e.preventDefault();

        const defaultRedirect = isEditing ? 'show' : 'new';
        const actualRedirect = redirectTo || defaultRedirect;

        transform(() => {
            return {
                ...data,
                redirect_to: actualRedirect,
            };
        });

        if (isEditing) {
            put(route('users.update', user.id));
            return;
        }

        post(route('users.store'), {
            onSuccess: () => {
                if (actualRedirect === 'new') {
                    reset();
                }
            },
        });
    };

    const selectedRole = roles.find(r => r.value === data.role);

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <form className="space-y-6">
                <div className="space-y-12 sm:space-y-16">
                    {/* header */}
                    <div>
                        <h2 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                            {isEditing
                                ? __('actions.named.edit', { name: __('users.user') })
                                : __('actions.named.create', { name: __('users.user') })}
                        </h2>
                    </div>

                    {/* fields */}
                    <div className="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:border-t-gray-900/10 sm:pb-0 dark:border-white/10 dark:sm:divide-white/10 dark:sm:border-t-white/10">
                        {/* first name */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="first_name"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('users.first_name')}
                            </label>
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <div className="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 sm:max-w-md dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                                    <input
                                        id="first_name"
                                        name="first_name"
                                        type="text"
                                        placeholder={__('users.first_name')}
                                        value={data.first_name}
                                        onChange={e => setData('first_name', e.target.value)}
                                        aria-invalid={!!errors.first_name}
                                        aria-describedby={errors.first_name ? 'first_name-error' : ''}
                                        className={clsx(
                                            'block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base focus:outline-none sm:text-sm/6 dark:bg-transparent',
                                            errors.first_name
                                                ? 'text-red-900 placeholder:text-red-300 dark:text-red-400 dark:placeholder:text-red-400/70'
                                                : 'text-gray-900 placeholder:text-gray-400 dark:text-white dark:placeholder:text-gray-500'
                                        )}
                                    />
                                </div>
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

                        {/* last name */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="last_name"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('users.last_name')}
                            </label>
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <div className="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 sm:max-w-md dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                                    <input
                                        id="last_name"
                                        name="last_name"
                                        type="text"
                                        placeholder={__('users.last_name')}
                                        value={data.last_name}
                                        onChange={e => setData('last_name', e.target.value)}
                                        aria-invalid={!!errors.last_name}
                                        aria-describedby={errors.last_name ? 'last_name-error' : ''}
                                        className={clsx(
                                            'block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base focus:outline-none sm:text-sm/6 dark:bg-transparent',
                                            errors.last_name
                                                ? 'text-red-900 placeholder:text-red-300 dark:text-red-400 dark:placeholder:text-red-400/70'
                                                : 'text-gray-900 placeholder:text-gray-400 dark:text-white dark:placeholder:text-gray-500'
                                        )}
                                    />
                                </div>
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

                        {/* email */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="email"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('users.email')}
                            </label>
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <div className="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 sm:max-w-md dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        placeholder={__('users.email')}
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        aria-invalid={!!errors.email}
                                        aria-describedby={errors.email ? 'email-error' : ''}
                                        className={clsx(
                                            'block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base focus:outline-none sm:text-sm/6 dark:bg-transparent',
                                            errors.email
                                                ? 'text-red-900 placeholder:text-red-300 dark:text-red-400 dark:placeholder:text-red-400/70'
                                                : 'text-gray-900 placeholder:text-gray-400 dark:text-white dark:placeholder:text-gray-500'
                                        )}
                                    />
                                </div>
                                {errors.email && (
                                    <p
                                        id="email-error"
                                        role="alert"
                                        className="mt-2 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {errors.email}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* role */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="role"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('users.role')}
                            </label>
                            <Listbox value={data.role} onChange={value => setData('role', value)}>
                                <div className="relative mt-2 sm:col-span-2 sm:mt-0 sm:max-w-md">
                                    <ListboxButton
                                        id="role"
                                        name="role"
                                        aria-invalid={!!errors.role}
                                        aria-describedby={errors.role ? 'role-error' : ''}
                                        className={clsx(
                                            'grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pr-2 pl-3 text-left outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6 dark:bg-white/5 dark:outline-white/10',
                                            errors.role
                                                ? 'text-red-900 dark:text-red-400'
                                                : 'text-gray-900 dark:text-white'
                                        )}
                                    >
                                        <span className="col-start-1 row-start-1 truncate pr-6">
                                            {selectedRole?.label ||
                                                __('actions.named.select', { name: __('users.role') })}
                                        </span>
                                        <ChevronUpDownIcon
                                            aria-hidden="true"
                                            className="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4 dark:text-gray-400"
                                        />
                                    </ListboxButton>

                                    <ListboxOptions
                                        transition
                                        className="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg outline-1 outline-black/5 data-leave:transition data-leave:duration-100 data-leave:ease-in data-closed:data-leave:opacity-0 sm:text-sm dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
                                    >
                                        {roles.map(role => (
                                            <ListboxOption
                                                key={role.value}
                                                value={role.value}
                                                className="group relative cursor-default py-2 pr-9 pl-3 text-gray-900 select-none data-focus:bg-indigo-600 data-focus:text-white data-focus:outline-hidden dark:text-white dark:data-focus:bg-indigo-500"
                                            >
                                                <span className="block truncate font-normal group-data-selected:font-semibold">
                                                    {role.label}
                                                </span>

                                                <span className="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 group-not-data-selected:hidden group-data-focus:text-white dark:text-indigo-400">
                                                    <CheckIcon aria-hidden="true" className="size-5" />
                                                </span>
                                            </ListboxOption>
                                        ))}
                                    </ListboxOptions>
                                    {errors.role && (
                                        <p
                                            id="role-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {errors.role}
                                        </p>
                                    )}
                                </div>
                            </Listbox>
                        </div>

                        {/* password */}
                        <div className="sm:grid sm:grid-cols-3 sm:items-start sm:gap-4 sm:py-6">
                            <label
                                htmlFor="password"
                                className="block text-sm/6 font-medium text-gray-900 sm:pt-1.5 dark:text-white"
                            >
                                {__('users.password')}
                            </label>
                            <div className="mt-2 sm:col-span-2 sm:mt-0">
                                <div className="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 sm:max-w-md dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        placeholder={__('users.password')}
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        aria-invalid={!!errors.password}
                                        aria-describedby={errors.password ? 'password-error' : 'password-help'}
                                        className={clsx(
                                            'block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base focus:outline-none sm:text-sm/6 dark:bg-transparent',
                                            errors.password
                                                ? 'text-red-900 placeholder:text-red-300 dark:text-red-400 dark:placeholder:text-red-400/70'
                                                : 'text-gray-900 placeholder:text-gray-400 dark:text-white dark:placeholder:text-gray-500'
                                        )}
                                    />
                                </div>
                                {errors.password && (
                                    <p
                                        id="password-error"
                                        role="alert"
                                        className="mt-2 text-sm text-red-600 dark:text-red-400"
                                    >
                                        {errors.password}
                                    </p>
                                )}
                                {!errors.password && (
                                    <p id="password-help" className="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        {__('common.optional')}
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                {/* actions */}
                <div className="mt-6 flex items-center justify-end gap-x-6">
                    <button
                        type="button"
                        onClick={() => window.history.back()}
                        className="cursor-pointer text-sm/6 font-semibold text-gray-900 dark:text-white"
                    >
                        {__('actions.back')}
                    </button>
                    <div className="inline-flex rounded-md shadow-xs dark:shadow-none">
                        <button
                            type="button"
                            onClick={e => handleSubmit(e)}
                            disabled={processing}
                            className="relative inline-flex items-center rounded-l-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white inset-ring-1 inset-ring-indigo-600 hover:bg-indigo-500 focus:z-10 dark:bg-indigo-500 dark:inset-ring-indigo-500 dark:hover:bg-indigo-400"
                        >
                            {processing
                                ? isEditing
                                    ? __('actions.updating')
                                    : __('actions.creating')
                                : isEditing
                                  ? __('actions.update_and_view')
                                  : __('actions.create_and_new')}
                        </button>
                        <Menu as="div" className="relative -ml-px block">
                            <MenuButton
                                disabled={processing}
                                className="relative inline-flex items-center rounded-r-md bg-indigo-600 px-2 py-2 text-white inset-ring-1 inset-ring-indigo-600 hover:bg-indigo-500 focus:z-10 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-indigo-500 dark:inset-ring-indigo-500 dark:hover:bg-indigo-400"
                            >
                                <span className="sr-only">Open options</span>
                                <ChevronDownIcon aria-hidden="true" className="size-5" />
                            </MenuButton>
                            <MenuItems
                                anchor="bottom end"
                                transition
                                className="z-10 w-56 rounded-md bg-white shadow-lg outline-1 outline-black/5 transition data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
                            >
                                <div className="py-1">
                                    {isEditing ? (
                                        <>
                                            <MenuItem>
                                                <button
                                                    type="button"
                                                    onClick={e => handleSubmit(e, 'edit')}
                                                    disabled={processing}
                                                    className="block w-full px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                                >
                                                    {__('actions.update_and_stay')}
                                                </button>
                                            </MenuItem>
                                            <MenuItem>
                                                <button
                                                    type="button"
                                                    onClick={e => handleSubmit(e, 'index')}
                                                    disabled={processing}
                                                    className="block w-full px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                                >
                                                    {__('actions.update_and_list')}
                                                </button>
                                            </MenuItem>
                                        </>
                                    ) : (
                                        <>
                                            <MenuItem>
                                                <button
                                                    type="button"
                                                    onClick={e => handleSubmit(e, 'show')}
                                                    disabled={processing}
                                                    className="block w-full px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                                >
                                                    {__('actions.create_and_view')}
                                                </button>
                                            </MenuItem>
                                            <MenuItem>
                                                <button
                                                    type="button"
                                                    onClick={e => handleSubmit(e, 'index')}
                                                    disabled={processing}
                                                    className="block w-full px-4 py-2 text-left text-sm text-gray-700 disabled:cursor-not-allowed disabled:opacity-50 data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white"
                                                >
                                                    {__('actions.create_and_list')}
                                                </button>
                                            </MenuItem>
                                        </>
                                    )}
                                </div>
                            </MenuItems>
                        </Menu>
                    </div>
                </div>
            </form>
        </div>
    );
};

export default Form;
