// Generated using webpack-cli https://github.com/webpack/webpack-cli
const path = require('path');
const isProduction = process.env.NODE_ENV == 'production';
let webpack = require('webpack');


const config = {
    entry: [
        // '/node_modules/flowbite/dist/flowbite.min.js',
        '/assets/js/dedeDev1.js',
        '/assets/js/AjaxHandle.js'
    ],
    output: {
        path: path.resolve(__dirname, 'assets/js'),
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
        })
    ]
};
module.exports = () => {
    if (isProduction) {
        config.mode = 'production';

    } else {
        config.mode = 'development';
    }
    return config;
};
