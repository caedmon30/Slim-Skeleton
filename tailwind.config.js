

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
        screens: {
            'sm': '640px',
            'md': '768px',
            'normal': '820px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
        },
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