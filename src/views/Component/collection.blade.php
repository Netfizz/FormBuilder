<fieldset id="{{ $id }}"{{ $attributes }}>

    @if (isset($label))
    <legend>{{ $label }}</legend>
    @endif

    {{ $prepend }}

    {{ $component }}

    {{ $message }}

    <!-- subforms collection -->
    <ol{{ $collection->attributes }}>
        @foreach ($elements as $element)
            {{ $element }}
        @endforeach
    </ol>

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
                nb_element = jQuery('#{{ $id }} .collection-component li').length;
                if (nb_element >= {{ $collection->max }}) {
                    return false;
                }
                @endif

                // Prototype
                var newWidget = collection.attr('data-prototype');
                newWidget = newWidget.replace(/__DELTA__/g, delta);
                delta++;

                // Add new row
                jQuery(collection).append(newWidget);

                // Scroll to bottom
                $('html, body').animate({
                    scrollTop: (jQuery(this).offset().top)
                }, 500);

                return false;
            });

            jQuery(document).on('click', '#{{ $id }} .collection-delete-row' , function() {
                jQuery( this ).parents('li').remove();
                return false;
            });

            @if ($sortable)
            jQuery('#{{ $id }} .collection-component').sortable({
                nested: false
            });

            @endif
        })
    </script>

</fieldset>