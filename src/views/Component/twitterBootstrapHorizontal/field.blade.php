<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}
    @if ($component)
    <div class="col-sm-10">
        {{ $component }}
    </div>
    @endif

    @if (count($elements) > 0)
    <div class="col-sm-10  component-childs">
        @foreach ($elements as $element)
            {{ $element }}
        @endforeach
    </div>
    @endif


</div>