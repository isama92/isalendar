import { useTheme } from '@/Contexts/ThemeContext';
import { MoonIcon, SunIcon } from '@heroicons/react/20/solid';
import clsx from 'clsx';

const ThemeSwitcher = () => {
    const { theme, toggleTheme } = useTheme();

    return (
        <div className="grid grid-cols-1">
            <div className="relative z-0 inline-grid grid-cols-2 gap-0.5 rounded-full bg-gray-950/5 p-0.5 text-gray-950 dark:bg-white/10 dark:text-white">
                <div
                    className={clsx(
                        'relative rounded-full p-1 *:size-5',
                        theme === 'light'
                            ? 'bg-white ring inset-ring ring-gray-950/10 inset-ring-white/10 dark:bg-gray-600 dark:text-white dark:ring-transparent'
                            : ''
                    )}
                >
                    <button
                        type="button"
                        className="absolute inset-0 cursor-pointer appearance-none"
                        aria-label={'Light Theme'}
                        onClick={toggleTheme}
                    />
                    <SunIcon aria-hidden="true" />
                </div>
                <div
                    className={clsx(
                        'relative rounded-full p-1 *:size-5',
                        theme === 'dark'
                            ? 'bg-white ring inset-ring ring-gray-950/10 inset-ring-white/10 dark:bg-gray-600 dark:text-white dark:ring-transparent'
                            : ''
                    )}
                >
                    <button
                        type="button"
                        className="absolute inset-0 cursor-pointer appearance-none"
                        aria-label={'Dark Theme'}
                        onClick={toggleTheme}
                    />
                    <MoonIcon aria-hidden="true" />
                </div>
            </div>
        </div>
    );
};

export default ThemeSwitcher;
