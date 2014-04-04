<?php namespace Netfizz\FormBuilder;

use Netfizz\FormBuilder\Component\Element;
use Netfizz\Core\Traits\Attributes;

use Config, Form, View, RuntimeException;

class Component {

    use Attributes;

    protected $config;

    protected $name;

    protected $type;

    protected $value;

    protected $attributes;

    protected $choices;

    protected $label;

    protected $elements;    // field, append, prepend, label

    protected $template;


    /**
     * Every params which not attributes
     * @var array
     */
    protected $params = array(
        'type',
        'value',
        'choices',
        'label'
    );

    protected $sections = array(
        'wrapper',
        'label',
        'field',
        'append',
        'prepend'
    );

    public function __construct($name, $type = 'text', $value = null, $attributes = null, $choices = null)
    {

        $this->setConfig(Config::get('form-builder::default', array()))
            ->setName($name)
            ->setType($type)
            ->setValue($value)
            ->setAttributes($attributes)
            ->setChoices($choices);
            //->makeDefaultElements();
    }


    /**
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }


    protected function makeDefaultElements()
    {
        $exceptions = array_get($this->config, 'excepts_elements', array());
        $elements = array_except($this->getAttributes(), $exceptions);

        foreach($elements as $name => $attributes){
            $method = 'Set'.ucfirst($name);
            if (method_exists($this, $method))
            {
                $this->$method($attributes);
            } else {
                $this->makeElement($name, $attributes);
            }
        }

    }

    public function getElements()
    {
        $this->makeDefaultElements();
        return $this->elements;
    }


    public function makeElement($name, $attributes)
    {
        $this->elements[$name] = new Element($name, $attributes);
    }


    public function getElement($name)
    {
        return array_key_exists($name, $this->elements) ?
            $this->elements[$name] : null;
    }


    public function setElement($name, Element $element)
    {
        $this->elements[$name] = $element;
    }




    public function setParams(array $params)
    {
        foreach($this->params as $param) {
            $this->setParam($param, $params);
        }

        $this->mergeAttributes($params);

        var_dump($this->name, $this->attributes);
    }

    protected function setParam($param, array &$params)
    {
        if (array_key_exists($param, $params))
        {
            $method = 'set' . ucfirst($param);
            $this->$method($params[$param]);
            unset($params[$param]);
        }
    }


    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }


    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }




    public function getAttributes() {

        if ($this->attributes === null) {
            $this->setDefaultAttributes();
        }

        return $this->attributes;
    }

    protected function setDefaultAttributes()
    {
        $all = array_get($this->config, 'default.*', array());

        $type = array_get($this->config, 'default.' . $this->type, array());

        $this->attributes = array_merge($all, $type);

        return $this;
    }



    public function setChoices($choices)
    {
        $this->choices = $choices;
        return $this;
    }


    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }


    public function getTemplate()
    {
        if ($this->template === null) {
            $this->template = array_get($this->attributes, 'template', 'form-builder::component');
        }

        return $this->template;
    }


    public function get()
    {
        return $this;
    }


    public function render($template = null)
    {
        if(is_null($template)) {
            $template = $this->getTemplate();
        }

        $elements = $this->getElements();

        var_dump($elements);
        //die;

        //var_dump($template, View::exists($template));
        return true;

        if ( View::exists($template) ) {
            return View::make($template, $elements);
        } else {
            return 'Error, template "' . $template .'" doesn\'t exists';
        }
    }


    protected function make()
    {
        // multiselect
        // checkboxes
        // radios
    }


    public function __toString()
    {
        return (string) $this->render();
    }
} 