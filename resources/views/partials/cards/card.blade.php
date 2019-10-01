<div class="box box-objective pointer" 
     style="height: 300px !important; min-width: 200px !important; padding: 0; background: url('https://curriculum.bildung-rp.de/v0.9.3.17/curriculum/public/assets/images/backgrounds/CC0-Pixabay-alphabet-1219546_1920.jpg') center center;  background-size: cover;">
     
          
        @switch($item->referenceable_type)
            @case('App\NavigatorView')
                <span class="no-padding pointer_hand" 
                    accesskey=""style="position:absolute; bottom:0px; height: 275px;width:100%;"
                    onclick="location.href='/navigators/{{$navigators->id}}/{{ $item->referenceable_id }}';">
                </span>
                @break

            @case('App\Curriculum')
                <span class="no-padding pointer_hand" 
                    accesskey=""style="position:absolute; bottom:0px; height: 275px;width:100%;"
                    onclick="location.href='/curricula/{{ $item->referenceable_id }}';">
                </span>
                @break

            @default
                <span>Something went wrong, please try again</span>
        @endswitch
          
          
          
          

    
    <span class="bg-white text-center p-1" 
          style="position:absolute; bottom:0px; height: 150px; width:100%;">

        <h6 class="events-heading pt-1 text-cut">
            <a onclick="location.href='curricula';">
                {{ $item->title }}
            </a>
        </h6>
        <p style="height: 110px;" 
           class=" text-muted small overflow-auto ">
            {{ $item->description }}
        </p>
        
    </span>
</div>