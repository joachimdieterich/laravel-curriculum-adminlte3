<?php

namespace App\Domains\Exams\Jobs\IleaPlusJobs;

class decodeTanJob
{
    public static function decode(string $tan)
    {
        $search = array(
            "9" => "10", "A" => "11", "B" => "12", "C" => "13", "D" => "14", "E" => "15", "F" => "16",
            "0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => "5", "5" => "6", "6" => "7", "7" => "8", "8" => "9");

        $chars = str_split($tan);
        $pass = '';
        foreach ($chars as $char) {
            $s=$search[$char];
            $pass != "" && $pass .= ",";
            $pass .= $s;
        }
        return $pass;
    }
}
