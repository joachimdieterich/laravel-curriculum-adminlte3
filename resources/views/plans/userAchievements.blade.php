@extends('layouts.contentonly')

@section('content')
    <plan-achievements :terminal="{{ $terminal }}" :enabling="{{ $enabling }}" :users="{{ $users }}"></plan-achievements>
@endsection