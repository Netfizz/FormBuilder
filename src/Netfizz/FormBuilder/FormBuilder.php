<?php namespace Netfizz\FormBuilder;

use Former;
use Former\Form\Fields\Button;
use Illuminate\Database\Eloquent\Model;
use Str;
use Illuminate\Validation\Validator;
use Former\FormerServiceProvider;
use Netfizz\Admin\Traits\ManageConfig;


use View, Config, RuntimeException;

class FormBuilder {

    use ManageConfig;

    protected $model;

    protected $validator;

    protected $method = 'put';


    protected $tableInfo;

    protected $form;

    protected $fields;

    protected $formElements;

    protected $config;


    public function __construct($config = null, $model = null, $validator = null)
    {
        $this->setModel($model);
        $this->initConfig($config);
        $this->setValidator($validator);

        $this->form = new \stdClass;

        //$this->tableInfo = $this->getTableInfo($this->model);

    }

    /**
     * @param $config
     */
    protected function initConfig($config)
    {

        if ( ! is_array($config)) {
            $config = array();
        }

        // merge default package config and  config
        $mergedConfig = array_merge(Config::get('form-builder::config'), $config);
        Config::set($this->getConfigKey(), $mergedConfig);

        $this->setConfigFields();

        //var_dump($this->getConfig());
    }


    /**
     * Transform array keys to field name
     * @param array $array
     * @return array
     */
    protected function uniformizeArray($array)
    {

        if ( ! is_array($array))
        {
            return array();
        }

        $newArray = array();

        foreach($array as $key => $value)
        {
            if (is_int($key) && is_string($value))
            {
                $newArray[$value] = null;
            } else {
                $newArray[$key] = $value;
            }
        }

        return $newArray;
    }


    public function setConfigFields()
    {
        // recupère les champs de la config
        $fields = $this->getConfig('fields');

        // si vide, on appel les champs du model (sans les exceptions)
        if (empty($fields) && $this->getModel())
        {
            $fields = $this->getModelAttributes();
        }

        $fields = $this->uniformizeArray($fields);


        var_dump($fields);

        // on parcours les champs pour initialiser les parametres
        foreach($fields as $name => &$params) {
            $params = $this->uniformizeArray($params);

            $this->setElementType($name, $params);

        }

        //die;


        $this->setConfig($fields, 'fields');
    }


    protected function setElementType($name, &$params)
    {
        if ( ! isset($params['type']))
        {
            // check if is a relation field
            if ($relationObj = $this->isRelationshipProperty($name))
            {
                //$this->setBelongsToRelashionship($relationName, $name, $params);
                //$params['type'] = null;

                $this->setRelashionship($name, $relationObj, $params);
                //var_dump($name, $relationObj);

            } else {
                $params['type'] = $this->getInputTypeFromTableInfo($name);

            }

        }
    }

    protected function isRelationshipProperty($name)
    {
        // check if is a foreign key
        $isForeignKey = strstr($name, '_id', true);

        //var_dump($name, $this->model->getAttribute($name));

        // check if method exist
        $method = $isForeignKey ?: $name;
        if ( ! method_exists($this->model, $method)) {
            return false;
        }

        // if this method return an eloquent Relationships class
        $relationObj = $this->model->$method();
        if (is_subclass_of($relationObj, 'Illuminate\Database\Eloquent\Relations\Relation')) {
            return $relationObj;
        }

        return false;
    }

    protected function setRelashionship($name, $relationObj, &$params)
    {
        $relatedModel = $relationObj->getRelated();

        $relationType = class_basename(get_class($relationObj));

        //$params['type'] = 'select';
        //$params['fromQuery'] = $relatedModel::all();

        //var_dump($relationType);

        switch($relationType) {
            case 'BelongsTo' :
                $params['type'] = 'select';
                $params['fromQuery'] = $relatedModel::all();
                break;

            case 'BelongsToMany' :
            case 'MorphMany' :
                $params['type'] = 'multiselect';
                $params['fromQuery'] = $relatedModel::all();

                //$params['name'] = $name . '[]';
                //$params['label'] = $name;
                //$params['multiple'] = true;
                break;

        }

        if ($this->model) {
            //$values = $this->model->getAttribute($name)->lists('id');

            //var_dump($name, $this->model->getAttribute($name));
        }

        /*
        if ($relationType === 'BelongsTo') {
            $params['type'] = 'radios';
            $choices = $relatedModel::all(array('id', 'name'))->toArray();

            $choices = array(
                'label' => array('name' => 'foo', 'value' => 'bar', 'data-foo' => 'bar'),
                'label2' => array('name' => 'foo', 'value' => 'bar2', 'data-foo' => 'bar2'),
            );
            $params['inline'] = true;

            //var_dump($choices);
           // $params['choices'] = $choices;
            $params['radios'] = $choices;
        }
        if ($relationType === 'BelongsToMany') {
            $params['type'] = 'multiselect';

            //$params['name'] = $name . '[]';
            //$params['label'] = $name;
            //$params['multiple'] = true;
        }
        */

        /*
        $params['type'] = 'text';
        $params['useDatalist'] = $relatedModel::all();
        */


        //$params['label'] = $relationName;
    }

    /**
     * Calculate correct Formbuilder method
     *
     * @param  string $name
     * @return string
     */
    protected function getInputTypeFromTableInfo($name)
    {

        //var_dump($name, $this->getTableInfo($name));

        // check if is a model property
        if ( ! $column = $this->getTableInfo($name))
        {
            return null;
        }

        $dataType = $column->getType()->getName();

        $lookup = array(
            'string'  => 'text',
            'float'   => 'text',
            'date'    => 'text',
            'text'    => 'textarea',
            'boolean' => 'checkbox'
        );

        return array_key_exists($dataType, $lookup)
            ? $lookup[$dataType]
            : 'text';
    }



    protected function getRelationField($name)
    {
        if ($relationName = strstr($name, '_id', true))
        {
            /*
            //$rel = $this->model->load($relationName);

            if (is_string($relationName)) $relationName = (array) $relationName;

            $query = $this->model->newQuery()->with($relationName);

            var_dump($relationName, $query->getModel());
            */


            /*
            //test = $this->model->getModels();
            list(, $caller) = debug_backtrace(false);

            $relation = $caller['function'];

            var_dump($relation, $caller);
            */

            if (method_exists($this->model, $relationName))
            {
                $relationClass = $this->model->$relationName();

                $relatedModel = $relationClass->getRelated();

                $test = Former::select('foo')->fromQuery($relatedModel::all());
                //echo $test;

                //var_dump($relationClass->getRelated());
            }
            //$test = $this->model->user();

        }



    }


    protected function getModelAttributes()
    {
        $names = array_keys($this->getTableInfo());

        $except_fields = Config::get($this->getConfigKey('extras_params.except_fields'), array());

        return array_diff($names, $except_fields);
    }



    public function getTableInfo($column = null)
    {
        if ($this->tableInfo === null)
        {
            $this->setTableInfo();
        }

        if ($column) {
            return array_key_exists($column, $this->tableInfo) ? $this->tableInfo[$column] : null;
        }

        return $this->tableInfo;
    }


    public function setTableInfo()
    {
        // check if model exist
        $model = $this->getModel();
        if ($model == null)
        {
            return array();
        }

        $this->tableInfo = \DB::getDoctrineSchemaManager()
            ->listTableDetails($model->getTable())
            ->getColumns();
    }


    /*
    protected function generateConfigFieldsFromModel($model)
    {
        if ($model === null)
        {
            return array();
        }

        $this->tableInfo = $this->getTableInfo($this->model);

    }
    */



    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function getForm($view = null)
    {
        $form = new \stdClass;

        $form->open = $this->getFormOpen();

        $form->elements = $this->getFormElements();

        $form->buttons = $this->getFormbuttons();

        $form->close = $this->getFormClose();

        if(is_null($view)) {
            $view = 'form-builder::form';
        }

        return View::make($view)->withForm($form);
    }


    public function populate($item)
    {
        //var_dump( $item->countries->lists('id'));
        //var_dump($item->getAttribute('countries')->lists('id'));
        //die;

        //$countries = $item->getAttribute('countries')->lists('id');

        Former::populate($item);

        // todo : check if is a model or an array
        if ($item instanceof Model) {
            var_dump('instance of');
            //Former::populateField('countries', $item->getAttribute('countries')->lists('id'));
            //Former::populateField('blocks', $item->getAttribute('blocks')->lists('id'));
        }

    }



    /**
     * Generate form open string
     * @param  string $method
     * @param  string $model
     * @return string
     */
    protected function getFormOpen()
    {
        // instantiate form
        $form = Former::open();

        // retrieve every params except fields
        $params = array_except($this->getConfig(), array('fields', 'extras_params'));

        // chain every methods with config params to form
        $this->executeMethods($form, $params);

        return $form->__toString();
    }


    /**
     * @param $obj
     * @param array $methods
     */
    protected function executeMethods(&$obj, array $methods)
    {
        //$methods = array_except($methods, array('type'));
        //var_dump($obj);
        //if (is_subclass_of($obj, 'Field')) {
            foreach($methods as $method => $value)
            {
                if (is_string($method))
                {
                    $obj->$method($value);
                } else {
                    $obj->$value();
                }

            }

        //}

     }

    protected function getFormClose()
    {
        return Former::close();
    }


    protected function getFormButtons()
    {
        return  Former::actions()
            ->large_primary_submit('Submit')
            ->large_inverse_reset('Reset')
            ->__toString();
    }




    /**
     * Dynamically create form elements
     *
     * @param  string $type
     * @param  string $element
     * @return string
     */
    protected function getFormElements()
    {
        $elements = array();
        $attributes = $this->getConfig('fields');

        foreach($attributes as $name => $params)
        {
            $elements[$name] = (string) $this->setElement($name);
        }

        return $elements;
    }

    protected function setElement($name, $params = null)
    {
        if ($params === null) {
            $params = $this->getConfig('fields.'.$name);
        }

        if ( ! array_key_exists('type', $params)) {
            throw new RuntimeException('No type defined for ' . $name . ' field.');
        }

        if ($params['type'] === null) {
            return null;
        }

        $element = Former::$params['type']($name);
        $this->executeMethods($element, $params);

        return $element;
    }

} 