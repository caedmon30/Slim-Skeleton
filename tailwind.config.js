

/** @type {import('tailwindcss').Config} */
const { fontFamily } = require("tailwindcss/defaultTheme");
module.exports = {

    content: [
        './templates/**/*.twig',
        './public/assets/js/**/*.js',
        './node_modules/flowbite/**/*.js',
        "./node_modules/tw-elements/js/**/*.js"
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['InterVariable', ...fontFamily.sans],
            },
        },
    },
    plugins: [
        require('flowbite/plugin'),
        require("tw-elements/plugin.cjs")
    ],
}