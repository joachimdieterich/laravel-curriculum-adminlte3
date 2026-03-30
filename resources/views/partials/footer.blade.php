<footer
    class="py-1 no-print"
    style="background-color: #737c83"
>
    <div class="d-flex flex-column flex-lg-row">
        <div class="col-lg-2 text-center text-sm-left">
            @if (config('app.footer.logo.height'))
            <a href="#">
                <img
                    src="{{ config('app.footer.logo.src') }}"
                    alt="{{ config('app.footer.logo.alt') }}"
                    style="height:{{ config('app.footer.logo.height') }};"
                />
            </a>
            @endif
        </div>

        <div class="navbar-expand col-lg-8">
            <ul class="navbar-nav d-flex flex-column flex-sm-row text-center p-2">
                @php ($footer_iterator = 1)
                @while ( config('app.footer.menu.'.$footer_iterator.'.title') )
                <li class="nav-item flex-fill py-2">
                    <a
                        class="nav-item text-white small"
                        href="{{ config('app.footer.menu.'.$footer_iterator.'.url') }}"
                    >
                        {{ config('app.footer.menu.'.$footer_iterator.'.title') }}
                    </a>
                </li>
                @php ($footer_iterator++)
                @endwhile
            </ul>
        </div>
    </div>
</footer>