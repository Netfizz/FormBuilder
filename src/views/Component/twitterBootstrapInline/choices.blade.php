<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}

    <div class="component-choices">
        {{ $component }}
    </div>

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach


</div>