@if (isset($wrapper))
<div{{ $wrapper->attributes() }}>
@endif

@if (isset($prepend))
    <div{{ $prepend->attributes() }}>
        {{ $prepend }}
    </div>
@endif

    {{ $field or '' }}

@if (isset($append))
    <div{{ $append->attributes() }}>
        {{ $append }}
    </div>
@endif

@if (isset($wrapper))
</div>
@endif
<hr />