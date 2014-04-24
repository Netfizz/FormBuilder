<?php namespace Netfizz\FormBuilder\Component;

//use Netfizz\FormBuilder\Component\Container;
use Illuminate\Support\Facades\Form as FormBuilder;
//use Illuminate\Html\FormBuilder;
use HTML, App;

class Choices extends Field {

    protected $choices;


    public function __construct($type, $name, $choices = array(), $selected = null, $options = array())
    {
        $this->choices = $choices;

        parent::__construct($type, $name, $selected, $options);
    }


    protected function makeContent()
    {
        $type = $this->type;
        $name = $this->name;
        $value = $this->content;
        $options = $this->getFlattenOptions();
        $list = $this->getChoices();

        //var_dump($name, $list, $value, $options);

        switch ($type) {
            case 'select' :
                $content = FormBuilder::select($name, $list, $value, $options);
                //$content = 'select';
                break;
        }


        return $content;
    }


    public function getChoices()
    {
        if ( is_array($this->choices) && count($this->choices) > 0 ) {
            return $this->choices;
        }
        //return range(5, 10);
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

        return range(1, 10);
    }



    protected function isRelationshipProperty($name)
    {
        // check if is a foreign key
        $isForeignKey = strstr($name, '_id', true);

        //var_dump($isForeignKey, $this->getFormService());
        //var_dump($isForeignKey, $this->getModel());


        $model = $this->getModel();


        // check if method exist
        $method = $isForeignKey ?: $name;
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

        return $this->collectionToArray($relatedModel::all());
        //var_dump($relatedModel::all());
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