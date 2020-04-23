<div class="col-12 col-sm-6 col-md-4 mb-3">
    <a  class="link-muted text-decoration-none ">
        <div class="info-box mb-0 rounded-0 rounded-top">
            <span class="{{ $infoBoxClass }}" 
                  data-toggle="collapse" 
                  href="#collapse{{ $infoBoxId }}" 
                  aria-expanded="false" 
                  aria-controls="collapse{{ $infoBoxId }}">
                <i class="{{ $infoBoxIcon }}"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ $infoText }}</span>
                <span class="info-box-number">{!! $infoBoxNumber !!}</span>
            </div>
        </div>
        <div id="collapse{{ $infoBoxId }}" 
            class="card pt-0 border-0 rounded-0 collapse ">
            @if(isset($include))
                <div class="card-body">@include("partials.{$include}")</div>
            @endif
        </div>
        
    </a>
</div>