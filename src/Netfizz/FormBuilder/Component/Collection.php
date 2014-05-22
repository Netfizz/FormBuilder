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

        if ($max = $this->getMaxElements())
        {
            for ($i = 0; $i < $max; $i++)
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

    public function setPrototype($prototype)
    {
        $this->prototype = $prototype;

        return $this;
    }

    protected function makeCollection()
    {
        $collection = new stdClass;
        $collection->prototype = HTML::attributes(array('data-prototype' => $this->prototype));

        return $collection;
    }


    public function getMaxElements()
    {
        if ( ! is_numeric($this->config['max_element']) )
        {
            throw new RuntimeException('Max Element params must be a integer');
        }

        return (int) $this->config['max_element'];
    }

    protected function getDatas()
    {
        return array_merge(parent::getDatas(), array(
            'id' => $this->getId(),
            'collection' => $this->makeCollection()
        ));
    }
} 