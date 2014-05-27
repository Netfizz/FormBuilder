<?php namespace Netfizz\FormBuilder\Component;

use Netfizz\FormBuilder\Component;
use Illuminate\Support\Facades\Form as FormBuilder;
use HTML;

class Field extends Component {


    protected function makeContent()
    {
        $type = $this->type;
        $name = $this->getName();
        //$value = $this->content;
        $value = $this->builder->getValueAttribute($this->getName(), $this->content);
        $options = $this->attributes();

        if ( ! array_key_exists('id', $options))
        {
            $options['id'] = $this->getId();
        }

        if ( array_key_exists('placeholder', $options))
        {
            if ($options['placeholder'] === false)
            {
                unset($options['placeholder']);
            }
            else if ($options['placeholder'] === true)
            {
                $options['placeholder'] = $this->getLabel();
            }
        }

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