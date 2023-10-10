<div id="{{ $field }}_form_group" class="form-group{{ $errors->has( $field ) ? ' has-danger' : '' }}">
    <label for="{{ $field }}">
        {{ trans('global.'.$model.'.fields.'.$field) }}
        @if(isset($required))
            *
        @endif
    </label>
    {{-- in order for the date-picker component to work, it needs a 'v-model' reference --}}
    <date-picker-wrapper
        :input-attr="{
            id: '{{ $field }}',
            name: '{{ $field }}',
            @if(isset($required))
                required: true
            @endif
        }"
        class="w-100 {{ $errors->has($field) ? ' is-invalid' : '' }}"
        type="datetime"
        value-type="YYYY-MM-DD HH:mm:ss"
        value="{{ $value }}"
        :popup-style="{ bottom: '100%', left: 0 }"
        :append-to-body="false"
        @if(isset($placeholder))
            :placeholder="'{{ trans($placeholder) }}'"
        @endif
        @if(isset($readonly))
            :editable="false"
        @endif
    ></date-picker-wrapper>
    @if ($errors->has( $field ))
        <span
            id="{{ $field }}-error"
            class="error text-danger"
            for="input-{{ $field }}">{{ $errors->first( $field ) }}
        </span>
    @endif
</div>