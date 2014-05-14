<?php namespace Netfizz\FormBuilder;

//use FormBuilder;

class Foozz {

    protected $bar = 'baz';
    protected $builder;

    /*
    public function __construct()
    {

        $this->builder = $builder;

        //var_dump($builder);

    }
    */

    function get()
    {
        return $this->bar;
    }
} 