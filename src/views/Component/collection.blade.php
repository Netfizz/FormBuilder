<fieldset{{ $attributes }}>

    @if (isset($label))
    <legend>{{ $label }}</legend>
    @endif

    {{ $prepend }}

    {{ $component }}

    {{ $message }}

    <!-- subforms collection -->
    <ul class="collection-component">
        @foreach ($elements as $element)
            <li>
                {{ $element }}
            </li>
        @endforeach
    </ul>

    {{ $append }}

    <!-- Tab script -->
    <script>

    </script>

</fieldset>