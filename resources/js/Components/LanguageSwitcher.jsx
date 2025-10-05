import { LanguageIcon } from '@heroicons/react/24/outline';
import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';

const LanguageSwitcher = () => {
    const { locale } = usePage().props;
    const [dropdownOpen, setDropdownOpen] = useState(false);

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
        <div className="relative">
            <button
                onClick={() => setDropdownOpen(!dropdownOpen)}
                className="flex cursor-pointer items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
            >
                <LanguageIcon className="h-5 w-5" />
            </button>

            {dropdownOpen && (
                <>
                    <div className="fixed inset-0 z-10" onClick={() => setDropdownOpen(false)} />
                    <div className="absolute right-0 z-20 mt-2 w-32 rounded-md border border-gray-200 bg-white shadow-lg">
                        <button
                            onClick={() => {
                                switchLanguage('en');
                                setDropdownOpen(false);
                            }}
                            className={`flex w-full items-center justify-between px-4 py-2 text-sm ${
                                locale === 'en'
                                    ? 'bg-blue-50 font-medium text-blue-600'
                                    : 'text-gray-700 hover:bg-gray-100'
                            }`}
                        >
                            <span>English</span>
                            {locale === 'en' && <span className="text-xs">✓</span>}
                        </button>
                        <button
                            onClick={() => {
                                switchLanguage('it');
                                setDropdownOpen(false);
                            }}
                            className={`flex w-full items-center justify-between px-4 py-2 text-sm ${
                                locale === 'it'
                                    ? 'bg-blue-50 font-medium text-blue-600'
                                    : 'text-gray-700 hover:bg-gray-100'
                            }`}
                        >
                            <span>Italiano</span>
                            {locale === 'it' && <span className="text-xs">✓</span>}
                        </button>
                    </div>
                </>
            )}
        </div>
    );
};

export default LanguageSwitcher;
