import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import { LanguageIcon } from '@heroicons/react/20/solid';
import { router } from '@inertiajs/react';
import clsx from 'clsx';

const LanguageSwitcher = ({ locale }) => {
    const locales = [
        { code: 'en', label: 'English' },
        { code: 'it', label: 'Italian' },
    ];

    const switchLanguage = newLocale => {
        router.post(
            route('locale.update'),
            { locale: newLocale },
            {
                onSuccess: () => {
                    window.location.reload();
                },
            }
        );
    };

    return (
        <Menu as="div" className="relative inline-block">
            <MenuButton className="inline-flex w-full cursor-pointer justify-center gap-x-1.5 rounded-md bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
                <LanguageIcon aria-hidden="true" className="size-5" />
            </MenuButton>

            <MenuItems
                transition
                className="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg outline-1 outline-black/5 transition data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
            >
                <div className="py-1">
                    {locales.map(loc => (
                        <div key={loc.code}>
                            <MenuItem>
                                <button
                                    type="button"
                                    onClick={() => switchLanguage(loc.code)}
                                    className={clsx(
                                        'flex w-full items-center justify-between px-4 py-2 text-sm text-gray-700 dark:text-gray-300',
                                        locale === loc.code
                                            ? ''
                                            : 'cursor-pointer data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden dark:data-focus:bg-white/5 dark:data-focus:text-white'
                                    )}
                                    disabled={locale === loc.code}
                                >
                                    <span>{loc.label}</span>
                                    {locale === loc.code && <span className="text-xs">✓</span>}
                                </button>
                            </MenuItem>
                        </div>
                    ))}
                </div>
            </MenuItems>
        </Menu>
    );
};

export default LanguageSwitcher;
