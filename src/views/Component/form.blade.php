<div style="border: 1px solid #ccc; padding: 20px; margin: 20px;">

    {{ $openFormTag or '' }}

    {{ $content or '' }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

    {{ $closeFormTag or '' }}
    FormBuilder::close()
</div>