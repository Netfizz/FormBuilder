<?php namespace Netfizz\FormBuilder;

use App, Config;

class Formizz {

    protected $form;

    protected $model;

    protected $builder;

    protected $elements = array();

    protected $button;

    public function __construct()
    {
        $this->builder = App::make('formizz.builder');
    }

    public function setFramework($framework)
    {
        $this->builder->setFramework($framework);

        return $this;
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


    public function bind($model)
    {
        $this->model = $model;
        return $this;
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

        if ($this->model) {
            $this->form->bind($this->model);
        }

        return $this->form;
    }


    public function makeEmbedForm()
    {
        // Todo :
    }

    protected function addElements()
    {
        foreach($this->elements as $element)
        {
            $this->form->add($element);
        }
    }

    protected function addButton()
    {
        $this->form->add($this->getButton());
    }


    public function setButton($button)
    {
        $this->button = $button;
        return $this;
    }


    public function getButton()
    {
        if ( is_null($this->button) ) {
            $label = Config::get('form-builder::button', 'OK');
            $this->setButton(Component::button($label));
        }

        return $this->button;
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