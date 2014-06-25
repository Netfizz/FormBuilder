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
        $this->setForm(Component::form());
    }


    public function __clone()
    {
        $this->form = clone $this->form;

        foreach ($this->getElements() as $delta => $element)
        {
            if (is_object($element) || (is_array($element))) {
                $this->elements[$delta] =  clone $element;
            }
        }

    }


    public function getId() {
        return null;
    }

    public function getDataId()
    {

    }

    public function setFramework($framework)
    {
        $this->builder->setFramework($framework);
        return $this;
    }


    public function resetMessages()
    {
        $this->recursiveAttribute($this->getElements(), null, array(), 'messages');
        return $this;
    }


    public function recursiveAttribute($elements = array(), $name = null, $value = null, $part = 'component')
    {
        if (empty($elements))
        {
            $elements = $this->getElements();
        }

        foreach ($elements as $element)
        {
            if (is_subclass_of($element, 'Netfizz\FormBuilder\Component'))
            {
                $element->attribute($name, $value, $part);
                if ($childs = $element->getElements())
                {
                    $this->recursiveAttribute($childs, $name, $value, $part);
                }
            }
        }
    }


    public function embed($name = null, $delta = 0)
    {

        if ($name === null)
        {
            return $this->embed;
        }

        $this->embed = $name;
        $this->delta = $delta;

        // Overide form container by an embed one
        $this->form = Component::embedContainer();
        $this->embedElements();

        return $this;
    }


    public function embedElements($embed = null)
    {
        if ($embed === null)
        {
            $embed = $this->embed;
        }

        foreach($this->getElements() as $element) {
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
        $this->addElements();
        $this->addButton();

        if ($this->model)
        {
            $this->form->bind($this->model);
        }

        return $this->getForm();
    }


    public function makeEmbedForm()
    {
        $this->addElements();

        return $this->getForm();
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getElements()
    {
        return $this->elements;
    }

    protected function addElements()
    {
        foreach($this->getElements() as $element)
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