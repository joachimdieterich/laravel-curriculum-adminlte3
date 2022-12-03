
<ul class="todo-list pb-1"
    data-widget="todo-list"
    style="display: flex;flex-direction: column;">
    @foreach ($exams as $exam)
    <li class="bg-light mb-2" style="display: flex;column-gap: 5px;align-items: center;">
        <!-- drag handle -->
        <!--        <span class="handle">
                    <i class="fas fa-ellipsis-v"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </span>-->
        <!-- checkbox -->
        <div class="icheck-primary d-inline ml-2" style="width: 10%;">
            <input
                @if(isset($exam->pivot->exam_completed_at))
                    checked
                @endif
                disabled={{true}}
                type="checkbox"
                value=""
                name="todo1"
                id="todoCheck1"
                >

            <label for="todoCheck1"></label>

        </div>
        <!-- todo text -->
        <span style="width: 15%;" class="text">
            @if(!isset($exam->pivot->exam_completed_at))
                <a href="{{ $exam->login_url }}" target="_blank">{{ $exam->test_name }}</a>
            @else
                {{ $exam->test_name }}
            @endif
        </span>
        @if(isset($exam->pivot->exam_completed_at))
            <small class="badge badge-secondary pull-right"><i class="fa fa-calendar-check"></i> {{ $exam->pivot->exam_completed_at }}</small>
        @endif
    </li>
    @endforeach
</ul>

