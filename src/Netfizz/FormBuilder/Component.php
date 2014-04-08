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

    protected $attributes;

    protected $choices;

    protected $label;

    protected $elements;    // field, append, prepend, label

    protected static $elementsName = array();

    protected $template;

    protected $params;

    protected $view;

    public function __construct($name, $type = 'text', $params = array()) //, $value = null, $attributes = null, $choices = null)
    {

        $this->view = App::make('view');
        //var_dump($this->view);
        //die;

        $this->setConfig(Config::get('form-builder::default', array()))
            ->setName($name)
            ->setType($type)
            ->setParams($params);
            //->setValue($value)
            //->setAttributes($attributes)
            //->setChoices($choices);
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

    protected function setDefaultParams()
    {
        $all = array_get($this->config, 'default.*', array());

        $type = array_get($this->config, 'default.' . $this->type, array());

        $default = array_merge($all, $type);

        $this->params = $default;

        return $this;
    }




    public function setParams(array $params)
    {
        // set default params for this component type if is not set OR if type change
        if ( is_null($this->params) || ( isset($params['type']) && $params['type'] != $this->type ))
        {
            $this->setDefaultParams();
        }

        // merge params with default params
        $this->params = array_merge($this->params, $params);

        // todo : a supprimer,
        //$template = $this->getTemplate();
        //$this->getElements($template);

        return $this;
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

    /*
    protected function makeDefaultElements()
    {
        $exceptions = array_get($this->config, 'excepts_elements', array());
        $elements = array_except($this->getAttributes(), $exceptions);

        //var_dump($elements);
        //return true;
        //die;

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
    */

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
        foreach ($elementNames as $name)
        {
            $attributes = array_key_exists($name, $this->params) ? $this->params[$name] : array();
            $this->makeElement($name, $attributes);

            //var_dump($name, $attributes);
        }
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
            $template = self::getTemplate();
        }

        $finder = $this->view->getFinder();
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
            $this->template = array_get($this->params, 'template', 'form-builder::component');
        }

        if ( ! $this->view->exists($this->template) )
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
        return $this->view->make($template, $elements);
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