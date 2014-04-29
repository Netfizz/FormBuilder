<?php

return array(

    // default params for every element
    '*' => array(
        // Blade template path for rendering component
        'template' => 'form-builder::component.field',

        // The class to be added to required fields
        'require' => array(
            'text' => ' <sup>*</sup>',
            'class' => 'required',
        ),
        /*
        'class' => array(
            'required'  => 'required',

        ),
        */

        // messageBag Formating when there are error
        //'messageBagFormat' => '<p>:message</p>',

        // messageBag
        'message' => array(
            'format' => '<p class="alert alert-danger">:message</p>',
            'show' => 'first', // or 'get' all
            'states' => array(
                'errors'     => 'has-error',
                'warnings'   => 'has-warning',
                'success'   => 'has-success'
            )
        ),


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

        'label' => array(
            'label' => 'Test BLA BLA',
            'class' => 'control-label'
        ),
        //'required' => true,
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


    'radio' => array(
        'attributes' => array(
            'class' => 'radio'
        ),
        'options' => array(
            'class' => null,
        ),
    ),

    'radios' => array(
        'options' => array(
            'class' => null,
        ),
    ),


    'checkbox' => array(
        'attributes' => array(
            'class' => 'checkbox'
        ),
        'options' => array(
            'class' => null,
        ),
    ),

    'checkboxes' => array(
        'options' => array(
            'class' => null,
        ),
    ),


    'boolean' => array(
        'attributes' => array(
            'class' => 'checkbox'
        ),
        'options' => array(
            'class' => null,
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