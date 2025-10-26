import pluginJs from '@eslint/js';
import eslintConfigPrettier from 'eslint-config-prettier';
import pluginJsxA11y from 'eslint-plugin-jsx-a11y';
import pluginReact from 'eslint-plugin-react';
import pluginReactHooks from 'eslint-plugin-react-hooks';
import globals from 'globals';

export default [
    pluginJs.configs.recommended,
    pluginReact.configs.flat.recommended,
    pluginJsxA11y.flatConfigs.recommended,
    {
        plugins: {
            'react-hooks': pluginReactHooks,
        },
        rules: {
            'react/react-in-jsx-scope': 0,
            'react/prop-types': 0,
            'react-hooks/rules-of-hooks': 'error',
            'react-hooks/exhaustive-deps': 'warn',
        },
        settings: {
            react: {
                version: 'detect',
            },
        },
    },
    {
        files: ['**/*.{js,jsx}'],
        languageOptions: {
            globals: {
                ...globals.browser,
                axios: true,
                route: true,
                __: true,
            },
        },
    },
    eslintConfigPrettier,
];
