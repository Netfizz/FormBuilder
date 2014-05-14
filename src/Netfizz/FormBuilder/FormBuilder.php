<?php namespace Netfizz\FormBuilder;

use Illuminate\Html\FormBuilder as DefaultFormBuilder;
use Illuminate\Database\Eloquent\Collection;
use Config;

class FormBuilder extends DefaultFormBuilder {

    protected $formId;

    protected $config = 'form-builder::config';


    public function setConfig($config)
    {
        $this->config = $config;
    }


    public function getConfig()
    {
        return $this->config;
    }

    public function getComponentConfigFilename()
    {
        return Config::get($this->getConfig() . '.component', array());
    }

    /**
     * Get model instance
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }


    /**
     * Get the model value that should be assigned to the field.
     *
     * @param  string  $name
     * @return string
     */
    protected function getModelValueAttribute($name)
    {

        if (is_object($this->model))
        {
            $value = object_get($this->model, $this->transformKey($name));

            if ($value instanceof Collection) {
                return $value->modelKeys();
            }

            return $value;
        }
        elseif (is_array($this->model))
        {
            return array_get($this->model, $this->transformKey($name));
        }
    }


    public function getFormId()
    {
        return $this->formId;
    }

    public function setFormId($formId)
    {
        $this->formId = $formId;
    }
}
