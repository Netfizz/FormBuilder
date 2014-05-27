<?php namespace Netfizz\FormBuilder;

use Illuminate\Html\FormBuilder as DefaultFormBuilder;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Collection;
use Config, RuntimeException;

class FormBuilder extends DefaultFormBuilder {

    protected $formId;

    protected $config;

    /**
     * @param HtmlBuilder $html
     * @param UrlGenerator $url
     * @param string $csrfToken
     */
    public function __construct(HtmlBuilder $html, UrlGenerator $url, $csrfToken)
    {
        parent::__construct($html, $url, $csrfToken);

        // Define default framework components config
        $framework = Config::get('form-builder::framework', 'none');
        $this->setFramework($framework);
    }

    /**
     * @param $framework
     * @return $this
     * @throws RuntimeException
     */
    public function setFramework($framework)
    {
        $component = Config::get('form-builder::frameworks.' . $framework, null);

        if ( is_null($component)) {
            throw new RuntimeException( 'Framework "' . ucfirst($framework) . '" component config file path doesn\'t exist.');
        }

        $config = Config::get($component, null);
        if ( is_null($component)) {
            throw new RuntimeException( 'Framework "' . ucfirst($framework) . '" component config file doesn\'t exist.');
        }

        $this->setConfig($config);
    }


    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
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
     * @return mixed
     */
    public function getFormId()
    {
        return $this->formId;
    }


    /**
     * @param $formId
     */
    public function setFormId($formId)
    {
        $this->formId = $formId;
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

            $value = $this->object_get($this->model, $this->transformKey($name));

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


    protected function object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') return $object;

        foreach (explode('.', $key) as $segment)
        {

            if ( ! is_object($object) || ! isset($object[$segment]))
            {
                return value($default);
            }

            $object = $object[$segment];

        }

        return $object;

    }
}
