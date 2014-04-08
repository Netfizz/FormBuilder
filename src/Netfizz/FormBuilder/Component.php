<?php namespace Netfizz\FormBuilder;

use Netfizz\FormBuilder\Component\Element;
use Netfizz\Core\Traits\Attributes;
use Illuminate\Filesystem\Filesystem;


use Config, Form, View, RuntimeException, App;

class Component {

    use Attributes;

    protected $config;

    protected $name;

    protected $type;

    protected $value;

    protected $options;

    protected $attributes;

    protected $choices;

    protected $label;

    protected $elements;    // field, append, prepend, label

    protected static $elementsName = array();

    protected $template;

    protected $params;

    protected $childs;      // childs components

    //protected $view;

    public function __construct($name, $type = 'text', $params = array())
    {

        //$this->view = App::make('view');

        $this->setConfig(Config::get('form-builder::default', array()))
            ->setName($name)
            ->setType($type)
            ->setParams($params);
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

    protected function setDefaultParams()
    {
        $all = array_get($this->config, 'default.*', array());

        $type = array_get($this->config, 'default.' . $this->type, array());

        $default = array_merge($all, $type);

        $this->params = $default;

        return $this;
    }




    public function setParams(array $params, $clear = false)
    {
        // set default params for this component type if is not set OR if type change
        if ($clear || is_null($this->params) || ( isset($params['type']) && $params['type'] != $this->type ))
        {
            $this->setDefaultParams();
        }

        // merge params with default params
        $this->params = array_merge($this->params, $params);

        //$fieldAttributes = $this->params;
        foreach(array_keys($this->params) as $name)
        {
            $this->callParamMethod($name);
        }

        //var_dump('setParam', $fieldParams);

        // todo : a supprimer,
        //$template = $this->getTemplate();
        //$this->getElements($template);

        return $this;
    }

    protected function setOptions($options = null)
    {
        if ( ! $this->options && is_null($options) ) {
            $this->options = $this->params;

            foreach (array_keys($this->options) as $option)
            {
                if (method_exists($this, 'set' . ucfirst($option)))
                {
                    unset($this->options[$option]);
                }
            }
        }

        //$this->options = array_except($this->options, )
        //$this->
    }

    protected function getFieldOptions()
    {
        /*
        $template = $this->getTemplate();

        $exception = $this->getElementNamesFromTemplate($template);
        $option = array_except($this->params, $exception);

        var_dump($notAttributesParams);
        */

        return $this->params['fields'];
    }


    protected function callParamMethod($name)
    {
        if (array_key_exists($name, $this->params) && method_exists($this, 'set' . ucfirst($name)))
        {
            $method = 'set' . ucfirst($name);
            $this->$method($this->params[$name]);
            //unset($params[$name]);
        }
    }


    public function getElements($template)
    {
        if ( is_null($elementNames = $this->getElementNamesFromTemplate($template)))
        {
            return array();
        }

        $this->makeElements($elementNames);

        return $this->elements;
    }


    protected function makeElements($elementNames)
    {

        foreach (array_except($elementNames, 'field') as $name)
        {
            $attributes = array_key_exists($name, $this->params) ? $this->params[$name] : array();

            $this->makeElement($name, $attributes);
        }


        $this->makeFieldElement();
    }

    protected function makeFieldElement()
    {
        // input($type, $name, $value = null, $options = array())
        // textarea($name, $value = null, $options = array())
        // select($name, $list = array(), $selected = null, $options = array())
        // checkable($type, $name, $value, $checked, $options)
        // button($value = null, $options = array())
        // macro

        // input        $type, $name, $value,           $options
        // textarea            $name, $value,           $options
        // select              $name, $list, $selected, $options
        // checkable    $type, $name, $value, $checked, $options
        // button                     $value,           $options
        // macro

        $type = $this->type;
        $name = $this->getName();
        $value = null;
        //$options = $this->getFieldOptions();
        $options = array();
        //$value = null;
        $list = $this->getChoices();


        //$content[] = Form::text($this->getName(), null);

        switch ($type) {
            case 'select' :
                $content[] = $this->makeLabel();
                $content[] = Form::select($name, $list, $value, $options);
                break;

            case 'textarea' :
                $content[] = $this->makeLabel();
                $content[] = Form::textarea($name, $value, $options);
                break;

            case 'text' :
            case 'password' :
            case 'hidden' :
            case 'email' :
            case 'url' :
            case 'file' :
            case 'reset' :
            case 'image' :
            case 'submit' :
                $content[] = $this->makeLabel();
                $content[] = Form::input($type, $name, $value, $options);
                break;
        }

        $this->makeElement('field', array('content' => $content));
    }

    protected function makeLabel()
    {
        return Form::label($this->getName(), $this->getLabel());
    }

    public function makeElement($name, $attributes)
    {
        $content = null;
        if ( ! is_array($attributes) )
        {
            $content = $attributes;
        }
        else if ( array_key_exists('content', $attributes))
        {
            $content = $attributes['content'];
        } else {
            $content = $name;
        }

        $this->elements[$name] = new Element($content, $attributes);
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



    /**
     * Get not compiled component template content
     *
     * @param null $template
     * @return string
     */
    protected function getTemplateContent($template = null)
    {
        if ( is_null($template) ) {
            $template = $this->getTemplate();
        }

        $finder = View::getFinder();
        $filesystem = $finder->getFilesystem();

        return $filesystem->get($finder->find($template));
    }


    /**
     * Get every elements component names in template
     *
     * @param $template
     * @return array
     */
    protected function getElementNamesFromTemplate($template)
    {
        if( ! array_key_exists($template, self::$elementsName) )
        {
            //var_dump('RUN RUN RUN -> '.$template);
            $content = $this->getTemplateContent($template);

            // extract every $variable into {{ ... }} and $variable->attributes()
            preg_match_all('/{{\s*\$([A-Za-z0-9_]+?)(|\s*or .+?|->attributes.+?)\s*}}/s', $content, $matches);

            self::$elementsName[$template] = array_key_exists(1, $matches) ? array_unique($matches[1]) : array();
        }

        return self::$elementsName[$template];
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


    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    public function setLabel($label)
    {

        //if ($this->params['label'])

        //var_dump('$label', $label);


        if ( is_array($label) )
        {
            $this->params['label'] = array_merge($this->params['label'], $label);
        }
        else if ( is_string($label))
        {
            $this->params['label']['content'] = $label;
        }

        if ( ! array_key_exists('content', $this->params['label'])) {
            $this->params['label']['content'] = ucfirst($this->getName());
        }

        // todo : add required

        //$this->label = $label;
        return $this;
    }


    public function getLabel()
    {
        return $this->params['label']['content'];
    }



    /*
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
    */


    public function setChoices($choices)
    {
        $this->choices = $choices;
        return $this;
    }

    public function getChoices()
    {
        return $this->choices;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }


    public function getTemplate()
    {
        if ($this->template === null) {
            $this->template = array_get($this->params, 'template', 'form-builder::component');
        }

        if ( ! View::exists($this->template) )
        {
            throw new RuntimeException('Template' . $this->template . ' doesn\'t exist.');
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

        $elements = $this->getElements($template);

        //var_dump($elements);
        //return ' component <br />';
        return View::make($template, $elements);
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