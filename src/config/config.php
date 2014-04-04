<?php

return array(
    'form' => array(

        'method' => 'put',

        'secure' => false,

        'foo' => 'bar',

        'class' => 'myform'
    ),


    'fields' => array(),


    // params not injected to former open object
    'extras_params' => array(


        'except_fields' => array('id', 'updated_at', 'deleted_at', 'password'),



    )

);