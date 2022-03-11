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

mix.js('resources/js/app.js', 'public/js').vue()
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ])
    .webpackConfig(require('./webpack.config'))

    /**
     *  I needed to add the following configuration rules because i was getting an
     *  error on the "resources/js/app.js" file while importing Element Plus using
     *  the folling import statement "import ElementPlus from 'element-plus';"
     *
     *  I researched this error and found that the following "webpackConfig" works
     *  to solve the issue. Refer to the github conversation:
     *
     *  Issue: https://github.com/element-plus/element-plus/issues/4132
     *
     *  Remove the following code line and run "npm run watch" to reproduce the
     *  issue and the associated error.
     */
    .webpackConfig({ module: { rules: [{ test: /\.mjs$/, resolve: { fullySpecified: false }, include: /node_modules/, type: "javascript/auto" }] }, });

if (mix.inProduction()) {
    mix.version();
}
