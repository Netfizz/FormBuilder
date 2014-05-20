<?php

return array(

    // Set Component config file
    //'component' => 'form-builder::component',

    // Path to frameworks components config files
    'frameworks' => array(
        'none'                          => 'form-builder::component',
        'twitterBootstrap'              => 'form-builder::frameworks/twitterBootstrapDefault',
        'twitterBootstrapHorizontal'    => 'form-builder::frameworks/twitterBootstrapHorizontal',
        'twitterBootstrapInline'        => 'form-builder::frameworks/twitterBootstrapInline',
    ),

    // Default framework to use
    'framework' => 'none',

    // Default button label text
    'button' => 'Validate'

);