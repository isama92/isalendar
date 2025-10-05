import pluginJs from '@eslint/js';
import eslintConfigPrettier from 'eslint-config-prettier';
import pluginReact from 'eslint-plugin-react';
import globals from 'globals';

export default [
    pluginJs.configs.recommended,
    pluginReact.configs.flat.recommended,
    {
        rules: {
            'react/react-in-jsx-scope': 0,
            'react/prop-types': 0,
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
