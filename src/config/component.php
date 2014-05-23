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
        ),


        // Labels attributes
        'label' => array(
            'class' => 'control-label'
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
            'class' => null,
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


    'help' => array(
        'template' => 'form-builder::component.paragraph',
        'wrapper' => array(
            'class' => 'help-block'
        ),
    ),


    'collection' => array(
        'template' => 'form-builder::component.collection',
        'element_min' => 1,
        'element_max' => 0, // 0 = unlimited
        'element_add' => '<span class="glyphicon glyphicon-plus"></span> Add another element',
        'element_del' => '<span class="glyphicon glyphicon-remove"></span> Remove',
        'element_sort' => true,
    ),


    'textarea' => array(
        'component' => array(
            'class' => 'form-control test2',
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
            'class' => null,
        ),
    ),


    'inline_radios' => array(
        'component' => array(
            'template' => 'form-builder::component.component-only',
            'label' => array(
                'class' => 'radio-inline'
            ),
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
            'class' => null,
        ),
    ),


    'checkboxes' => array(
        'component' => array(
            'template' => 'form-builder::component.field',
        ),
    ),


    'inline_checkboxes' => array(
        'component' => array(
            'template' => 'form-builder::component.component-only',
            'label' => array(
                'class' => 'checkbox-inline'
            ),
        ),
    ),


    'boolean' => array(
        'wrapper' => array(
            'class' => 'checkbox'
        ),
        'component' => array(
            'class' => null,
        ),
        'label' => array(
            'class' => null,
        ),
    ),


    'file' => array(
        'component' => array(
            'class' => null,
        ),
    ),


    'submit' => array(
        'label' => null,
        'component' => array(
            'class' => 'btn btn-primary'
        )
    ),


    'button' => array(
        'label' => null,
        'component' => array(
            'type' => 'submit',
            'class' => 'btn btn-primary'
        )
    ),

);