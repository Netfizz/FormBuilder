<?php

return array(
    'method' => 'put',

    'secure' => false,

    'foo' => 'bar',

    'fields' => array(


    ),


    // params not injected to former open object
    'extras_params' => array(

        'except_fields' => array('id', 'updated_at', 'deleted_at', 'password'),

        'default_fields_params' => array(
            'textarea' => array(
                'rows' => 10,
                'columns' => 20,
            )
        )
    )

);