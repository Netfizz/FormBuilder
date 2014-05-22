<?php namespace Netfizz\FormBuilder;

use Illuminate\Support\Contracts\RenderableInterface as Renderable;
use App, Config;

class Formizz implements Renderable {

    protected $form;

    protected $model;

    protected $builder;

    protected $elements = array();

    protected $button;

    protected $embed;

    protected $delta = 0;

    public function __construct()
    {
        $this->builder = App::make('formizz.builder');
    }


    public function __clone()
    {
        foreach ($this->elements as $delta => $element)
        {
            if (is_object($element) || (is_array($element))) {
                $this->elements[$delta] =  clone $element;
            }
        }

    }


    public function getId() {
        return null;
    }

    public function setFramework($framework)
    {
        $this->builder->setFramework($framework);
        return $this;
    }

    public function embed($name = null, $delta = 0)
    {

        if ($name === null)
        {
            return $this->embed;
        }

        $this->embed = $name;
        $this->delta = $delta;

        $this->embedElements();


        return $this;
    }


    public function embedElements($embed = null)
    {
        if ($embed === null)
        {
            $embed = $this->embed;
        }

        foreach($this->elements as $element) {
            if ( method_exists($element, 'embed') )
            {
                $element->embed($embed, $this->delta);
            }
        }
    }


    public function isEmbed()
    {
        return $this->embed() ? true : false;
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

        if ($this->model)
        {
            $this->form->bind($this->model);
        }

        return $this->form;
    }


    public function makeEmbedForm()
    {
        // Todo :
        // instanciate form
        $this->form = Component::create();
        $this->addElements();

        return $this->form;
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
        return $this->isEmbed() ?
            $this->makeEmbedForm()->render() :
            $this->makeForm()->render() ;
    }


    public function __toString()
    {
        return (string) $this->render();
    }
} 