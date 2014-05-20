<div{{ $attributes }}>

    {{ $message }}

    {{ $prepend }}

    {{ $label }}
    {{ $component }}

    @if (count($elements) > 0)
    <div class="component-childs">
        @foreach ($elements as $element)
        {{ $element }}
        @endforeach
    </div>
    @endif

    {{ $append }}

</div>