<div{{ $attributes }}>

    {{ $message }}

    @if ($prepend)
    <div class="col-sm-offset-2 col-sm-10">
        {{ $prepend }}
    </div>
    @endif


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

    @if ($append)
    <div class="col-sm-offset-2 col-sm-10">
        {{ $append }}
    </div>
    @endif


</div>