@extends('layouts.master')
@section('title')
    {{ trans('global.home') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active"><a href="/">{{ trans('global.home') }}</a></li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-8">
            <div class="col-lg-12 p-0">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">{{ trans('global.dashboard.actual') }}</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Info</h6>

                        <p class="card-text">...</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 p-0">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="m-0">{{ trans('global.dashboard.statistic') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a href="#">
                                    <strong>{{ trans('global.dashboard.statistic_archievements') }}</strong>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#">
                                    {{ trans('global.dashboard.today') }} 
                                    <span class="pull-right text-green">
                                        {{ count(auth()->user()->achievements_today()->where('status', '>=', 10)->where('status', '<', 30)) }}
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#">
                                    {{ trans('global.dashboard.statistic_archievements_total') }} 
                                    <span class="pull-right text-green">
                                        {{ count(auth()->user()->achievements->where('status', '>=', 10)->where('status', '<', 30)) }}
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <ul class="pt-4 nav nav-pills flex-column">
                            <li class="nav-item">
                                <a href="#">
                                    <strong>{{ trans('global.dashboard.online') }}</strong>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#">
                                    {{ trans('global.dashboard.now_online') }}
                                    <span class="pull-right">{{ now_online() }}
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#">
                                    {{ trans('global.dashboard.today') }}
                                    <span class="pull-right">{{ today_online() }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <div class="col-lg-12 p-0">
                    <div class="card ">
                      <div class="card-header">
                        <h5 class="m-0">{{ trans('global.organization.title') }}</h5>
                      </div>
                        <div class="card-body">
                            
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                @foreach(auth()->user()->organizations as $organization)
                                <li class="item">
                                    <div >
                                        <a href="/organizations/{{$organization->id}}" 
                                           class="product-title">
                                            {{$organization->title}}
                                        </a>
                                        <span class="product-description">
                                            {!! $organization->description !!}
                                        </span>
                                    </div>
                                </li>
                                <!-- /.item -->
                                @endforeach
                            </ul>

                        </div>
                    </div>
                
                <div class="card ">
                      <div class="card-header">
                        <h5 class="m-0">{{ trans('global.group.title') }}</h5>
                      </div>
                      <div class="card-body">
                        <ul class="products-list product-list-in-card pl-2 pr-2">
                                @foreach(auth()->user()->groups as $groups)
                                <li class="item">
                                    <div >
                                        <a href="/groups/{{$groups->id}}" class="product-title">{{$groups->title}}
                                        <span class="product-description">
                                            {{$groups->grade->title}}
                                        </span>
                                        </a>
                                    </div>
                                </li>
                                <!-- /.item -->
                                @endforeach
                            </ul>
                      </div>
                    </div>
                </div>
        </div>
        
    </div>
</div>

@endsection
@section('scripts')
@parent

@endsection