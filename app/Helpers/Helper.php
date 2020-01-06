<?php

if (!function_exists('format_select_input')) {

    /**
     * helper function for selects 
     * if input is an array, it returns the first value of array
     * else it returns the input
     * 
     * returns integer
     */
    function format_select_input($input) {
        if (is_array($input)) {
            return Arr::first($input);
        } else {
            return $input;
        }
    }
}
if (!function_exists('getImmediateChildrenByTagName')) {
    /**
    * Traverse an elements children and collect those nodes that
    * have the tagname specified in $tagName. Non-recursive
    * Source: http://stackoverflow.com/questions/3049648/php-domelementgetelementsbytagname-anyway-to-get-just-the-immediate-matching 
    *
    * @param DOMElement $element
    * @param string $tagName
    * @return array
    */
   function getImmediateChildrenByTagName(DOMElement $element, $tagName) {
       $result = array();
       foreach ($element->childNodes as $child) {
           if ($child instanceof DOMElement && $child->tagName == $tagName) {
               $result[] = $child;
           }
       }
       return $result;
   }
}

if (!function_exists('relativeToAbsoutePaths')) {
    function relativeToAbsoutePaths($input) {
        return preg_replace_callback( 
                '/<img\s+[^>]*src="\/media\/(.*?)"(\s+[^>]*)[^>]*>/mi', 
                function($match) 
                { 
                    $media = App\Medium::find($match[1]);
                    return (( "<img src=\"{$media->absolutePath()}\"{$match[2]}>"));      
                }, 
                $input 
            ); 
    }
}

if (!function_exists('str_singular')) {

    /**
     * helper function str_singular() translator to Str::singular
     */
    function str_singular($param) {
            return Str::singular($param);
    }
}

if (!function_exists('starts_with')) {

    /**
     * helper function starts_with() translator to Str::startsWith()
     */
    function starts_with($param1, $param2) {
            return Str::startsWith($param1, $param2);
    }
}

if (!function_exists('ends_with')) {

    /**
     * helper function starts_with() translator to Str::endsWith()
     */
    function ends_with($param1, $param2) {
            return Str::endsWith($param1, $param2);
    }
}

if (!function_exists('camel_case')) {

    /**
     * helper function camel_case() translator to Str::camel()
     */
    function camel_case($param) {
            return Str::camel($param);
    }
}

if (!function_exists('str_limit')) {

    /**
     * helper function str_limit() translator to Str::limit()
     */
    function  str_limit($param1, $param2) {
            return Str::limit($param1, $param2);
    }
}

if (!function_exists('str_contains')) {

    /**
     * helper function str_contains() translator to Str::contains()
     */
    function  str_contains($param1, $param2) {
            return Str::contains($param1, $param2);
    }
}

if (!function_exists('is_dir_empty')) {
    function is_dir_empty($dir) {
        if (!is_readable($dir)) return NULL; 
        return (count(scandir($dir)) == 2);
    }
}

if (!function_exists('find_all_files')) {
   
    function find_all_files($dir) 
    { 
        $result = array();
        $root = scandir($dir); 
        foreach($root as $value) 
        { 
            if($value === '.' || $value === '..') {continue;} 
            if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;} 
            foreach(find_all_files("$dir/$value") as $value) 
            { 
                $result[]=$value; 
            } 
        } 
        return $result; 
    } 
}