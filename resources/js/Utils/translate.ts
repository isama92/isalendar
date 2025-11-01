type TranslationReplacements = Record<string, string | number>;
type TranslationObject = Record<string, unknown>;

// Global __ function for use outside of components
let globalTranslations: TranslationObject = {};

export const setGlobalTranslations = (translations: TranslationObject): void => {
    globalTranslations = translations;
};

export const __ = (key: string, replacements: TranslationReplacements = {}): string => {
    const keys = key.split('.');
    let translation: unknown = globalTranslations;

    for (const k of keys) {
        if (translation && typeof translation === 'object' && k in translation) {
            translation = (translation as Record<string, unknown>)[k];
        } else {
            return key;
        }
    }

    if (typeof translation !== 'string') {
        return key;
    }

    return Object.keys(replacements).reduce((result, placeholder) => {
        return result.replace(`:${placeholder}`, String(replacements[placeholder]));
    }, translation);
};
