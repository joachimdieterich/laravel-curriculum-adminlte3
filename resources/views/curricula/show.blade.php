@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    <title-component></title-component>
@endsection

@section('contributors')
    <div id="contributors"></div>
@endsection

@section('breadcrumb')
    @if (Auth::user()->id == env('GUEST_USER'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> Str::limit($curriculum->title, 10) ]
        ])}}"
        ></breadcrumbs>
    @else
        @if(isset($course))
            @can('achievement_access')
                <breadcrumbs
                    :entries="{{json_encode([
                ['active'=> true, 'title'=> trans('global.curriculum.title_singular'), 'url' => "/curricula/" . $course->curriculum_id ],
                ['active'=> true, 'title'=> Str::limit($course->title, 10) ]
            ])}}"
                ></breadcrumbs>
            @endcan
        @else
            <breadcrumbs
                :entries="{{json_encode([
                ['active'=> true, 'title'=> trans('global.curriculum.title_singular'), 'url' => "/curricula"],
                ['active'=> true, 'title'=> Str::limit($curriculum->title, 10) ]
            ])}}"
            ></breadcrumbs>
        @endif
    @endif
@endsection

@section('content')
    <Curriculum
        :curriculum="{{ $curriculum }}"
        :course="{{ $course ?? json_encode((object)[]) }}"
        :settings="{{ $settings }}"
        :websocket="{{ $is_websocket_active ? 'true' : 'false' }}"
    />
    <div id="content_top_placeholder"></div>
@endsection