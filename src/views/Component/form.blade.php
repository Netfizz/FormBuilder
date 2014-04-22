<div style="border: 1px solid #ccc; padding: 20px; margin: 20px;">
    {{ $formOpenTag or '' }}

    {{ $content or '' }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

    {{ $formCloseTag or '' }}
</div>