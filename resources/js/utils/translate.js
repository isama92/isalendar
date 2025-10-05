import { usePage } from '@inertiajs/react';

export const useTranslations = () => {
    const { translations } = usePage().props;

    const __ = (key, replacements = {}) => {
        const keys = key.split('.');
        let translation = translations;

        for (const k of keys) {
            if (translation && typeof translation === 'object' && k in translation) {
                translation = translation[k];
            } else {
                return key;
            }
        }

        if (typeof translation !== 'string') {
            return key;
        }

        return Object.keys(replacements).reduce((result, placeholder) => {
            return result.replace(`:${placeholder}`, replacements[placeholder]);
        }, translation);
    };

    return { __ };
};

// Global __ function for use outside of components
let globalTranslations = {};

export const setGlobalTranslations = translations => {
    globalTranslations = translations;
};

export const __ = (key, replacements = {}) => {
    const keys = key.split('.');
    let translation = globalTranslations;

    for (const k of keys) {
        if (translation && typeof translation === 'object' && k in translation) {
            translation = translation[k];
        } else {
            return key;
        }
    }

    if (typeof translation !== 'string') {
        return key;
    }

    return Object.keys(replacements).reduce((result, placeholder) => {
        return result.replace(`:${placeholder}`, replacements[placeholder]);
    }, translation);
};
