<?php namespace Netfizz\FormBuilder\Component;

use Netfizz\FormBuilder\Component;
use HTML, stdClass, RuntimeException;

class Collection extends Component {

    protected $prototype;

    protected function makeContent()
    {
        if (class_basename(get_class($this->content)) !== 'Formizz') {
            throw new RuntimeException('Collection is not a Formizz object');
        };

        if ($min = $this->getMinElements())
        {
            for ($i = 0; $i < $min; $i++)
            {
                $embedForm = clone $this->content->embed($this->getName(), $i);
                $this->add($embedForm);
            }
        }
        else
        {
            $embedForm = clone $this->content->embed($this->getName());
            $this->add($embedForm);
        }

        if ($this->allowAdd())
        {
            $prototype = clone $this->content->embed($this->getName(), '__DELTA__');
            $this->setPrototype((string) $prototype);
        }

        //var_dump($this->config);

        return null;
    }

    public function allowAdd()
    {
        return array_get($this->config, 'allow_add', false);
    }

    public function allowDelete()
    {
        return array_get($this->config, 'allow_delete', false);
    }

    public function allowSorting()
    {
        return array_get($this->config, 'allow_sorting', false);
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
        $collection->add = $this->allowAdd();
        $collection->delete = $this->allowDelete();
        $collection->sorting = $this->allowSorting();
        $collection->min = $this->getMinElements();
        $collection->max = $this->getMaxElements();


        return $collection;
    }


    public function getMaxElements()
    {
        return (int) array_get($this->config, 'max_element', 0);
    }

    public function getMinElements()
    {
        return (int) array_get($this->config, 'min_element', 1);
    }

    protected function getDatas()
    {
        return array_merge(parent::getDatas(), array(
            'id' => $this->getId(),
            'collection' => $this->makeCollection()
        ));
    }
} 