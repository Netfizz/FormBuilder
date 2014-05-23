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

    @if ($collection->add)
    <a href="#" class="collection-add-row">{{ $collection->add }}</a>
    @endif

    {{ $append }}

    <!-- Tab script -->
    <script type="text/javascript">

        jQuery(document).ready(function() {

            var collection = jQuery('#{{ $id }} .collection-component');
            var delta = jQuery('li', collection).length;

            jQuery('#{{ $id }} .collection-add-row').click(function() {

                @if ($collection->max)
                // Check max element
                if (delta >= {{ $collection->max }}) {
                    return false;
                }
                @endif

                // Prototype
                var newWidget = collection.attr('data-prototype');
                newWidget = newWidget.replace(/__DELTA__/g, delta);
                delta++;

                // Add new row
                var newLi = jQuery('<li></li>').html(newWidget);
                newLi.appendTo(collection);

                return false;
            });

            jQuery(document).on('click', '#{{ $id }} .collection-delete-row' , function() {
                jQuery( this ).parents('li').remove();

                console.log(jQuery( this ));
                return false;
            });



        })
    </script>

</fieldset>