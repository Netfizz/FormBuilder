<?php namespace Netfizz\FormBuilder\Component;

//use Netfizz\FormBuilder\Component\Container;
use Illuminate\Support\Facades\Form as FormBuilder;
//use Illuminate\Html\FormBuilder;
use HTML;

class Field extends Container {


    protected function makeContent()
    {
        $type = $this->type;
        $name = $this->name;
        $value = $this->content;
        $options = $this->getFlattenOptions();

        switch ($type) {
            case 'text' :
            case 'password' :
            case 'hidden' :
            case 'email' :
            case 'url' :
            case 'file' :
            case 'reset' :
            case 'image' :
                $content = FormBuilder::input($type, $name, $value, $options);
                break;

            case 'textarea' :
                $content = FormBuilder::textarea($name, $value, $options);
                break;

            case 'submit' :
                $content = FormBuilder::input($type, $name, $value, $options);
                $this->removeLabel();
                break;

            case 'button':
                $content = FormBuilder::button($name, $options);
                $this->removeLabel();
                break;

        }

        return $content;
    }


    protected function makeLabel()
    {
        $label = $this->getLabel();
        if ($label == null) {
            return null;
        }

        if ($this->isRequired()) {
            $label .= array_get($this->config, 'require.text');
        }

        $options = array_get($this->config, 'label');
        if (is_array($options) && array_key_exists('label', $options))
        {
            unset($options['label']);
        }

        return HTML::decode(FormBuilder::label($this->getId(), $label, $options));
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

} 