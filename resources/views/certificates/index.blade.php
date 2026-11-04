@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.title') }}
@endsection
@section('content')
    @can('certificate_access')
        <div class="px-3">
            <a href="{{ route('certificate-designer.index') }}" class="btn btn-primary mb-3">
                Zertifikatseditor
            </a>
        </div>
    @endcan
    <Certificates/>
@endsection