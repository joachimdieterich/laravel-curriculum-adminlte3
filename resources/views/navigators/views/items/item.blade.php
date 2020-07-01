<div
    id="navigator-item-{{ $item->id }}"
    class="box box-objective pointer {{ $item->css_class }} my-1" 
    style="height: 300px !important; min-width: 200px !important; padding: 0;background: url('{{isset($item->medium->id) ? route('media.show', ($item->medium->id)) : Avatar::create('test')->toGravatar(['d' => 'identicon', 'r' => 'pg', 's' => 100])}}') center center;  background-size: cover;"
    onclick="{{ $onclick }}">
    
    <div class="symbol" style="position: absolute;
        padding: 6px;
        z-index: 1;
        width: 30px;
        height: 40px;
        background-color: #0583C9;
        top: 0px;
        font-size: 1.2em;
        left: 10px;">
        @switch($item->referenceable_type)
            @case('App\NavigatorView')
                <i class="fa fa-map-signs text-white pt-2"></i>
                @break
             @case('App\Medium')
                <i class="fa fa-photo-video text-white pt-2"></i>
                @break
             @default
             <!--@case('App\Curriculum')-->
                <i class="fa fa-th text-white  pt-2"></i>
                @break   
           
        @endswitch
    </div>
        @can('navigator_create')
        <span class="p-1 pointer_hand" 
           accesskey="" style="position:absolute; top:0px; height: 30px; width:100%;">
           <form action="{{route('navigatorItems.destroy', ['navigatorItem' => $item->id])}}" 
                 method="POST" 
                 enctype="multipart/form-data"
                 onclick="event.stopPropagation();">
               @csrf
               @method('DELETE')   
               <button 
                   id="delete-navigator-item-{{ $item->id }}"
                   type="submit" class="btn btn-danger btn-sm pull-right">
                   <small><i class="fa fa-trash"></i></small>
               </button>
           </form>
    <!--       <a href="{{route('navigatorItems.edit', ['navigatorItem' => $item->id, 'navigator_id'])}}"
                   class="btn btn-primary btn-sm pull-right mr-1">
               <small><i class="fa fa-edit"></i></small>
           </a>-->
       </span>   
        @endcan
    

    <span class="bg-white text-center p-1 overflow-auto " 
         style="position:absolute; bottom:0px; height: 150px; width:100%;">

       <h6 class="events-heading pt-1 hyphens">
           {{ $item->title }}
       </h6>
       <p class=" text-muted small">
           {{ strip_tags($item->description) }}   
       </p>

   </span>
</div>
