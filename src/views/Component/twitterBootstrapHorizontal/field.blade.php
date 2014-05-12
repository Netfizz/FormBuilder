<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}
    <div class="col-sm-10">
    {{ $component }}
    </div>

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

</div>