<div style="border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; background:#ddd;">
    {{ $name or '' }}

    @foreach ($elements as $element)
        {{ $element }}
    @endforeach
</div>