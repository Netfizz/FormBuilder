{{ $formOpenTag }}

    {{ $content }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

{{ $formCloseTag}}
