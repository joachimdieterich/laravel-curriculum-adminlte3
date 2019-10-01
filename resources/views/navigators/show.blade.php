@extends('layouts.contentonly')
@section('content')
   
<div class="header">
    <div class="row">
        <div class="col-12">
            <h5 class="p-3">
                <i class="fa fa-university mr-1"></i> {{$navigators->title }}
            </h5>
        </div>
    </div>
</div>
<!-- /.header -->

<div class="body">
    <div class="row p-4">
        <div class="col-centered" >  
            @foreach($views->items as $item)
                @include ('partials.cards.card', [
                    'item' => $item,
                ])
            @endforeach
        </div>
    </div>
</div>
<!-- /.body -->

<div class="footer">

</div>
  
@endsection
