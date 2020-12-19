<div id="{{ $id }}"
                    class="box box-objective pointer card"
                    style="height: 300px !important; min-width: 200px !important; padding: 0; background-color: {{ $color }}; background-size: cover;">
                    <span  style="position:absolute" class="w-100 h-100 center-icon p-3">
                        <i class="{{ $icon }}"></i>
                        <h6 class="events-heading pt-3 hyphens">
                          {{ $title }}
                        </h6>
                    </span>
                    <span class="bg-white p-1 hide-scrollbars card-body"
                         style="position:absolute; bottom:0px; height: 150px; width:100%;">
                        {!! $content !!}

<!--                       <p class="text-primary small p-2 mb-0"
                           style="position:absolute; bottom:0px; right:0px">
                         mehr...
                       </p>-->
                   </span>
                </div>
