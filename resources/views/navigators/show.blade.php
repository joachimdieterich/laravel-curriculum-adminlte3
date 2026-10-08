@extends('layouts.master')
@section('content')
    <Navigator
        :navigator="{{ $navigator }}"
        :view="{{ $view ?? null }}"
    />
@endsection