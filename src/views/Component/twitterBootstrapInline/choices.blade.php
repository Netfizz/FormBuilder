<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}

    <div>
        {{ $component }}
    </div>

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach


</div>