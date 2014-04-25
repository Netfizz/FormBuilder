<?php namespace Netfizz\FormBuilder\Component;

use Illuminate\Support\Contracts\RenderableInterface as Renderable;
use Netfizz\FormBuilder\Component\Form;
use Netfizz\FormBuilder\Component\Field;
use Netfizz\FormBuilder\Component\Choices;
use Illuminate\Support\MessageBag;
use View, Config, HTML, App, Session, RuntimeException;

class Container implements Renderable {

    protected $config;

    protected $type;

    protected $id;

    protected $name;

    protected $content;

    protected $params;

    protected $elements = array();

    protected $template;

    protected $attributes;

    protected $messages;

    //protected $model;

    //protected $parent;
    //protected $errors;

    //protected $warnings;

    //protected $submit;

    //protected $validator;



    public function __construct($type = 'container', $name, $content = null, $params = array())
    {
        $this->type = $type;
        $this->name = $name;
        $this->content = $content;
        $this->params = $params;

        $this->setConfig();
        $this->setMessagesBags();
    }

    public function getFormService()
    {
        return App::make('formizz');
    }

    public function getModel()
    {
        return $formService = $this->getFormService()->getModel();
    }

    /*
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        //return parent::getInstance();
        return $this->parent;
    }


    public function getInstance()
    {
        return $this;
    }
    */

    /*
    public function setModel($model)
    {
        $this->model = $model;
    }
    */


    protected function setConfig()
    {
        if ( ! is_array($this->params)) {
            throw new RuntimeException( ucfirst($this->name) . ' params is not an array.');
        }

        $commonConfig = Config::get('form-builder::component.*', array());

        $defaultTypeConfig = Config::get('form-builder::component.'.$this->type, array());

        $this->config = array_merge($commonConfig, $defaultTypeConfig, $this->params);
    }



    protected function setMessagesBags()
    {
        $states = array_get($this->config, 'message.states', array());
        $format = array_get($this->config, 'message.format', null);

        foreach ($states as $state => $value)
        {
            if (! $message = Session::get($state))
            {
                $message = new MessageBag;
            }

            $message->setFormat($format);

            $this->messages[$state] = $message;
        }
    }

    public function makeMessage()
    {

        $states = array_get($this->config, 'message.states', array());
        $showMethod = array_get($this->config, 'message.show', 'first');

        foreach($states as $state => $class)
        {
            if ($this->messages[$state]->has($this->name)) {

                $this->addClass($class);

                $message = $this->messages[$state]->$showMethod($this->name);

                return is_array($message) ? implode(PHP_EOL, $message) : $message;
            }
        }

        return null;
    }


    protected function getOptions()
    {
        $options = array_get($this->config, 'options', array());

        return array_merge($options, $this->params);
    }

    protected function getFlattenOptions()
    {
        $options = $this->getOptions();
        foreach($options as &$option) {
            if (is_array($option)) {
                $option = implode(' ', $option);
            }
        }

        return $options;
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

    public static function email($name, $content = null, $params = array())
    {
        return new Field('email', $name, $content, $params);
    }

    public static function textarea($name, $content = null, $params = array())
    {
        return new Field('textarea', $name, $content, $params);
    }

    public static function select($name, $choices = array(), $content = null, $params = array())
    {
        return new Choices('select', $name, $choices, $content, $params);
    }

    public static function multiselect($name, $choices = array(), $content = null, $params = array())
    {
        $params = array_merge($params, array('multiple' => 'multiple'));
        return new Choices('select', $name, $choices, $content, $params);
    }

    public static function radio($name, $choices = array(), $content = null, $params = array())
    {
        return new Choices('radio', $name, $choices, $content, $params);
    }

    public static function radios($name, $choices = array(), $content = null, $params = array())
    {
        return new Choices('radios', $name, $choices, $content, $params);
    }

    public static function checkbox($name, $choices = array(), $content = null, $params = array())
    {
        return new Choices('checkbox', $name, $choices, $content, $params);
    }

    public static function checkboxes($name, $choices = array(), $content = null, $params = array())
    {
        return new Choices('checkboxes', $name, $choices, $content, $params);
    }

    public static function submit($name, $content = null, $params = array())
    {
        return new Field('submit', $name, $content, $params);
    }

    public static function button($name, $content = null, $params = array())
    {
        return new Field('button', $name, $content, $params);
    }


    /**
     * @return $this
     */
    public function add()
    {
        $elements = array();

        foreach(func_get_args() as $delta => $element)
        {
            // Todo : remplacer Renderable par une interface pour les champs
            if ($element instanceof Renderable)
            {
                $id = $element->getId() ?: $delta;
                $elements[$id] = $element;
            }
            elseif (is_string($element))
            {
                $elements[$delta] = Container::create($delta, $element);
            }
        }

        $this->elements = array_merge($this->elements, $elements);

        return $this;
    }


    /**
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }


    /**
     * @return mixed
     */
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

    public function getId()
    {
        return $this->id ?: $this->makeId();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    protected function makeId()
    {
        $id = $this->name;
        $this->setId($id);

        return $id;
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

        $label = array_get($this->config, 'label');

        if (is_array($label)) {
            return array_get($label, 'label', ucwords(str_replace(array('_', '[]'), array(' ', ''), $this->getName())));
        }

        if (is_string($label)) {
            return $label;
        }

        return null;
    }

    protected function isRequired()
    {

        if (array_get($this->config, 'required')) {
            $class = array_get($this->config, 'require.class');
            $this->addClass($class);
            return true;
        }

        return false;
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




    public function addClass($class, $element = 'container')
    {
        if ($element == 'container')
        {
            $currentClass = array_get($this->config, 'attributes.class');

            if (! is_array($currentClass)) {
                $currentClass = array($currentClass);
            }

            $currentClass[] = $class;

            array_set($this->config, 'attributes.class', $currentClass);
        }
    }



    protected function getDatas()
    {
        return array(
            'message' => $this->makeMessage(),
            'label' => $this->makeLabel(),
            'content' => $this->makeContent(),
            'attributes' => $this->makeAttributes(),
            'elements' => $this->makeElements(),
        );
    }


    public function render()
    {

        if ($this->type == 'container') {
            return $this->content;
        }

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