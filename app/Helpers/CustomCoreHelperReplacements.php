<?php
if (! function_exists('trans')) {

    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function trans($key = null, $replace = [], $locale = null)
    {

        if (is_null($key)) {

            return app('translator');

        } else  if (app('translator')->get($key, $replace, $locale) == $key) {

            return app('translator')
                ->get('global.'. substr($key, strrpos($key, '.') + 1), $replace, $locale);
        }

        return app('translator')->get($key, $replace, $locale);
    }
}
