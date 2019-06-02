<button 
    id="{{ $field }}"  
    type="{{ $type }}"
    name="{{ $field }}" 
    class="{{ $class }}" 
    @if(isset($onclick)) 
        onclick="{{ $onclick }}"
    @endif 
    >
    <i class="{{ $icon }} mr-2"></i>{{ $label }}
</button>