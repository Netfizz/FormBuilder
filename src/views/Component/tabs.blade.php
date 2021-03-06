<div{{ $attributes }}>

    {{ $message }}

    {{ $label }}

    @if (isset($component))
        <p>{{ $component }}</p>
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