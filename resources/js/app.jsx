import Layout from '@/Components/Layout';
import { ThemeProvider } from '@/Contexts/ThemeContext';
import { __, setGlobalTranslations } from '@/Utils/translate';
import { createInertiaApp } from '@inertiajs/react';
import axios from 'axios';
import dayjs from 'dayjs';
import 'dayjs/locale/it';
import timezone from 'dayjs/plugin/timezone';
import updateLocale from 'dayjs/plugin/updateLocale';
import utc from 'dayjs/plugin/utc';
import { createRoot } from 'react-dom/client';
import { route } from 'ziggy-js';

dayjs.extend(updateLocale);
dayjs.extend(utc);
dayjs.extend(timezone);

// Configure Axios to use the same origin as the current page
axios.defaults.baseURL = window.location.origin;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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

        if (props.initialPage.props.currentUser?.settings?.default_timezone) {
            dayjs.tz.setDefault(props.initialPage.props.currentUser.settings.default_timezone);
        }

        createRoot(el).render(
            <ThemeProvider>
                <App {...props} />
            </ThemeProvider>
        );
    },
});
