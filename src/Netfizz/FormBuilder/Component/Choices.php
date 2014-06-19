<?php namespace Netfizz\FormBuilder\Component;

use Illuminate\Support\Facades\Form as FormBuilder;
use Illuminate\Database\Eloquent\Collection;
use Netfizz\FormBuilder\Component;
use HTML, App, ArrayableInterface;

class Choices extends Field {

    protected $choices;

    protected $hasMany;     // has many values

    static $fetch = array();


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
        $list = $this->getChoices();
        $type = $this->getType();
        $value = $this->builder->getValueAttribute($this->getName(), $this->content);

        if ($value instanceof Collection) {
            $value = $value->modelKeys();

            //$this->getModel()->blocks;
            //var_dump($this->getModel()->blocks);
            //var_dump('object_get', $this->getModel(), object_get($this->getModel(), 'blocks'));
        }

        $options = $this->attributes();
        $labelAttributes = $this->attributes('label');

        //var_dump('$this->content', $this->content);
        //var_dump('$value', $value);



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
            case 'boolean' :
                $methodType = $type === 'radio' ? 'radio' : 'checkbox';

                if ($type === 'boolean')
                {
                    $content[] = FormBuilder::hidden($this->getName(), 0);
                }

                $content[] = sprintf('<label%s>%s %s</label>',
                    HTML::attributes($this->array_flatten($labelAttributes)),
                    FormBuilder::$methodType($this->getName(), $list, $value, $options),
                    $this->getLabel());

                $this->removeLabel();
                break;

            case 'radios' :
            case 'checkboxes' :
            case 'inline_radios' :
            case 'inline_checkboxes' :
                $content = null;
                $methodType = str_contains($type, 'radio') ? 'radio' : 'checkbox';

                if ($methodType === 'checkbox') $this->setHasMany();

                foreach((array) $list as $val => $label)
                {
                    $checked = $value == $val ? true : false;
                    $options['id'] = $this->getId() . ucfirst(camel_case($val));

                    $field = Component::$methodType($this->getName(), $val, $checked, $options)->setLabel($label);

                    $this->add($field);
                }
                break;

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
        $name = parent::getName();

        if ( $this->hasMany() && ! ends_with($name, '[]') )
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
            return self::getRelatedChoices($relationObj);
        }

        return array();
    }



    protected function isRelationshipProperty($name)
    {
        $model = $this->getModel();


        // TODO : add check if trait exist  $model->trait_exists('')
        if ($model === null) {
            return false;
        }

        // check if is a foreign key OR Multiple key []
        $attribute = preg_replace('/(?:_id|\[\])+$/', '', $name);

        return $model::isRelationshipProperty($attribute);
    }

    protected function getRelatedChoices($relationObj)
    {

        $relationType = class_basename(get_class($relationObj));

        if ($relationType === 'BelongsTo' && $relationObj->getForeignKey() != $this->name)
        {
            $this->setName($relationObj->getForeignKey());
        }

        $relatedModel = $relationObj->getRelated();
        $collection = self::fetchCollection($relatedModel);

        //$collection = $relatedModel::all();
        return $this->collectionToArray($collection);
    }

    public static function fetchCollection($relatedModel)
    {
        $key = class_basename(get_class($relatedModel));
        if (! array_key_exists($key, self::$fetch)) {
            self::$fetch[$key] = $relatedModel::all();
        }

        return self::$fetch[$key];
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