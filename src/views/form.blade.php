{{ $formOpenTag }}

    {{ $component }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

{{ $formCloseTag}}
