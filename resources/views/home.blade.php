@extends('layouts.master')
@section('title')
    {{ trans('global.home') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active"><a href="/">Home</a></li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-8">
            <div class="col-lg-12 p-0">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0">Aktuelles</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">Info</h6>

                        <p class="card-text">...</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 p-0">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="m-0">Statistik</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item"><a href="#"><strong>Erreichte Ziele</strong></a></li>
                            <li class="nav-item"><a href="#">Gesamt <span class="pull-right text-green">123</span></a></li>
                            <li class="nav-item"><a href="#">davon Heute <span class="pull-right text-green">123</span></a></li>
                        </ul>
                        <ul class="pt-4 nav nav-pills flex-column">
                            <li class="nav-item"><a href="#"><strong>online</strong></a></li>
                            <li class="nav-item"><a href="#">jetzt online <span class="pull-right">123</span></a></li>
                            <li class="nav-item"><a href="#">heute <span class="pull-right"> 123</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <div class="col-lg-12 p-0">
                    <div class="card ">
                      <div class="card-header">
                        <h5 class="m-0">Meine Institutionen</h5>
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
                                            {{ $organization->description }}
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
                        <h5 class="m-0">Meine Lerngruppen</h5>
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