<div{{ $attributes->wrapper }}>
@if ($prepend)
    <div{{ $attributes->prepend }}>
        {{ $prepend }}
    </div>
@endif

    {{ $element }}

@if ($append)
    <div{{ $attributes->append }}>
        {{ $append }}
    </div>
@endif
</div>