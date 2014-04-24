<div{{ $attributes }} style="border: 1px solid #ccc; padding: 20px; margin: 20px;">

    {{ $message }}
    {{ $label }}
    {{ $content }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

</div>