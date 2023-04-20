<div class="mb-3">
    <a class="link-muted text-decoration-none ">
        <div class="info-box mb-0 rounded-0 rounded-top">
            <span class="{{ $infoBoxClass }} pointer"
                  onclick="window.location.href='{{ $infoBoxRoute }}'"
                  >
                <i class="{{ $infoBoxIcon }}"></i>
            </span>
            <div class="info-box-content pointer"
                  data-toggle="collapse"
                  href="#collapse{{ $infoBoxId }}"
                  aria-expanded="true"
                  aria-controls="collapse{{ $infoBoxId }}">
                <span class="info-box-text">{{ $infoText }}</span>
                <span class="info-box-number ">
                     @if(isset($infoBoxNumber))
                         {!! $infoBoxNumber !!}
                    @endif
                </span>
            </div>
        </div>
        <div id="collapse{{ $infoBoxId }}"
            class="card pt-0 border-0 rounded-0 collapse show"
             style="overflow-y: auto; max-height:240px;">
            @if(isset($include))
                <div class="card-body p-1">@include("partials.{$include}")</div>
            @endif
        </div>

    </a>
</div>
