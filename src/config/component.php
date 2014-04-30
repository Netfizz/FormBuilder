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


        // messageBag
        'messages' => array(
            'errors'     => array(
                'wrapperClass' => 'has-error',
                'format' => '<p class="alert alert-danger">:message</p>',
            ),
            'warnings'     => array(
                'wrapperClass' => 'has-warning',
                'format' => '<p class="alert alert-warning">:message</p>',
            ),
            'success'     => array(
                'wrapperClass' => 'has-success',
                'format' => '<p class="alert alert-success">:message</p>',
            )
        ),

        // Container attributes
        'wrapper' => array(
            'class' => 'form-group',
            //'style' => 'border: 1px solid #ccc; padding: 20px; margin: 20px;'
        ),


        // Input options
        'options' => array(
            'class' => 'form-control',
        ),

        // Labels
        'label' => array(
            'class' => 'control-label'
        )

    ),


    'form' => array(
        'template' => 'form-builder::form',

        // Input options
        'options' => array(
            'method' => 'put',
            'class' => 'form-horizontal',
            'role' => 'form'
        ),
    ),


    'tabs' => array(
        'template' => 'form-builder::component.tabs',
        'label' => null
    ),

    'tab' => array(
        'label' => null,
        'wrapper' => array(
            'class' => 'tab-pane'
        ),

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
        'wrapper' => array(
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
        'wrapper' => array(
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
        'wrapper' => array(
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