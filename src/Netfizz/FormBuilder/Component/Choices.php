<?php namespace Netfizz\FormBuilder\Component;

//use Netfizz\FormBuilder\Component\Container;
use Illuminate\Support\Facades\Form as FormBuilder;
//use Illuminate\Html\FormBuilder;
use HTML, App;
use Netfizz\FormBuilder\Component;

class Choices extends Field {

    protected $choices;

    protected $isMultiple = false;


    public function __construct($type, $name, $choices = array(), $selected = null, $options = array())
    {
        $this->choices = $choices;

        parent::__construct($type, $name, $selected, $options);
    }


    protected function makeContent()
    {
        $list = $this->getChoices();

        $type = $this->type;
        $name = $this->getName();
        $value = $this->getFormService()->getValueAttribute($name, $this->content);
        $options = $this->getFlattenOptions();



        //var_dump($name, $list, $value, $options);
        var_dump($name, $options);

        switch ($type) {
            case 'select' :
                $content = FormBuilder::select($name, $list, $value, $options);
                break;

            case 'radio' :
            case 'checkbox' :
                $options = array_except($options, array('template'));
                $content = sprintf('<label>%s %s</label>', FormBuilder::$type($name, $list, $value, $options), $this->getLabelText());
                //$content = FormBuilder::$type($name, $list, $value, $options);
                break;

            case 'radios' :

                $content = array();
                foreach((array) $list as $val => $label) {
                    $checked = $value == $val ? true : false;
                    $options = array_merge($options, array('label' => $label));

                    //$content[] = sprintf('<label>%s %s</label>', FormBuilder::radio($name, $val, $checked, $options), $label);
                    $content[] = Container::radio($name, $val, $checked, $options);
                }

                $content = implode(PHP_EOL, $content);
                break;

            case 'checkboxes' :
                $content = array();
                foreach((array) $list as $val => $label) {
                    $checked = $value == $val ? true : false;
                    $options = array_merge($options, array('label' => $label));

                    $content[] = sprintf('<label>%s %s</label>', FormBuilder::checkbox($name, $val, $checked, $options), $label);
                    //$content[] = $label;
                }

                $content = implode(PHP_EOL, $content);
                break;


        }


        return $content;
    }

    protected function makeId()
    {
        $id = parent::makeId();

        $choices = $this->getChoices();
        if (is_string($choices)) {
            $id .= '.' . $choices;
        }

        $this->setId($id);

        return $id;
    }

    public function makeLabel()
    {
        return parent::makeLabel();
        //return null;
    }

    public function getName()
    {
        $name = $this->name;

        if (ends_with($this->name, '[]'))
        {
            $this->setMultiple();
        }
        else if (array_key_exists('multiple', $this->params) )
        {
            $name .= '[]';
        }

        return $name;
    }

    public function setMultiple()
    {
        $this->params = array_merge($this->params, array('multiple' => 'multiple'));
        return $this;
    }

    protected function setMultipleType()
    {
        $this->type = str_plural($this->type);
    }


    public function getChoices()
    {
        if ( is_array($this->choices) && count($this->choices) > 0 ) {
            $this->setMultipleType();
            return $this->choices;
        }

        if (is_string($this->choices)) {
            return $this->choices;
        }

        return $this->autoGenerateChoices();
    }


    protected function autoGenerateChoices()
    {
        //return range('a', 'f');

        // check if is a relation field
        if ($relationObj = $this->isRelationshipProperty($this->name))
        {
            //$this->setRelashionship($this->name, $relationObj, $params);

            return $this->getRelatedChoices($relationObj);
            //var_dump($relationObj);

            //return range('a', 'f');
        }

        return array();
        //return range(1, 10);
    }



    protected function isRelationshipProperty($name)
    {

        // check if is a foreign key OR Multiple key []
        $method = preg_replace('/(?:_id|\[\])+$/', '', $name);
        $model = $this->getModel();

        if ( ! method_exists($model, $method)) {
            return false;
        }

        // if this method return an eloquent Relationships class
        $relationObj = $model->$method();
        if (is_subclass_of($relationObj, 'Illuminate\Database\Eloquent\Relations\Relation')) {
            return $relationObj;
        }

        return false;
    }

    protected function getRelatedChoices($relationObj)
    {
        $relatedModel = $relationObj->getRelated();

        $relationType = class_basename(get_class($relationObj));

        $multipleRelationTypes = array(
            'BelongsToMany',
            'MorphMany'
        );

        if (in_array($relationType, $multipleRelationTypes)) {
            $this->setMultiple();
        }


        return $this->collectionToArray($relatedModel::all());
        //var_dump($relatedModel::all());
    }


    /*
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
                $params['choices'] = $this->collectionToArray($relatedModel::all());
                //$params['fromQuery'] = $relatedModel::all();
                break;

            case 'BelongsToMany' :
            case 'MorphMany' :
                //$params['type'] = 'multiselect';
                $params['type'] = 'select';
                $params['multiple'] = 'multiple';
                $params['choices'] = $this->collectionToArray($relatedModel::all());

                //$params['fromQuery'] = $relatedModel::all();

                //$params['name'] = $name . '[]';
                //$params['label'] = $name;
                //$params['multiple'] = true;
                break;

        }

        if ($this->model) {
            //$values = $this->model->getAttribute($name)->lists('id');

            //var_dump($name, $this->model->getAttribute($name));
        }
    }
    */

    protected function collectionToArray($collection, $value = null, $key = null)
    {
        $options = array();

        foreach($collection as $item) {
            // Calculate the value
            if ($value and isset($item->$value)) $modelValue = $item->$value;
            elseif (method_exists($item, '__toString')) $modelValue = $item->__toString();
            else $modelValue = null;

            // Calculate the key
            if ($key and isset($item->$key)) $modelKey = $item->$key;
            elseif (method_exists($item, 'getKey')) $modelKey = $item->getKey();
            elseif (isset($item->id)) $modelKey = $item->id;
            else $modelKey = $modelValue;

            // Skip if no text value found
            if (!$modelValue) continue;

            $options[$modelKey] = (string) $modelValue;
        }

        return $options;
    }

}