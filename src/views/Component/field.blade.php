<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}
    {{ $component }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

</div>