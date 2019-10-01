<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Period;

class PeriodsApiController extends Controller {

    public function index() {
        $periods = Period::all();

        return $periods;
    }

    public function show(Period $period) {

        return $period;
    }
    
    public function store() {
        
        return Period::create($this->filteredRequest());
    }
    
    public function update(Period $period) {
        if ($period->update($this->filteredRequest())) 
        { 
            return $period->fresh();
        }
    }
    
    public function destroy(Period $period) {
        if ($period->delete()) 
        {
            return ['message' => 'Successful deleted'];
        }
    }
    
    protected function filteredRequest() {
        return array_filter(request()->all()); //filter to ignore fields with null values
    }

}
