<div{{ $attributes }}>

    {{ $message }}
    {{ $label }}
    {{ $content }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

</div>