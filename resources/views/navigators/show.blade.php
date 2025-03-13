@extends((Auth::user()->id == env('GUEST_USER')) || $is_shared ? 'layouts.contentonly' : 'layouts.master')

@section('breadcrumb')
    @if (Auth::user()->id == env('GUEST_USER'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> Str::limit($kanban->title, 10) ]
        ])}}"
        ></breadcrumbs>
    @else
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> false, 'title'=> trans('global.navigator.title'), 'url' => '/navigators' ],
            ['active'=> true, 'title'=> $navigator->title ]
        ])}}"
    ></breadcrumbs>
    @endif
@endsection
@section('content')
    <navigator
        :navigator="{{ $navigator }}"
        :view="{{ $view ?? null }}">
    </navigator>
@endsection
