<div class="row mx-3">
    <!--<div class="col-12" >-->  
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
                            @if ($item->referenceable->mime_type == 'url')
                                @include ('navigators.views.items.item', [ 'item' => $item, 'onclick' => "location.href='{$item->referenceable->relativePath()}';" ])
                            @else
                                @include ('navigators.views.items.item', [ 'item' => $item, 'onclick' => "location.href='/media/{$item->referenceable_id}';" ])
                            @endif 
                            @break
                    @endswitch
                @endif
            @endforeach
         @endif
    <!--</div>-->
</div>