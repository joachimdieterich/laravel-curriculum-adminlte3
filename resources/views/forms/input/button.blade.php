<button 
    id="{{ $field }}"  
    type="{{ $type }}"
    name="{{ $field }}" 
    class="{{ $class }}" 
    @if(isset($onclick)) 
        onclick="{{ $onclick }}">
    @endif 
    >
    <span class="{{ $icon }}"></span> "{{ $label }}"
</button>