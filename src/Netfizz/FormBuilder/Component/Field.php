<?php namespace Netfizz\FormBuilder\Component;

//use Netfizz\FormBuilder\Component\Container;
use Illuminate\Support\Facades\Form as FormBuilder;
//use Illuminate\Html\FormBuilder;


class Field extends Container {

    protected $type;

    protected $name;

    protected $value;

    protected $options;


    public function __construct($type, $name, $value = null, $options = array())
    {
        /*
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->options = $options;
        */
        //$this->type = $type;
        $this->options = $options;

        parent::__construct($type, $name, $value, $options);
    }

    public function getValidator() {
        return parent::getValidator();
    }



    protected function makeContent()
    {
        $type = $this->type;
        $name = $this->name;
        //$value = null;
        $value = $this->value;


        $options = $this->getOptions();
        foreach($options as &$option) {
            if (is_array($option)) {
                $option = implode(' ', $option);
            }
        }

        $list = array();

        switch ($type) {
            case 'select' :
                $content = FormBuilder::select($name, $list, $value, $options);
                break;

            case 'textarea' :
                //$content = FormBuilder::textarea($name, $value, $options);
                $content = FormBuilder::textarea($name, $value, $options);
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
                $content = FormBuilder::input($type, $name, $value, $options);
                break;

            case 'button':
                $content = FormBuilder::button($name, $options);
                break;

        }


        return $content;
    }


    protected function makeLabel()
    {
        //var_dump(array_get($this->config, 'label'));

        $label = null;
        $options = array_get($this->config, 'label');

        return FormBuilder::label($this->name, $label, $options);
    }


    protected function getDatas() {

        //var_dump($this->attributes, array_get($this->config, 'attributes'));

        $datas = parent::getDatas();

        //var_dump($datas);

        return $datas;
    }
} 