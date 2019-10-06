<div class="row px-3">
        <div class="col-12" >  
            @if(isset($views->items))
                @foreach($views->items as $item)
                    @if ($item->position == $position)
                        @switch($item->referenceable_type)
                            @case('App\NavigatorView')
                                @include ('navigators.views.items.item', [ 'item' => $item, 'onclick' => "location.href='/navigators/{$navigators->id}/{$item->referenceable_id}';" ])
                                @break

                            @case('App\Curriculum')
                                @include ('navigators.views.items.item', [ 'item' => $item, 'onclick' => "location.href='/curricula/{$item->referenceable_id}';" ])
                                @break  

                            @case('App\Content')
                                @include ('navigators.views.items.content', [ 'item' => $item, 'onclick' => "" ])
                                @break  
                                
                             @case('App\Medium')
                                @include ('navigators.views.items.item', [ 'item' => $item, 'onclick' => "location.href='/media/{$item->referenceable_id}';" ])
                                @break
                        @endswitch
                    @endif
                @endforeach
             @endif
        </div>
    </div>