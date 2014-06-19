<?php namespace Netfizz\FormBuilder;

use Illuminate\Html\FormBuilder as DefaultFormBuilder;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Collection;
use Config, RuntimeException;

class FormBuilder extends DefaultFormBuilder {

    protected $formId;

    protected $config;

    static $relationsCollection = array();

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
        //var_dump('getModel()', class_basename($this->model));
        return $this->model;
    }

    //public function getRelation


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
        $name = $this->transformKey($name);

        if (is_object($this->model))
        {
            $value = object_get($this->model, $name);

            if (is_null($value))
            {
                $value = $this->getRelationModelValueAttribute($name);
            }

            return $value;
        }
        elseif (is_array($this->model))
        {
            return array_get($this->model, $name);
        }
    }

    public function getRelationModelValueAttribute($name)
    {
        $params = explode('.', $name);
        $property = array_shift($params);

        if ( ! $this->isRelationshipProperty($property))
        {
            return null;
        }

        $collection = self::getRelationCollection($property, $this->model);

        if (empty($params))
        {
            return $collection->modelKeys();
        }

        return array_get($collection->toArray(), implode('.', $params), null);
    }


    public static function getRelationCollection($property, $model)
    {
        if (! array_key_exists($property, self::$relationsCollection)) {
            $collection = array();
            if ($model->isRelationshipProperty($property))
            {
                $collection = $model->$property;
            }

            self::$relationsCollection[$property] = $collection;
        }

        return self::$relationsCollection[$property];
    }


    public function isRelationshipProperty($property, $model = null)
    {
        if ($model === null)
        {
            $model = $this->model;
        }

        // TODO : add check if trait exist  $model->trait_exists('')
        return $model::isRelationshipProperty($property);
    }

}
