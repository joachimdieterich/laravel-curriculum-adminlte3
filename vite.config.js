import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import commonjs from 'vite-plugin-commonjs';
import copy from 'rollup-plugin-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                },
            },
        }),
        commonjs(),
        copy({
            targets: [
                { src: 'node_modules/tinymce', dest: 'public/node_modules' },
                { src: 'resources/js/langs', dest: 'public/node_modules/tinymce' },
                { src: 'node_modules/mathjax/es5', dest: 'public/node_modules/mathjax' },
                { src: 'node_modules/@dimakorotkov', dest: 'public/node_modules' },
            ],
            verbose: true,
        }),
    ],
    resolve: {
        alias: {
          vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});