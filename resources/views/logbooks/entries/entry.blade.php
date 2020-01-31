<!-- timeline item -->
<div>
    <i class="{{ isset($css_icon) ? $css_icon : 'fas fa-envelope' }} bg-{{ isset($color) ? $color : 'green' }}"></i>
    <div class="timeline-item">
        <span class="time"><i class="fas fa-clock"></i> {{ $entry->begin }}</span>
        <h3 class="timeline-header"><strong>{{ $entry->title }}</strong></h3>

        <div class="timeline-body">
            {!! $entry->description !!}
        </div>
        <hr>
        objectives
        <hr>
        content
        <hr>
        task
        <hr>
        user status
        <div class="timeline-footer">
            <a class="btn btn-primary btn-sm">More</a>
        </div>
    </div>
</div>
<!-- END timeline item -->