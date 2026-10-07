/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                // Warna utama SCANIC TRACE: biru netral, kesan profesional & clean
                brand: {
                    50: '#f0f5ff',
                    100: '#dbe7ff',
                    500: '#3b5bdb',
                    600: '#2f4bc7',
                    700: '#263ea0',
                },
            },
        },
    },
    plugins: [],
};
