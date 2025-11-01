import LogoDark from '@/../images/logo-dark.png';
import LogoLight from '@/../images/logo-light.png';
import LanguageSwitcher from '@/Components/UI/LanguageSwitcher';
import ThemeSwitcher from '@/Components/UI/ThemeSwitcher';
import { usePage } from '@inertiajs/react';

const GuestLayout = ({ children }) => {
    const { appName, locale, title, subtitle } = usePage().props;
    return (
        <div className="relative flex min-h-screen flex-col justify-center bg-white px-6 py-12 lg:px-8 dark:bg-gray-900">
            <div className="absolute top-6 right-6 flex items-center gap-3">
                <ThemeSwitcher />
                <LanguageSwitcher locale={locale} />
            </div>
            <div className="sm:mx-auto sm:w-full sm:max-w-sm">
                <img alt={appName} src={LogoLight} className="mx-auto h-10 w-auto dark:hidden" />
                <img alt={appName} src={LogoDark} className="mx-auto h-10 w-auto not-dark:hidden" />
                <h2 className="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">
                    {title}
                </h2>
                {subtitle && <p className="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">{subtitle}</p>}
            </div>

            <div className="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">{children}</div>
        </div>
    );
};

export default GuestLayout;
