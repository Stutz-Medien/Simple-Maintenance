module.exports = {
    ...require('@wordpress/prettier-config'),
    printWidth: 80,
    tabWidth: 4,
    useTabs: false,
    singleQuote: true,
    trailingComma: 'es5',
    bracketSpacing: true,
    overrides: [
        {
            files: '*.php',
            options: {
                phpVersion: '8.3', // or the version you're using
            },
        },
    ],
};
