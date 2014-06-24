<?php namespace Netfizz\FormBuilder\Component;

use Netfizz\FormBuilder\Component;
use HTML, Input, stdClass, RuntimeException;

class Collection extends Component {

    protected $prototype;

    static $ValuesCollection;

    protected function makeContent()
    {
        if (class_basename(get_class($this->content)) !== 'Formizz') {
            throw new RuntimeException('Collection is not a Formizz object');
        }


        if ($delete = $this->elementDelete())
        {
            $this->content->add('<div class="text-right"><a href="#" class="collection-delete-row">' . $delete . '</a></div>');
        }


        $this->autoAddCollectionPrimaryKeyField();

        $min = $this->elementMin();
        for ($i = 0; $i <= $min; $i++)
        {
            $embedForm = clone $this->content->embed($this->getName(), $i);
            $this->add($embedForm);
        }


        if ($this->elementAdd())
        {
            $prototype = clone $this->content->embed($this->getName(), '__DELTA__')->resetMessages();
            $this->setPrototype((string) $prototype);
        }

        return null;
    }


    protected function autoAddCollectionPrimaryKeyField()
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

        $this->content->add(Component::hidden($keyName));
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
        if (! $this->isRelationshipProperty())
        {
            return false;
        }

        $builder = $this->getBuilder();
        return $builder::getRelationCollection($this->getName(), $this->getModel());
    }


    protected function isRelationshipProperty()
    {
        $model = $this->getModel();

        // TODO : add check if trait exist  $model->trait_exists('')
        if ($model === null) {
            return false;
        }

        $attribute = $this->getName();

        return $model::isRelationshipProperty($attribute);
    }


    public function setPrototype($prototype)
    {
        $this->prototype = $prototype;

        return $this;
    }


    protected function makeCollection()
    {
        $collection = new stdClass;

        $collection->attributes = $this->getCollectionAttributes();
        $collection->add = $this->elementAdd();
        $collection->delete = $this->elementDelete();
        $collection->sorting = $this->elementSorting();
        $collection->min = $this->elementMin();
        $collection->max = $this->elementMax();


        return $collection;
    }

    protected function getCollectionAttributes()
    {
        $class = array('collection-component');
        if ($this->isSortable()) {
            $class[] = 'collection-sortable';
        }

        $attributes = array(
            'class' => implode(' ', $class),
            'data-prototype' => $this->prototype
        );

        return HTML::attributes($attributes);
    }


    protected function isSortable()
    {
        if ($relationObj = $this->isRelationshipProperty())
        {
            $related = $relationObj->getRelated();

            foreach (class_uses($related) as $trait)
            {
                if (class_basename($trait) === 'ModelSortableTrait') return true;
            }
        }

        return false;
    }

    protected function getDatas()
    {
        return array_merge(parent::getDatas(), array(
            'id' => $this->getId(),
            'collection' => $this->makeCollection(),
            'sortable' => $this->isSortable()
        ));
    }
} 