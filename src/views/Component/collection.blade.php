<fieldset id="{{ $id }}"{{ $attributes }}>

    @if (isset($label))
    <legend>{{ $label }}</legend>
    @endif

    {{ $prepend }}

    {{ $component }}

    {{ $message }}

    <!-- subforms collection -->
    <ul class="collection-component"{{ $collection->prototype }}>
        @foreach ($elements as $element)
            <li>
                {{ $element }}
            </li>
        @endforeach
    </ul>

    <a href="#" class="collection-add-row">Add another element</a>

    {{ $append }}

    <!-- Tab script -->
    <script type="text/javascript">

        jQuery(document).ready(function() {

            var delta = jQuery('#{{ $id }} .collection-component').length;

            jQuery('#{{ $id }} .collection-add-row').click(function() {
                // parcourt le template prototype
                var newWidget = jQuery('#{{ $id }} .collection-component').attr('data-prototype');
                newWidget = newWidget.replace(/__DELTA__/g, delta);
                delta++;

                // créer une nouvelle liste d'éléments et l'ajoute à notre liste
                var newLi = jQuery('<li></li>').html(newWidget);
                newLi.appendTo(jQuery('#{{ $id }} .collection-component'));

                console.log(delta);
                return false;
            });
        })
    </script>

</fieldset>