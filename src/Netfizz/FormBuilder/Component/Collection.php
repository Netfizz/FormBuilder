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


        $min = $this->elementMin();
        for ($i = 0; $i < $min; $i++)
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

    protected function getCollectionItem()
    {
        $model = $this->getModel();
        $method = $this->getName();

        if ( ! method_exists($model, $method)) {
            return false;
        }

        // if this method return an eloquent Relationships class
        $relationObj = $model->$method();
        if ( ! is_subclass_of($relationObj, 'Illuminate\Database\Eloquent\Relations\Relation')) {
            return false;
        }

        $relatedModel = $relationObj->getRelated();

        var_dump($relationObj);
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

        if ($this->getModel()) {
            $element_min = 2;
        }

        if ($data = Input::old($this->name)) {
            $element_min = count($data);
        }

        return $element_min;
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