<?php

namespace App\Interfaces;


use Illuminate\Http\Request;

interface MeetingInterface
{
    public function index();

    public function list ();

    public function create();

    public function store (Request $request);

    public function show (Meeting $meeting);

    public function edit (Meeting $meeting);

    public function update (Request $request, Meeting $meeting);

    public function destroy (Meeting $meeting);
}
