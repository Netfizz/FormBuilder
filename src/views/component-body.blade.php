@if (isset($wrapper))
<div{{ $wrapper->attributes() }}>
@endif
@if (isset($prepend))
    <div{{ $prepend->attributes() }}>
        {{ $prepend }}
    </div>
@endif
    BODY
    {{ $field or ''}}
    {{ $field2 or ''}}
    {{ $field3 or ''}}
    {{{ $field4 or  '' }}}
    BODY
@if (isset($append))
    <div{{ $append->attributes() }}>
        {{ $append }}
    </div>
@endif

@if (isset($wrapper))
</div>
@endif
<hr />