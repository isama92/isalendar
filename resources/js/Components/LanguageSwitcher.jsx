import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import { LanguageIcon } from '@heroicons/react/24/outline';
import { router } from '@inertiajs/react';

const LanguageSwitcher = ({ locale }) => {
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
            <MenuButton className="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
                <LanguageIcon aria-hidden="true" className="size-5" />
            </MenuButton>

            <MenuItems
                transition
                className="absolute right-0 z-10 mt-2 w-32 origin-top-right rounded-md bg-white shadow-lg outline-1 outline-black/5 transition data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10"
            >
                <div className="py-1">
                    <MenuItem>
                        <button
                            type="button"
                            onClick={() => switchLanguage('en')}
                            className={`flex w-full items-center justify-between px-4 py-2 text-sm data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden ${
                                locale === 'en'
                                    ? 'bg-blue-50 font-medium text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'
                                    : 'text-gray-700 dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white'
                            }`}
                        >
                            <span>English</span>
                            {locale === 'en' && <span className="text-xs">✓</span>}
                        </button>
                    </MenuItem>
                    <MenuItem>
                        <button
                            type="button"
                            onClick={() => switchLanguage('it')}
                            className={`flex w-full items-center justify-between px-4 py-2 text-sm data-focus:bg-gray-100 data-focus:text-gray-900 data-focus:outline-hidden ${
                                locale === 'it'
                                    ? 'bg-blue-50 font-medium text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'
                                    : 'text-gray-700 dark:text-gray-300 dark:data-focus:bg-white/5 dark:data-focus:text-white'
                            }`}
                        >
                            <span>Italiano</span>
                            {locale === 'it' && <span className="text-xs">✓</span>}
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </Menu>
    );
};

export default LanguageSwitcher;
