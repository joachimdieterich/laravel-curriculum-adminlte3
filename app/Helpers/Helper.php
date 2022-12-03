<?php

use Illuminate\Support\Facades\DB;

if (! function_exists('getEntriesForSelect2ByModel')) {
    /**
     * helper function to paginate on select2 fields
     * @param $model
     * @param string|array $field one or multiple fields to search term
     * @param string $oderby
     * @param string $text
     * @return \Illuminate\Http\JsonResponse
     */
    function getEntriesForSelect2ByModel($model, $field = 'title', $oderby = 'title', $text = 'title', $id = 'id')
    {
        $input = request()->validate([
            'page' => 'required|integer',
            'term' => 'sometimes|string|max:255|nullable',
        ]);
        $page = $input['page'];
        $resultCount = 25;

        $offset = ($page - 1) * $resultCount;

        $term = $input['term'];

        $entries = $model::where(
            function ($query) use ($field, $term) {
                foreach ((array)$field as $f) {
                    $query->orWhere($f, 'LIKE', '%' . $term . '%');
                }
            })
            ->orderBy($oderby)
            ->skip($offset)
            ->take($resultCount)
            ->get([$id, DB::raw($text . ' as text')]);

        $count = $entries->count();
       /* $count = Count($model::where(
            function ($query) use ($field, $term) {
                foreach ((array)$field as $f) {
                    $query->orWhere($f, 'LIKE', '%' . $term . '%');
                }
            })
            ->orderBy($oderby)
            ->get([$id, DB::raw($text . ' as text')]));*/

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;
        //dump($resultCount.' '.$count);
        $results = array(
            "results" => $entries,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
if (! function_exists('getEntriesForSelect2ByCollection'))
{
    function getEntriesForSelect2ByCollection($collection, $table = '', $field = 'title', $oderby = 'title', $text = 'title', $id = 'id' )
    {
        $input = request()->validate([
            'page' => 'required|integer',
            'term' => 'sometimes|string|max:255|nullable',
        ]);
        $page = $input['page'];
        $resultCount = 25;

        $offset = ($page - 1) * $resultCount;

        $term = $input['term'];

        $entries = $collection->where(
            function($query) use ($field, $term)
            {
                foreach ((array) $field as $f) {
                    $query->orWhere($f, 'LIKE', '%' . $term . '%');
                }
            })
            ->orderBy($oderby)
            ->skip($offset)
            ->take($resultCount)
            ->select([$table.$id, DB::raw($text . ' as text')])
            ->get();

        $count = $entries->count();
        /*$count = Count($collection->where(
            function($query) use ($field, $term)
            {
                foreach ((array) $field as $f) {
                    $query->orWhere($f, 'LIKE', '%' . $term . '%');
                }
            })
            ->orderBy($oderby)
            ->select([$table.$id, DB::raw($text . ' as text')])
            ->get());*/

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        $results = array(
            "results" => $entries,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}


if (! function_exists('format_select_input')) {

    /**
     * helper function for selects
     * if input is an array, it returns the first value of array
     * else it returns the input
     *
     * returns integer
     */
    function format_select_input($input)
    {
        if (is_array($input)) {
            return Arr::first($input);
        } else {
            return $input;
        }
    }
}
if (! function_exists('getImmediateChildrenByTagName')) {
    /**
     * Traverse an elements children and collect those nodes that
     * have the tagname specified in $tagName. Non-recursive
     * Source: http://stackoverflow.com/questions/3049648/php-domelementgetelementsbytagname-anyway-to-get-just-the-immediate-matching
     *
     * @param  DOMElement  $element
     * @param  string  $tagName
     * @return array
     */
    function getImmediateChildrenByTagName(DOMElement $element, $tagName)
    {
        $result = [];
        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMElement && $child->tagName == $tagName) {
                $result[] = $child;
            }
        }

        return $result;
    }
}

if (! function_exists('relativeToAbsoutePaths')) {
    function relativeToAbsoutePaths($input)
    {
        return preg_replace_callback(
            '/<img\s+[^>]*(src="\/media\/(.*?)")(\s+[^>]*)?[^>]*>/mi',
            function ($match) {
                $media = App\Medium::find($match[2]);
                //dump($media->absolutePath());
                if (! file_exists($media->absolutePath())) {
                    return ''; //"<!--File does not exist-->"; //todo: remove from db?
                }
                if ($media !== null) {
                    return str_replace($match[1], "src=\"{$media->absolutePath()}\"", $match[0]);
                } else {
                    return ''; //"<!--Image not available-->";
                }
            },
            $input
        );
    }
}

if (! function_exists('str_singular')) {

    /**
     * helper function str_singular() translator to Str::singular
     */
    function str_singular($param)
    {
        return Str::singular($param);
    }
}

if (! function_exists('starts_with')) {

    /**
     * helper function starts_with() translator to Str::startsWith()
     */
    function starts_with($param1, $param2)
    {
        return Str::startsWith($param1, $param2);
    }
}

if (! function_exists('ends_with')) {

    /**
     * helper function starts_with() translator to Str::endsWith()
     */
    function ends_with($param1, $param2)
    {
        return Str::endsWith($param1, $param2);
    }
}

if (! function_exists('camel_case')) {

    /**
     * helper function camel_case() translator to Str::camel()
     */
    function camel_case($param)
    {
        return Str::camel($param);
    }
}

if (! function_exists('str_limit')) {

    /**
     * helper function str_limit() translator to Str::limit()
     */
    function str_limit($param1, $param2)
    {
        return Str::limit($param1, $param2);
    }
}

if (! function_exists('str_contains')) {

    /**
     * helper function str_contains() translator to Str::contains()
     */
    function str_contains($param1, $param2)
    {
        return Str::contains($param1, $param2);
    }
}

if (! function_exists('is_dir_empty')) {
    function is_dir_empty($dir)
    {
        if (! is_readable($dir)) {
            return null;
        }

        return count(scandir($dir)) == 2;
    }
}

if (! function_exists('find_all_files')) {
    function find_all_files($dir)
    {
        $result = [];
        $root = scandir($dir);
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file("$dir/$value")) {
                $result[] = "$dir/$value";
                continue;
            }
            foreach (find_all_files("$dir/$value") as $value) {
                $result[] = $value;
            }
        }

        return $result;
    }
}

if (! function_exists('now_online')) {
    function now_online()
    {

         // Get time session life time from config.
        $time = time() - (config('session.lifetime') * 60);

        // Total login users (user can be log on 2 devices will show once.)
        $totalActiveUsers = DB::table('sessions')
                 ->where('last_activity', '>=', $time)->
         count(DB::raw('DISTINCT user_id'));

        return $totalActiveUsers;
    }
}

if (! function_exists('today_online')) {
    function today_online()
    {
        $time = time() - (24 * 60 * 60); // 24 Hours

        // Total login users (user can be log on 2 devices will show once.)
        $totalActiveUsers = DB::table('sessions')
                 ->where('last_activity', '>=', $time)->
         count(DB::raw('DISTINCT user_id'));

        return $totalActiveUsers;
    }
}

if (! function_exists('is_admin')) {
    function is_admin()
    {
        return auth()->user()->role()->id == 1;
    }
}

if (! function_exists('is_schooladmin')) {
    function is_schooladmin()
    {
        return auth()->user()->role()->id == 4;
    }
}

if (! function_exists('str_replace_special_chars')) {
    function str_replace_special_chars($string)
    {
        $replace = [
            '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
            '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
            '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
            'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
            'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
            'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
            'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'L', 'Ľ' => 'L',
            'Ĺ' => 'L', 'Ļ' => 'L', 'Ŀ' => 'L', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
            'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
            'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
            'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
            'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
            '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
            'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
            'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
            'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
            'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
            'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
            'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
            'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
            'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
            'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
            'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
            'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
            '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
            'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
            'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
            'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
            'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
            'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
            'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
            'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
            'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
            'ю' => 'yu', 'я' => 'ya',
        ];

        $string = str_replace(array_keys($replace), $replace, $string);

        return $string;
    }

    if (! function_exists('limiter')) {
        /**
         * Check for Limit based on params
         *
         * @param  string  $referenceable_type
         * @param  mixed  $referenceable_id
         * @param  string  $key
         * @param  string  $model
         * @param  string  $model_key
         * @return bool
         */
        function limiter($referenceable_type = 'App\\Role', $referenceable_id = 1, $key = 'logbook_limiter', $model = 'App\Logbook', $model_key = 'owner_id')
        {
            $limit = optional(App\Config::where([
                ['referenceable_type', '=', $referenceable_type],
                ['referenceable_id', '=', $referenceable_id],
                ['key', '=', $key],
            ])->get()->first())->value ?: -1;

            return ($limit == -1)
                ? true
                : $model::where($model_key, auth()->user()->id)
                    ->get()
                    ->count() < $limit;
        }
    }
}
