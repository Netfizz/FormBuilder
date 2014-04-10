<?php

return array(

    // default params for every element
    '*' => array(
        // The class to be added to required fields
        'required_class'    => 'required',

        // A facultative text to append to the labels of required fields
        'required_text'     => '<sup>*</sup>',

        // Blade template path for rendering component
        'template' => 'form-builder::component.field',

        // Input options
        'options' => array(
            'class' => 'form-control',
        ),

        // Container attributes
        'attributes' => array(
            'class' => 'form-group'
        ),

        // Labels
        'label' => array(
            'class' => 'lab'
        )

    ),


    'form' => array(
        'template' => 'form-builder::component.form',

        // Input options
        'options' => array(
            'class' => 'form-horizontal',
            'role' => 'form'
        ),
    ),


    'tabs' => array(
        //'template' => 'form-builder::component.tabs',
    ),


    'fieldset' => array(
        'template' => 'form-builder::component.fieldset',
    ),


    'textarea' => array(
        'options' => array(
            'class' => 'form-control test2',
            'att' => array('c', 'd'),
            'rows' => 10,
            'columns' => 20,
        ),
    ),


    'multiselect' => array(
        'options' => array(
            'multiple',
            'class' => 'test',
        ),
    )

);