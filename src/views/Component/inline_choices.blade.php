<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}

    {{ $component }}

    <div class="component-choices">
        @foreach ($elements as $element)
            {{ $element }}
        @endforeach
    </div>

</div>