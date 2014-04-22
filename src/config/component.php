<?php

return array(

    // default params for every element
    '*' => array(
        // Blade template path for rendering component
        'template' => 'form-builder::component.field',

        // The class to be added to required fields
        //'requiredClass'    => 'required',
        'class' => array(
            'required'  => 'required',
            'error'     => 'has-error',
            'success'   => 'has-success',
            'warning'   => 'has-warning'
        ),

        // messageBag Formating when there are error
        'messageBagFormat' => '<p>:message</p>',


        // A facultative text to append to the labels of required fields
        'requiredText'     => '<sup>*</sup>',

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
            'class' => 'control-label'
        )

    ),


    'form' => array(
        'template' => 'form-builder::component.form',

        // Input options
        'options' => array(
            'method' => 'put',
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
    ),

    'submit' => array(
        'label' => null,
        'options' => array(
            'class' => 'btn btn-primary'
        )
    ),

    'button' => array(
        'label' => null,
        'options' => array(
            'type' => 'submit',
            'class' => 'btn btn-primary'
        )
    ),

);