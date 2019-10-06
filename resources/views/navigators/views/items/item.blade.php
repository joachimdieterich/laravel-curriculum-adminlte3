<div class="box box-objective pointer" 
     style="height: 300px !important; min-width: 200px !important; padding: 0; background: url('{{route('media.show', (isset($item->medium->id) ? $item->medium->id : ''))}}') center center;  background-size: cover;"
     onclick="{{ $onclick }}">
      
    <span class="p-1 pointer_hand" 
        accesskey="" style="position:absolute; top:0px; height: 30px; width:100%;">
        <form action="{{route('navigatorItems.destroy', ['navigatorItem' => $item->id])}}" 
              method="POST" 
              enctype="multipart/form-data"
              onclick="event.stopPropagation();">
            @csrf
            @method('DELETE')   
            <button type="submit" class="btn btn-danger btn-sm pull-right">
                <small><i class="fa fa-trash"></i></small>
            </button>
        </form>
        <button type="button" class="btn btn-primary btn-sm pull-right mr-1" onclick="alert('add');event.stopPropagation();">
            <small><i class="fa fa-edit"></i></small>
        </button>
    </span>     
    
    <span class="bg-white text-center p-1 overflow-auto " 
          style="position:absolute; bottom:0px; height: 150px; width:100%;">

        <h6 class="events-heading pt-1 ">
            {{ $item->title }}
        </h6>
        <p class=" text-muted small ">
            {{ strip_tags($item->description) }}   
            
        </p>
        
    </span>
</div>