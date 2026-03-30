@extends('layouts.master')

@section('content')
    <plan-achievements :terminal="{{ $terminal }}" :enabling="{{ $enabling }}" :users="{{ $users }}"/>
@endsection