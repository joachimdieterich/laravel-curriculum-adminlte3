const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js([
        'resources/js/app.js',
        'vendor/select2/select2/dist/js/select2.full.min.js'
    ], 'public/js')
    .vue({version: 2});

mix.js('node_modules/popper.js/dist/popper.js', 'public/js').sourceMaps()
   .sass('resources/sass/app.scss', 'public/css');

/* broadcasting */
mix.options({
    hmrOptions: {
    host: 'localhost',
    port:8080,
    }
});

/* TinyMCE */
mix.copyDirectory('resources/js/plugins/curriculummedia/plugin.js', 'public/node_modules/tinymce/plugins/curriculummedia/plugin.js');
/*mix.copyDirectory('node_modules/tinymce/plugins', 'public/js/plugins');
mix.copyDirectory('node_modules/tinymce/plugins', 'public/js/plugins');
mix.copyDirectory('node_modules/tinymce/skins', 'public/js/skins');
mix.copyDirectory('node_modules/tinymce/themes', 'public/js/themes');
mix.copyDirectory('node_modules/tinymce/icons', 'public/js/icons');
mix.copyDirectory('node_modules/tinymce', 'public/node_modules/tinymce');
mix.copyDirectory('resources/js/langs', 'public/node_modules/tinymce/langs');*/

/* Bootstrap colorpicker */
/*mix.copyDirectory('node_modules/bootstrap-colorpicker/dist', 'public/node_modules/bootstrap-colorpicker');*/
/* Bootstrap datetimepicker */
/*mix.copyDirectory('node_modules/@activix/bootstrap-datetimepicker/js', 'public/node_modules/bootstrap-datetimepicker');*/
/* Datatables */
/*mix.copyDirectory('node_modules/datatables.net/js', 'public/node_modules/datatables.net/js');
mix.copyDirectory('node_modules/datatables.net-bs5/js', 'public/node_modules/datatables.net-bs5/js');
mix.copyDirectory('node_modules/datatables.net-buttons', 'public/node_modules/datatables.net-buttons/');

mix.copyDirectory('node_modules/datatables.net-select/js', 'public/node_modules/datatables.net-select/js');*/
/* moment */
/*mix.copyDirectory('node_modules/moment/min', 'public/node_modules/moment/js');*/
/* mathjax */
/*mix.copyDirectory('node_modules/mathjax/es5', 'public/node_modules/mathjax/es5');
mix.copyDirectory('node_modules/@dimakorotkov/tinymce-mathjax', 'public/node_modules/@dimakorotkov/tinymce-mathjax');*/


//mix.version();
mix.vue({version: 3});
