<?php namespace Netfizz\FormBuilder\Component;

use Illuminate\Support\Facades\Form as FormBuilder;
use Netfizz\FormBuilder\Component;
use HTML, App;

class Choices extends Field {

    protected $choices;

    protected $hasMany;     // has many values


    public function __construct($type, $name, $choices = array(), $selected = null, $options = array())
    {
        $this->choices = $choices;

        if (ends_with($this->name, '[]'))
        {
            $this->setHasMany();
        }

        parent::__construct($type, $name, $selected, $options);
    }


    protected function makeContent()
    {
        $content = null;
        $list = $this->getChoices();
        $type = $this->getType();
        $value = $this->builder->getValueAttribute($this->getName(), $this->content);
        $options = $this->attributes();
        $labelAttributes = $this->attributes('label');

        switch ($type) {
            case 'select' :
                if (array_key_exists('multiple', $this->config['component']) )
                {
                    $this->setHasMany();
                }

                if ( ! array_key_exists('id', $options))
                {
                    $options['id'] = $this->getId();
                }

                $content = FormBuilder::select($this->getName(), $list, $value, $options);
                break;

            case 'radio' :
            case 'checkbox' :
                $content = sprintf('<label%s>%s %s</label>',
                    HTML::attributes($this->array_flatten($labelAttributes)),
                    FormBuilder::$type($this->getName(), $list, $value, $options),
                    $this->getLabel());

                $this->removeLabel();
                break;

            case 'boolean' :
                $content[] = FormBuilder::hidden($this->getName(), 0);
                $content[] = sprintf('<label%s>%s %s</label>',
                    HTML::attributes($this->array_flatten($labelAttributes)),
                    FormBuilder::checkbox($this->getName(), $list, $value, $options),
                    $this->getLabel());

                $this->removeLabel();
                break;

            case 'radios' :
            case 'checkboxes' :
            case 'inline_radios' :
            case 'inline_checkboxes' :
                $labelAttributes = $this->attributes('component.label');
                $methodType = str_contains($type, 'radio') ? 'radio' : 'checkbox';

                if ($methodType === 'checkbox') $this->setHasMany();

                foreach((array) $list as $val => $label)
                {
                    $checked = $value == $val ? true : false;
                    $options['id'] = $this->getId() . ucfirst(camel_case($val));
                    unset($options['label']);

                    /*
                    $field = sprintf('<label%s>%s %s</label>',
                        HTML::attributes($this->array_flatten($labelAttributes)),
                        FormBuilder::$methodType($this->getName(), $val, $checked, $options),
                        $label);
                    */

                    $field = Component::$methodType($this->getName(), $val, $checked, $options)->setLabel($label);

                    $this->add($field);
                }
                break;
            /*
            case 'xxradios' :
            case 'xxcheckboxes' :
                $methodType = $type === 'radios' ? 'radio' : 'checkbox';
                if ($type === 'checkboxes') $this->setHasMany();

                foreach((array) $list as $val => $label)
                {
                    $checked = $value == $val ? true : false;
                    $options['component']['id'] = $this->getId() . ucfirst(camel_case($val));

                    $content[] = Component::$methodType($this->getName(), $val, $checked, $options)->setLabel($label);
                }
                break;
            */

            default:
                $content = null;

        }

        return is_array($content) ? implode(PHP_EOL, $content) : $content;
    }


    protected function makeId()
    {
        $id = parent::makeId();

        $choices = $this->getChoices();
        if (is_string($choices)) {
            $id .= ucfirst(camel_case($choices));
        }

        $this->setId($id);

        return $id;
    }


    public function getName()
    {
        $name = $this->name;

        if ( $this->hasMany() )
        {
            $name .= '[]';
        }

        return $name;
    }


    public function setHasMany($status = true)
    {
        $this->hasMany = $status;

        return $this;
    }


    public function hasMany()
    {
        return $this->hasMany;
    }


    public function getChoices()
    {
        if ( is_array($this->choices) && count($this->choices) > 0 )
        {
            return $this->choices;
        }

        if (is_string($this->choices))
        {
            return $this->choices;
        }

        return $this->autoGenerateChoices();
    }


    protected function autoGenerateChoices()
    {
        $this->choices = array();

        // check if is a relation field
        if ($relationObj = $this->isRelationshipProperty($this->name))
        {
            return $this->getRelatedChoices($relationObj);
        }

        return array();
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
            //$this->setHasManyValues();
        }


        return $this->collectionToArray($relatedModel::all());
        //var_dump($relatedModel::all());
    }


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