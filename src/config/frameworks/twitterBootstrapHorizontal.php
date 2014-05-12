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


        // Labels attributes
        'label' => array(
            'class' => 'control-label col-sm-2'
        ),


        // Component attributes (Field input, etc.)
        'component' => array(
            'class' => 'form-control',
        )
    ),


    'form' => array(
        'template' => 'form-builder::form',
        'component' => array(
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
        /*
        'label' => array(
            'label' => 'Test BLA BLA',
            'class' => 'control-label'
        ),
        'required' => true,
        */
        'component' => array(
            'class' => 'form-control test2',
            //'att' => array('c', 'd'),
            'rows' => 10,
            'columns' => 20,
        ),
    ),


    'multiselect' => array(
        'component' => array(
            'multiple',
            'class' => 'test',
        ),
    ),


    'radio' => array(
        'wrapper' => array(
            'class' => 'radio'
        ),
        'component' => array(
            'class' => null,
        ),
        'label' => array(
            'class' => 'radio-inline',
            //'foo' => 'bar'
        ),
    ),

    'radios' => array(
        'component' => array(
            'class' => null,
        ),
    ),


    'checkbox' => array(
        'wrapper' => array(
            'class' => 'checkbox'
        ),
        'component' => array(
            'class' => null,
        ),
        'label' => array(
            'class' => 'checkbox-inline',
        ),
    ),

    'checkboxes' => array(
        'component' => array(
            'class' => null,
        ),
    ),


    'boolean' => array(
        'wrapper' => array(
            'class' => 'checkbox'
        ),
        'component' => array(
            'class' => null,
        ),
    ),



    'submit' => array(
        'template' => 'form-builder::component.button',
        'label' => null,
        'component' => array(
            'class' => 'btn btn-primary'
        )
    ),

    'button' => array(
        'template' => 'form-builder::component.button',
        'label' => null,
        'component' => array(
            'type' => 'submit',
            'class' => 'btn btn-primary'
        )
    ),

);