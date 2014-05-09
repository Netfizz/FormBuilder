<fieldset{{ $attributes }}>

    @if (isset($label))
    <legend>{{ $label }}</legend>
    @endif

    {{ $component }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

</fieldset>