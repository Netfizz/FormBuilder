<div{{ $wrapper->attributes() }}>

@if (isset($prepend))
    <div{{ $prepend->attributes() }}>
        {{ $prepend }}
    </div>
@endif

    {{ $field or ''}}

@if (isset($append))
    <div{{ $append->attributes() }}>
        {{ $append }}
    </div>
@endif

</div>