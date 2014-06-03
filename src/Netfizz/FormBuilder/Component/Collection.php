<?php namespace Netfizz\FormBuilder\Component;

use Netfizz\FormBuilder\Component;
use HTML, Input, stdClass, RuntimeException;

class Collection extends Component {

    protected $prototype;

    protected function makeContent()
    {
        if (class_basename(get_class($this->content)) !== 'Formizz') {
            throw new RuntimeException('Collection is not a Formizz object');
        }



        if ($delete = $this->elementDelete())
        {
            $this->content->add('<div class="text-right"><a href="#" class="collection-delete-row">' . $delete . '</a></div>');
        }



        //$this->content->add(Component::text('id'));
        $this->autoAddCollectionKeyField();

        $min = $this->elementMin();
        for ($i = 0; $i < $min; $i++)
        {
            $embedForm = clone $this->content->embed($this->getName(), $i);
            //var_dump($this->getEmbedName('id'));
            //$embedForm->add(Component::text($this->getEmbedName('id')));
            $this->add($embedForm);
        }


        if ($this->elementAdd())
        {
            $prototype = clone $this->content->embed($this->getName(), '__DELTA__')->resetMessages();
            $this->setPrototype((string) $prototype);
        }

        return null;
    }


    protected function autoAddCollectionKeyField()
    {
        if (! $relationObj = $this->isRelationshipProperty())
        {
            return false;
        }

        $keyName = $relationObj->getRelated()->getKeyName();
        foreach($this->content->getElements() as $element) {
            if (is_subclass_of($element, 'Netfizz\FormBuilder\Component')
                && $element->getName() == $keyName) {
                return false;
            }
        }

        $this->content->add(Component::text($keyName));
    }


    public function elementAdd()
    {
        return array_get($this->config, 'element_add', false);
    }


    public function elementDelete()
    {
        return array_get($this->config, 'element_del', false);
    }


    public function elementSorting()
    {
        return array_get($this->config, 'element_sort', false);
    }


    public function elementMax()
    {
        return (int) array_get($this->config, 'element_max', 0);
    }


    public function elementMin()
    {
        $element_min = (int) array_get($this->config, 'element_min', 1);

        if ($items = $this->getItems()) {
            $element_min = count($items);
        }

        if ($data = Input::old($this->name)) {
            $element_min = count($data);
        }

        return $element_min;
    }


    public function getItems()
    {
        if (! $relationObj = $this->isRelationshipProperty())
        {
            return false;
        }


        return $relationObj->getResults();
    }


    protected function isRelationshipProperty()
    {
        $attribute = $this->getName();
        $model = $this->getModel();
        if ( ! method_exists($model, $attribute)) {
            return false;
        }

        // if this method return an eloquent Relationships class
        $relationObj = $model->$attribute();
        if (is_subclass_of($relationObj, 'Illuminate\Database\Eloquent\Relations\Relation')) {
            return $relationObj;
        }

        return false;
    }


    public function setPrototype($prototype)
    {
        $this->prototype = $prototype;

        return $this;
    }


    protected function makeCollection()
    {
        $collection = new stdClass;

        $collection->prototype = HTML::attributes(array('data-prototype' => $this->prototype));
        $collection->add = $this->elementAdd();
        $collection->delete = $this->elementDelete();
        $collection->sorting = $this->elementSorting();
        $collection->min = $this->elementMin();
        $collection->max = $this->elementMax();

        return $collection;
    }


    protected function getDatas()
    {
        return array_merge(parent::getDatas(), array(
            'id' => $this->getId(),
            'collection' => $this->makeCollection()
        ));
    }
} 