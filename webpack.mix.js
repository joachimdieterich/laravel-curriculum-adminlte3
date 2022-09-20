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

mix.js(['resources/js/app.js', 'vendor/select2/select2/dist/js/select2.min.js'], 'public/js')
   .js('node_modules/popper.js/dist/popper.js', 'public/js').sourceMaps()
   .sass('resources/sass/app.scss', 'public/css')

mix.copyDirectory('node_modules/datatables.net-buttons/js', 'public/js/datatables.net-buttons/');

/* TinyMCE */
/*mix.copyDirectory('node_modules/tinymce/plugins', 'public/node_modules/tinymce/plugins');
mix.copyDirectory('node_modules/tinymce/skins', 'public/node_modules/tinymce/skins');
mix.copyDirectory('node_modules/tinymce/themes', 'public/node_modules/tinymce/themes');
mix.copyDirectory('node_modules/tinymce', 'public/node_modules/tinymce');*/

/* Bootstrap colorpicker */
//mix.copyDirectory('node_modules/bootstrap-colorpicker/dist', 'public/node_modules/bootstrap-colorpicker');
/* Bootstrap datetimepicker */
//mix.copyDirectory('node_modules/@activix/bootstrap-datetimepicker/js', 'public/node_modules/bootstrap-datetimepicker');
/* Datatables */
/*mix.copyDirectory('node_modules/datatables.net/js', 'public/node_modules/datatables.net/js');
mix.copyDirectory('node_modules/datatables.net-bs4/js', 'public/node_modules/datatables.net-bs4/js');

mix.copyDirectory('node_modules/datatables.net-select/js', 'public/node_modules/datatables.net-select/js');*/
/* moment */
//mix.copyDirectory('node_modules/moment/min', 'public/node_modules/moment/js');
/* mathjax */
/*mix.copyDirectory('node_modules/mathjax/es5', 'public/node_modules/mathjax/es5');
mix.copyDirectory('node_modules/@dimakorotkov/tinymce-mathjax', 'public/node_modules/@dimakorotkov/tinymce-mathjax');*/

mix.version();
mix.vue({version: 2});
