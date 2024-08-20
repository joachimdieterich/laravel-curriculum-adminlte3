@extends('layouts.contentonly')

@section('content')
    <plan-achievements :terminal="{{ $terminal }}" :enabling="{{ $enabling }}" :user="{{ $user }}"></plan-achievements>
@endsection