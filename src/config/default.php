<?php

return array(



    // Elements parts of component
    /*
    'elements' => array(
        'wrapper',
        'label',
        'field',
        'append',
        'prepend'
    ),
    */


    'default' => array(

        // default params for every element
        '*' => array(
            // The class to be added to required fields
            'required_class'    => 'required',

            // A facultative text to append to the labels of required fields
            'required_text'     => '<sup>*</sup>',

            // blade template path for rendering component
            'template' => 'form-builder::component',


            'field' => array(
                'class' => 'test1',
                'att' => array('a', 'b')
            ),

            'wrapper' => array(
                'class' => 'form-group'
            ),

            'label' => array(
                'class' => 'lab'
            )

        ),


        'textarea' => array(
            'template' => 'form-builder::component-multipdsd',

            'field' => array(
                'class' => 'test2',
                'att' => array('c', 'd'),
                'rows' => 10,
                'columns' => 20,
            ),

            /*
            'prepend' => array(
                'class' => 'prep',
                'content' => 'xxx'
            )
            */
        ),


        'multiselect' => array(
            'field' => array(
                'multiple',
                'class' => 'test',
            ),

            'wrapper' => array(
                'class' => 'form-group-multi'
            ),

            'append' => 'testgdfgfdgfdgfd'
        )
    ),

    /**
     *  Every key params that is not an element
     */

    'elements_exceptions' => array(
        'required_class',
        'required_text',
        'template'
    )

);