<?php namespace Netfizz\FormBuilder\Component;

use Illuminate\Support\Contracts\RenderableInterface as Renderable;
use Netfizz\FormBuilder\Component\Field;
use Netfizz\FormBuilder\Component\Form;
use View, Config, HTML, RuntimeException;

class Container implements Renderable {

    protected $config;

    protected $type;

    protected $name;

    protected $content;

    protected $params;

    protected $elements = array();

    protected $template;

    protected $attributes;


    public function __construct($type = 'container', $name, $content = null, $params = array())
    {

        $this->type = $type;
        $this->name = $name;
        $this->content = $content;
        $this->config = $this->makeConfig($params);
    }

    protected function makeConfig($params)
    {

        if ( ! is_array($params)) {
            throw new RuntimeException( ucfirst($this->name) . ' params is not an array.');
        }

        $commonConfig = Config::get('form-builder::component.*', array());

        $defaultTypeConfig = Config::get('form-builder::component.'.$this->type, array());

        return array_merge($commonConfig, $defaultTypeConfig, $params);
    }

    protected function getOptions()
    {
        $options = array_get($this->config, 'options', array());

        return array_merge($options, $this->options);
    }

    public function get($selector)
    {
        // todo : créer une fonction qui retourne tous les éléments imbriqué pour pouvoir les cherchers

        $element = array_get($this->elements, $selector);

        return $element;
    }

    public static function create($name, $content = null, $params = array())
    {
        return new self('container', $name, $content, $params);
    }

    public static function form($name, $content = null, $params = array())
    {
        return new Form($name, $content, $params);
    }

    public static function fieldset($name, $content = null, $params = array())
    {
        return new self('fieldset', $name, $content, $params);
    }

    public static function text($name, $content = null, $params = array())
    {
        return new Field('text', $name, $content, $params);
    }

    public static function textarea($name, $content = null, $params = array())
    {
        return new Field('textarea', $name, $content, $params);
    }

    public static function select($name, $content = null, $params = array())
    {
        return new Field('select', $name, $content, $params);
    }






    public function add()
    {
        $elements = array();

        foreach(func_get_args() as $element)
        {

            if ($element instanceof Renderable)
            {
                $name = $element->getName() ?: null;
                $elements[$name] = $element;
            }
            elseif (is_string($element))
            {
                $elements[$element] = Container::create($element, $element);
            }
        }

        $this->elements = array_merge($this->elements, $elements);

        return $this;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }


    public function getTemplate()
    {
        return $this->template ?: array_get($this->config, 'template', 'form-builder::container');
    }





    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }




    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }



    protected function makeContent()
    {
        return $this->content;
    }

    protected function makeLabel() {
        return ucfirst($this->getName());
    }


    protected function makeAttributes()
    {
        $attributes = $this->attributes ?: array_get($this->config, 'attributes');

        if ( ! is_array($attributes)) {
            return null;
        }

        foreach($attributes as &$value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
        }

        return HTML::attributes($attributes);
    }


    public function setElements($elements)
    {
        $this->elements = $elements;
        return $this;
    }


    public function getElements()
    {
        return $this->elements;
    }

    public function makeElements()
    {
        return $this->elements;
    }



    protected function getDatas()
    {
        return array(
            'label' => $this->makeLabel(),
            'content' => $this->makeContent(),
            'attributes' => $this->makeAttributes(),
            'elements' => $this->makeElements(),
        );
    }


    public function render()
    {
        //var_dump($this->getTemplate(), $this->getDatas());
        return View::make(
            $this->getTemplate(),
            $this->getDatas()
        );
    }


    public function __toString()
    {
        return (string) $this->render();
    }
}