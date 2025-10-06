import { createInertiaApp } from '@inertiajs/react';
import dayjs from 'dayjs';
import 'dayjs/locale/it';
import updateLocale from 'dayjs/plugin/updateLocale';
import { createRoot } from 'react-dom/client';
import { route } from 'ziggy-js';
import Layout from './Components/Layout';
import { ThemeProvider } from './Contexts/ThemeContext';
import { __, setGlobalTranslations } from './utils/translate';

dayjs.extend(updateLocale);

window.route = route;
window.__ = __;

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        const page = pages[`./Pages/${name}.jsx`];

        page.default.layout = page.default.layout || (page => <Layout>{page}</Layout>);

        return page;
    },
    setup({ el, App, props }) {
        if (props.initialPage.props.translations) {
            setGlobalTranslations(props.initialPage.props.translations);
        }

        if (props.initialPage.props.locale) {
            dayjs.locale(props.initialPage.props.locale);
        }

        // Set Monday as the first day of the week
        dayjs.updateLocale('en', { weekStart: 1 });
        dayjs.updateLocale('it', { weekStart: 1 });

        createRoot(el).render(
            <ThemeProvider>
                <App {...props} />
            </ThemeProvider>
        );
    },
});
