<div class="form-group">

    {{ $message }}
    {{ $label }}

    <div class="col-sm-offset-2 col-sm-10">
        <div{{ $attributes }}>

            {{ $component }}

        </div>
    </div>

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach

</div>