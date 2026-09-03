@extends('layouts.master')
@section('content')
    <navigator
        :navigator="{{ $navigator }}"
        :view="{{ $view ?? null }}"
    ></navigator>
@endsection