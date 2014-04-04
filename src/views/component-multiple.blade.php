<div{{ $wrapper->attributes() }}>

@if (isset($prepend))
    <div{{ $prepend->attributes() }}>
        {{ $prepend }}
    </div>
@endif
    MULTIPLI
    {{ $field or ''}}
    BODY
@if (isset($append))
    <div{{ $append->attributes() }}>
        {{ $append }}
    </div>
@endif

</div>