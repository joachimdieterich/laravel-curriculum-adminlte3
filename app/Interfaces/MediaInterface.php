<?php

namespace App\Interfaces;

use App\Medium;
use Illuminate\Http\Request;

interface MediaInterface
{
    public function index(Request $request);

    public function list();

    public function create();

    public function store(Request $request);

    public function show(Medium $medium);

    public function thumb(Medium $medium, Integer $size);

    public function edit(Medium $medium);

    public function update(Request $request, Medium $medium);

    public function destroy(Medium $medium, mixed $subscribable_type, mixed $subscribable_id);

    public function checkIfUserHasSubscription($subscription);
}
