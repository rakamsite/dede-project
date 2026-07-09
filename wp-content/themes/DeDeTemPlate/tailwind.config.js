/** @type {import('tailwindcss').Config} */
module.exports = {
    // prefix:'dg-',
    content: [
        './**.php',
        './template/**.php',
        './template/MegaMenu/**.php',
        './woocommerce/**.php',
        './woocommerce/**/**.php',
        './assets/js/**.js',
        './node_modules/flowbite/**/*.js',
        './ajax/**/**/**.{js,php}',
        './video/**.php',
        './footer.php'
        // './woocommerce/emails/customer-completed-order.php',
    ],
    plugins: [
        require('@tailwindcss/typography'),
    ],
}

