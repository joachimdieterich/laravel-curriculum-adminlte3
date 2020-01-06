<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(  
 *      required={"id", "lang_de", "country", "code"},
 *      @OA\Xml(name="State"),
 *      
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="lang_de", type="string"),
 *      @OA\Property( property="country", type="string"),
 *      @OA\Property( property="code", type="string"),
 *   ),
 */
class State extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    
    protected $attributes = [
        'lang_:de' => '-',
     ];
    
    public function country()
    {
        return $this->hasOne('App\Country', 'alpha2', 'country');
    }
}
