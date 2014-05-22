<?php namespace Netfizz\FormBuilder\Component;

use Netfizz\FormBuilder\Component;
use RuntimeException;

class Collection extends Component {


    protected function makeContent()
    {
        if (class_basename(get_class($this->content)) !== 'Formizz') {
            throw new RuntimeException('Collection is not a Formizz object');
        };

        $max = $this->getMaxElements();
        for ($i = 1; $i <= $max; $i++)
        {
            $embedForm = clone $this->content->embed($this->getName(), $i);
            $this->add($embedForm);
        }

        return null;
    }


    public function getMaxElements()
    {
        if ( ! is_numeric($this->config['max_element']) )
        {
            throw new RuntimeException('Max Element params must be a integer');
        }

        return (int) $this->config['max_element'];
    }

} 