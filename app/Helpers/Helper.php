<?php

if (!function_exists('format_select_input')) {
    /**
     * helper function for selects 
     * if input is an array, it returns the first value of array
     * else it returns the input
     * 
     * returns integer
     */ 

    function format_select_input($input)
    {
        if (is_array($input))
        {
            return array_first($input);
        }
        else 
        {
            return $input;
        }
    }
}