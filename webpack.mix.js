const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

const domain = 'dagobah.test'; // <== edit this one
const homedir = require('os').homedir();

mix.js('resources/js/app.js', 'public/js').vue({
        extractVueStyles: true,
        globalVueStyles: false
    })
    .sass('resources/sass/app.scss', 'public/css')
    .options({
        postCss: [
            require('postcss-import'),
            require('tailwindcss'),
        ]
    })
    .webpackConfig(require('./webpack.config'))

    .browserSync({
        proxy: 'https://' + domain,
        host: domain,
        https: {
            key: homedir + '/.config/valet/Certificates/' + domain + '.key',
            cert: homedir + '/.config/valet/Certificates/' + domain + '.crt',
        },
    })


if (mix.inProduction()) {
    mix.version();
}
