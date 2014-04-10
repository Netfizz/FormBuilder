<fieldset>
    @if (isset($label))
    <legend>{{ $label }}</legend>
    @endif

    {{ $content or '' }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

</fieldset>