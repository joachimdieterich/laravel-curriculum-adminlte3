<div class="box box-objective pointer" 
     style="height: 300px !important; min-width: 200px !important; padding: 0; background: url('{{route('media.show', (isset($item->referenceable->medium_id) ? $item->referenceable->medium_id : ''))}}') center center;  background-size: cover;"
     onclick="{{ $onclick }}">
      
        <span class="no-padding pointer_hand" 
            accesskey=""style="position:absolute; bottom:0px; height: 275px;width:100%;">
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