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


    protected function makeContent()
    {
        $type = $this->type;
        $name = $this->name;
        $value = null;


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
        }


        return $content;
    }


    protected function makeLabel()
    {
        return FormBuilder::label($this->name, $this->name);
    }


    protected function getDatas() {

        //var_dump($this->attributes, array_get($this->config, 'attributes'));

        $datas = parent::getDatas();

        //var_dump($datas);

        return $datas;
    }
} 