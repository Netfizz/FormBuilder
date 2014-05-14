<?php namespace Netfizz\FormBuilder;

//use Netfizz\FormBuilder\Foozz;
use App, View;

class Formizz {

    protected $form;

    protected $config;

    protected $builder;

    protected $elements = array();

    public function __construct()
    {
        $this->builder = App::make('formizz.builder');
    }

    public function getBuilder()
    {
        return $this->builder;
    }


    public function setBuilder($builder)
    {
        $this->builder = $builder;
        return $this;
    }


    public function setConfig($config)
    {
        $this->builder->setConfig($config);

        return $this;
    }


    public function getConfig()
    {
        return $this->builder->getConfig();
    }



    public function add()
    {
        $elements = array();

        foreach(func_get_args() as $delta => $element)
        {
            $elements[$delta] = $element;
        }

        $this->elements = array_merge($this->elements, $elements);

        return $this;
    }




    public function makeForm()
    {
        // instanciate form
        $this->form = Component::form();
        $this->addElements();
        $this->addButton();

        return $this->form;
    }


    public function makeEmbedForm()
    {
        /*
        // instanciate form
        $this->form = Component::form();
        $this->addElements();
        $this->addButton();

        return $this->form;

        */
    }

    protected function addElements(){
        foreach($this->elements as $element)
        {
            $this->form->add($element);
        }
    }

    protected function addButton(){
        $this->form->add(Component::button('valider'));
    }


    public function render()
    {
        return $this->makeForm()->render();
    }


    public function __toString()
    {
        return (string) $this->render();
    }
} 