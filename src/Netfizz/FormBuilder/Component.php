<?php namespace Netfizz\FormBuilder;

use Illuminate\Support\Contracts\RenderableInterface as Renderable;
use Netfizz\FormBuilder\Component\Form;
use Netfizz\FormBuilder\Component\Field;
use Netfizz\FormBuilder\Component\Choices;
use Netfizz\FormBuilder\Component\Tabs;
use Illuminate\Support\MessageBag;
use View, Config, HTML, App, Session, RuntimeException;

class Component implements Renderable {

    protected $builder;

    protected $config;

    protected $configFile = 'form-builder::component';

    protected $type;

    protected $id;

    protected $name;

    protected $label;

    protected $content;

    protected $params;

    protected $elements = array();

    protected $template;

    protected $attributes;

    protected $messages;


    public function __construct($type = 'container', $name, $content = null, $params = array())
    {
        $this->builder = App::make('formizz.builder');

        $this->type = $type;
        $this->name = $name;
        $this->content = $content;
        $this->params = (array) $params;

        $this->initConfig();
        $this->initMessagesBags();
        $this->initLabel();
    }


    protected function initConfig()
    {
        /*
        // Get component config filename
        $filename = $this->builder->getComponentConfigFilename();

        // Get common config
        $common = Config::get($filename . '.*', array());

        // Get element type config
        $type = Config::get($filename . '.' . $this->getType(), array());
        */

        // Get component config filename
        $config = $this->builder->getConfig();
        //var_dump($config);


        // Get common config
        $common = array_get($config, '*', array());

        // Get element type config
        $type = array_get($config, $this->getType(), array());

        //var_dump($config, $common, $type, $this->params);


        // Merge both
        $this->config = array_merge($common, $type, $this->params);
    }


    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        if ( is_string($config) ) {
            throw new RuntimeException( ucfirst($config) . ' config is not an array.');
        }

        $this->config = $config;

        return $this;
    }



    public function attribute($name = null, $value = null, $element = 'component')
    {
        if ($name === null && $value === null)
        {
            return array_get($this->config, $element, array());
        }

        if ($name && $value === null)
        {
            return array_get($this->config[$element], $name, array());
        }

        if ($name && $value)
        {
            array_set($this->config[$element], $name, $value);
            return $this;
        }
    }


    public function attributes($element = 'component')
    {
        return $this->attribute(null, null, $element);
    }


    protected function array_flatten($array)
    {
        if ( ! is_array($array) ) {
            return array();
        }

        foreach($array as &$value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
        }

        return $array;
    }

    /*
    protected function getAttributes($element = 'component')
    {
        $params = $this->attribute(null, null, $element);

        //var_dump($params);

        foreach($params as $key => $option) {
            if (is_array($option)) {
                $params[$key] = implode(' ', $option);
            }
        }

        if ($element == 'component' && ! array_key_exists('id', $params))
        {
            $params['id'] = $this->getId();
        }

        $exceptions = array(
            'label'
        );

        return array_except($params, $exceptions);
    }
    */

    /*
    protected function getFlattenOptions()
    {
        $options = $this->getOptions();
        foreach($options as &$option) {
            if (is_array($option)) {
                $option = implode(' ', $option);
            }
        }

        if ( ! array_key_exists('id', $options))
        {
            $options['id'] = $this->getId();
        }

        $exceptions = array(
            'label'
        );



        return array_except($options, $exceptions);
    }
    */


    protected function initMessagesBags()
    {
        $states = array_get($this->config, 'messages', array());

        foreach ($states as $state => $params)
        {
            if (! $message = Session::get($state))
            {
                $message = new MessageBag;
            }
            //$format = array_get($this->config, 'message.format', null);
            $message->setFormat($params['format']);

            $this->messages[$state] = $message;
        }
    }

    public function makeMessage()
    {

        $states = array_get($this->config, 'messages', array());

        foreach($states as $state => $params)
        {
            if ($this->messages[$state]->has($this->name)) {

                $this->addClass($params['wrapperClass']);

                $message = $this->messages[$state]->first($this->name);

                return is_array($message) ? implode(PHP_EOL, $message) : $message;
            }
        }

        return null;
    }


    public function getBuilder()
    {
        return $this->builder;
    }

    public function getModel()
    {
        return $formService = $this->builder->getModel();
    }

    public function required()
    {
        $this->config['required'] = true;
        array_set($this->config, 'component.required', 'required');

        return $this;
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

    public static function form($name = null, $content = null, $params = array())
    {
        return new Form($name, $content, $params);
    }

    public static function fieldset($name, $content = null, $params = array())
    {
        return new self('fieldset', $name, $content, $params);
    }

    public static function tabs($name, $content = null, $params = array())
    {
        return new Tabs('tabs', $name, $content, $params);
    }

    public static function tab($name, $content = null, $params = array())
    {
        return new Tabs('tab', $name, $content, $params);
    }

    public static function text($name, $content = null, $params = array())
    {
        return new Field('text', $name, $content, $params);
    }

    public static function file($name, $content = null, $params = array())
    {
        return new Field('file', $name, $content, $params);
    }

    public static function password($name, $content = null, $params = array())
    {
        return new Field('password', $name, $content, $params);
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

    public static function radio($name, $choices = null, $content = null, $params = array())
    {
        $type = is_string($choices) ? 'radio' : 'radios';

        return new Choices($type, $name, $choices, $content, $params);
    }

    public static function radios($name, $choices = array(), $content = null, $params = array())
    {
        return new Choices('radios', $name, $choices, $content, $params);
    }

    public static function inline_radios($name, $choices = array(), $content = null, $params = array())
    {
        return new Choices('inline_radios', $name, $choices, $content, $params);
    }

    public static function checkbox($name, $choices = null, $content = null, $params = array())
    {
        $type = is_string($choices) ? 'checkbox' : 'checkboxes';

        return new Choices($type, $name, $choices, $content, $params);
    }

    public static function checkboxes($name, $choices = array(), $content = null, $params = array())
    {
        return new Choices('checkboxes', $name, $choices, $content, $params);
    }

    public static function boolean($name, $choices = null, $content = null, $params = array())
    {
        return new Choices('boolean', $name, $choices, $content, $params);
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
                $elements[$delta] = Component::create($delta, $element);
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
        return $this->template ?: array_get($this->config, 'template', 'form-builder::component.field');
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

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
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
        $id = $this->getPrefixId().ucfirst($this->name);
        $this->setId($id);

        return $id;
    }

    public function getPrefixId()
    {
        return $this->builder->getFormId();
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

    public function initLabel()
    {
        $label = array_get($this->config, 'label', false);

        if (is_array($label)) {
            $label = array_get($label, 'label', false);
        }

        if ($label === false) {
            $label = $this->autoGenerateLabel();
        }

        $this->setLabel($label);
    }

    public function autoGenerateLabel()
    {
        return ucwords(str_replace(array('_', '[]'), array(' ', ''), $this->getName()));
    }

    public function removeLabel()
    {
        $this->setLabel(null);
    }


    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }


    protected function makeLabel()
    {
        return $this->getType() == 'container' ? null : $this->getLabel();
    }


    protected function makeWrapperAttributes()
    {
        $attributes = $this->attributes('wrapper');

        return HTML::attributes($this->array_flatten($attributes));
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
        return $this->getElements();
    }


    public function addClass($class, $element = 'wrapper')
    {
        /*
        //if ($element == 'container')
        //{
            $currentClass = array_get($this->config, $element.'.class');

            if (! is_array($currentClass)) {
                $currentClass = array($currentClass);
            }

            $currentClass[] = $class;

            array_set($this->config, $element.'.class', $currentClass);
        //}
        */
    }



    protected function getDatas()
    {
        return array(
            'message'       => $this->makeMessage(),
            'component'     => $this->makeContent(),
            'label'         => $this->makeLabel(),
            'attributes'    => $this->makeWrapperAttributes(),
            'elements'      => $this->makeElements(),
        );
    }


    public function render()
    {
        //var_dump($this->config); //, $this->getDatas());

        //return true;

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