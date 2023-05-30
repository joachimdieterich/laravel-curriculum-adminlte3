@if(isset($contentonly))
<footer class="main-footer py-1 no-print"
        style="background-color:#737c83; position: fixed;left: 0;bottom: 0;width: 100%;margin-left:0 !important;" >
@else
<footer class="main-footer py-1 no-print"
        style="background-color:#737c83">
@endif
    <div class="row">
        <div class="col-lg-2">
            @if (env('FOOTER_LOGO_HEIGHT'))
            <a href="#">
                <img style="height:{{ env('FOOTER_LOGO_HEIGHT') }};"
                     src="{{ env('FOOTER_LOGO_URL') }}" alt="{{ env('FOOTER_LOGO_ALT') }}">
            </a>
            @endif
        </div>

        <div class="navbar-expand col-lg-8">
            <ul class="navbar-nav d-flex p-2">
                @php ($footer_iterator = 1)
                @while ( env('FOOTER_TITLE_'.$footer_iterator) )
                <li class="nav-item flex-fill ">
                    <a class=" nav-item d-none d-sm-inline-block text-white small text-decoration-none"
                        href="{{ env('FOOTER_URL_'.$footer_iterator) }}">
                         {{ env('FOOTER_TITLE_'.$footer_iterator) }}
                     </a>
                </li>
                @php ($footer_iterator++)
                @endwhile
            </ul>
        </div>

<!--        <div class="col-lg-2 pt-2 d-none d-sm-inline-block ">
            <a class="text-white-50 text-decoration-none "
               href="http://curriculumonline.de">
                <small>
                    Version 1.0.0
                </small>
            </a>
        </div>-->
    </div>
</footer>
