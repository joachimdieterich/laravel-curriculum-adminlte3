<div class="box box-objective pointer" 
     style="height: 300px !important; min-width: 200px !important; padding: 0; background: url('{{route('media.show',  (isset($item->medium->id) ? $item->medium->id : ''))}}') center center;  background-size: cover;">
      
        <span class="no-padding pointer_hand" 
            accesskey=""style="position:absolute; bottom:0px; height: 275px;width:100%;"
            onclick="{{ $onclick }}">
        </span>     
    
    <span class="bg-white text-center p-1 overflow-auto " 
          style="position:absolute; bottom:0px; height: 150px; width:100%;">

        <h6 class="events-heading pt-1">
            <a onclick="location.href='curricula';">
                {{ $item->title }}
            </a>
        </h6>
        <p style="height: 110px;" 
           class=" text-muted small ">
            {{ $item->description }}
        </p>
        
    </span>
</div>