<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(
 *      required={"id", "alpha2", "lang_de"},
 *      @OA\Xml(name="Country"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="alpha2", type="string"),
 *      @OA\Property( property="alpha3", type="string"),
 *      @OA\Property( property="langCS", type="string"),
 *      @OA\Property( property="lang_de", type="string"),
 *      @OA\Property( property="lang_en", type="string"),
 *      @OA\Property( property="langES", type="string"),
 *      @OA\Property( property="langFR", type="string"),
 *      @OA\Property( property="langIT", type="string"),
 *      @OA\Property( property="langNL", type="string"),
 *   ),
 */
class Country extends Model
{
    protected $primaryKey = 'alpha2';

    public $incrementing = false;

    public function states()
    {
        return $this->hasMany(State::class, 'country', 'alpha2');
//                    ->withDefault(function ()
//                    {
//                        return new State();
//                    });
    }
}
