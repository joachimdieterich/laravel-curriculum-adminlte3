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
            return array_first($input);
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