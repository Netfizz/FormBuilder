<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}

    {{ $component }}

    <div class="col-sm-10 component-choices">
        @foreach ($elements as $element)
            {{ $element }}
        @endforeach
    </div>

</div>