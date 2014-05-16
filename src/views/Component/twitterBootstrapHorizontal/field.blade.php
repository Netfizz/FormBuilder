<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}
    <div class="col-sm-10">
        {{ $component }}
    </div>

    @if (count($elements) > 0)
    <div class="col-sm-10">
        @foreach ($elements as $element)
            {{ $element }}
        @endforeach
    </div>
    @endif


</div>