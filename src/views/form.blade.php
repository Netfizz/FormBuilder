{{ $form->open }}

    @foreach ($form->elements as $element)
        {{ $element }}
    @endforeach

    {{ $form->buttons }}

{{ $form->close }}form.blade.php