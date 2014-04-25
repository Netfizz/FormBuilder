<div{{ $attributes }} style="border: 1px solid #ccc; padding: 20px; margin: 20px;">

    {{ $content }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

</div>