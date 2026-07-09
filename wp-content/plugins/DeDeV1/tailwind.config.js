/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix:"dev-",
  content: [
      './src/classes/**.php',
      // './src/classes/dede_dev_ajax_pdf_maker.php'
      './assets/js/**.js',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

