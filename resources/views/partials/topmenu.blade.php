
<!-- Brand Logo/menu -->
@if(  env('BRAND_MENU_TITLE_1') )
<div id="topnav" class="bg-lime">
    <button
        type="button"
        data-toggle="dropdown"
        class="btn dropdown-menu-lime p-0 m-0" >
        <div id="mainpage-0"
             class="logo">
            @if(  Request::is('videoconferences') || Request::is('videoconferences/*'))
                <i class="fa fa-chalkboard-teacher text-white p-2"
                style="font-size: 24px !important"></i>
                {{ trans('global.videoconference.title') }}
                <i class="fa fa-chevron-down"></i>
            @elseif(  Request::is('maps') || Request::is('maps/*'))
                <i class="fa fa-map-location-dot text-white p-2"
                   style="font-size: 24px !important"></i>
                {{ trans('global.map.header_title') }}
                <i class="fa fa-chevron-down"></i>
            @else
                <svg  class="p-1" version="1.0" xmlns="http://www.w3.org/2000/svg"
                      height="32.000000pt" viewBox="0 0 400.000000 460.000000"
                      preserveAspectRatio="xMidYMid meet">
                        <g transform="translate(0.000000,460.000000) scale(0.100000,-0.100000)"
                           fill="#fff" stroke="none">
                            <path d="M2231 4531 c-8 -5 -48 -74 -90 -153 l-76 -143 -60 -13 c-153 -32
                              -325 -76 -380 -96 l-60 -21 -135 87 c-156 102 -173 105 -259 57 -201 -114
                              -482 -343 -498 -407 -3 -14 11 -78 36 -161 23 -75 43 -146 43 -157 1 -11 -28
                              -65 -64 -119 -36 -55 -94 -152 -128 -215 -34 -63 -65 -119 -70 -123 -4 -5 -70
                              -22 -146 -38 -177 -37 -189 -42 -207 -87 -23 -59 -55 -207 -71 -332 -19 -145
                              -30 -352 -19 -366 4 -6 71 -43 148 -83 77 -40 145 -78 151 -84 6 -7 22 -73 37
                              -147 14 -74 41 -182 60 -240 18 -59 30 -110 25 -115 -5 -6 -47 -68 -93 -140
                              -61 -93 -85 -139 -85 -161 0 -65 203 -365 350 -516 69 -72 78 -78 115 -78 22
                              0 90 15 150 34 177 54 158 55 262 -13 50 -34 140 -87 200 -120 157 -86 149
                              -76 178 -220 40 -196 40 -197 83 -214 52 -21 250 -64 351 -76 119 -15 354 -24
                              367 -14 7 4 46 71 88 148 42 77 83 145 92 151 8 6 38 15 67 18 66 9 228 49
                              334 83 l82 27 138 -92 c76 -51 149 -92 162 -92 77 0 530 330 576 421 23 43 18
                              91 -21 221 -19 66 -36 127 -36 136 -1 9 24 55 55 101 55 82 69 126 49 157 -9
                              15 -406 254 -421 254 -6 0 -50 23 -98 50 -104 60 -134 63 -171 18 -57 -67
                              -256 -255 -315 -296 -409 -287 -958 -268 -1349 47 -267 216 -405 461 -439 786
                              -37 344 94 685 360 938 346 329 831 410 1270 210 71 -32 123 -41 147 -25 7 4
                              59 84 116 177 247 402 264 435 240 471 -4 6 -55 37 -114 68 -59 32 -109 65
                              -112 74 -3 9 -19 78 -34 152 -16 75 -34 146 -41 157 -20 39 -182 79 -436 108
                              -145 17 -283 19 -304 6z"/>
                        </g>
                    </svg>
                    <span class="brand-txt pl-3">
                     {{ env('APP_NAME') }}  <i class="fa fa-chevron-down"></i>
                </span>
            @endif
        </div>
    </button>
    <div class="dropdown-menu bg-lime dropdown-menu-lime elevation-2">
    @php ($brand_iterator = 1)
    @while ( env('BRAND_MENU_TITLE_'.$brand_iterator) )
        <a href="{{ env('BRAND_MENU_HREF_'.$brand_iterator) }}" class="dropdown-item">
            <i class="brand-dropdown_icon {{ env('BRAND_MENU_ICON_'.$brand_iterator) }} fa-fw text-white"></i>
            <span  class="font-weight-light pl-1">{{ env('BRAND_MENU_TITLE_'.$brand_iterator) }}</span>
        </a>
    @php ($brand_iterator++)
    @endwhile

    </div>
</div>
@else
    <a href="{{ route("home") }}" class="brand-link p-1">
        <svg class="ml-2 pl-1" version="1.0" xmlns="http://www.w3.org/2000/svg"
             width="29.000000pt" viewBox="0 0 400.000000 460.000000"
             preserveAspectRatio="xMidYMid meet">
            <g transform="translate(0.000000,460.000000) scale(0.100000,-0.100000)"
               fill="#fff" stroke="none">
                <path d="M2231 4531 c-8 -5 -48 -74 -90 -153 l-76 -143 -60 -13 c-153 -32
                      -325 -76 -380 -96 l-60 -21 -135 87 c-156 102 -173 105 -259 57 -201 -114
                      -482 -343 -498 -407 -3 -14 11 -78 36 -161 23 -75 43 -146 43 -157 1 -11 -28
                      -65 -64 -119 -36 -55 -94 -152 -128 -215 -34 -63 -65 -119 -70 -123 -4 -5 -70
                      -22 -146 -38 -177 -37 -189 -42 -207 -87 -23 -59 -55 -207 -71 -332 -19 -145
                      -30 -352 -19 -366 4 -6 71 -43 148 -83 77 -40 145 -78 151 -84 6 -7 22 -73 37
                      -147 14 -74 41 -182 60 -240 18 -59 30 -110 25 -115 -5 -6 -47 -68 -93 -140
                      -61 -93 -85 -139 -85 -161 0 -65 203 -365 350 -516 69 -72 78 -78 115 -78 22
                      0 90 15 150 34 177 54 158 55 262 -13 50 -34 140 -87 200 -120 157 -86 149
                      -76 178 -220 40 -196 40 -197 83 -214 52 -21 250 -64 351 -76 119 -15 354 -24
                      367 -14 7 4 46 71 88 148 42 77 83 145 92 151 8 6 38 15 67 18 66 9 228 49
                      334 83 l82 27 138 -92 c76 -51 149 -92 162 -92 77 0 530 330 576 421 23 43 18
                      91 -21 221 -19 66 -36 127 -36 136 -1 9 24 55 55 101 55 82 69 126 49 157 -9
                      15 -406 254 -421 254 -6 0 -50 23 -98 50 -104 60 -134 63 -171 18 -57 -67
                      -256 -255 -315 -296 -409 -287 -958 -268 -1349 47 -267 216 -405 461 -439 786
                      -37 344 94 685 360 938 346 329 831 410 1270 210 71 -32 123 -41 147 -25 7 4
                      59 84 116 177 247 402 264 435 240 471 -4 6 -55 37 -114 68 -59 32 -109 65
                      -112 74 -3 9 -19 78 -34 152 -16 75 -34 146 -41 157 -20 39 -182 79 -436 108
                      -145 17 -283 19 -304 6z"/>
            </g>
        </svg>

        <span class="pl-1 brand-text d-inline-block">
            @if(  Request::is('videoconferences') )
                {{ trans('global.videoconference.title') }}
            @else
                {{ env('APP_NAME') }}
            @endif
        </span>
   </a>
@endif
