<div{{ $attributes }} style="border: 1px solid #ccc; padding: 20px; margin: 20px;">

    {{ $message }}

    {{ $label }}

    @if (isset($content))
        <p>{{ $content }}</p>
    @endif


    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        @foreach ($tabs as $tab)
            <li><a href="#{{ $tab->id }}" data-toggle="tab">{{ $tab->label }}</a></li>
        @endforeach
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        @foreach ($elements as $element)
            {{ $element }}
        @endforeach
    </div>

    <!-- Tab script -->
    <script>
        $('#{{ $id }} .nav a:first').tab('show');

        $('#{{ $id }} .nav a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
    </script>

</div>