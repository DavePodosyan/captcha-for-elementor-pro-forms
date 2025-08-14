import js from '@eslint/js';
import prettier from 'eslint-config-prettier';

export default [
    js.configs.recommended,
    prettier,
    {
        files: ['assets/js/**/*.js'],
        languageOptions: {
            ecmaVersion: 2020,
            sourceType: 'script',
            globals: {
                window: 'readonly',
                document: 'readonly',
                jQuery: 'readonly',
                elementorModules: 'readonly',
                elementorFrontend: 'readonly',
                hcaptcha: 'readonly',
                turnstile: 'readonly',
                setTimeout: 'readonly',
                clearTimeout: 'readonly',
                MutationObserver: 'readonly',
            },
        },
        rules: {
            'no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
            'no-console': 'warn',
            'prefer-const': 'error',
            'no-var': 'error',
            eqeqeq: 'error',
            curly: 'error',
            'brace-style': ['error', '1tbs'],
            indent: ['error', 4],
            quotes: ['error', 'single'],
            semi: ['error', 'always'],
        },
    },
];
