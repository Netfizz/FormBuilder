<?php namespace Netfizz\FormBuilder\Component;

//use ClassesWithParents\A;
use Netfizz\FormBuilder\Component;
use HTML, stdClass;


class Tabs extends Component {

    protected $tab;

    protected function makeWrapperAttributes()
    {
        $attributes = $this->attributes ?: array_get($this->config, 'wrapper');

        if ( ! is_array($attributes)) {
            return null;
        }

        foreach($attributes as &$value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
        }


        if ( ! array_key_exists('id', $attributes))
        {
            $attributes['id'] = $this->getId();
        }


        return HTML::attributes($attributes);
    }

    protected function makeTabs()
    {
        $tabs = array();

        foreach($this->getElements() as $element)
        {
            $tab = new stdClass;
            $tab->id = $element->getId();
            $tab->label = $element->getLabel();

            if (! $tab->label) {
                $tab->label = $element->autoGenerateLabel();
            }

            $tabs[] = $tab;
        }

        return $tabs;
    }


    protected function getDatas() {
        return array_merge(parent::getDatas(), array(
            'id' => $this->getId(),
            'tabs' => $this->makeTabs()
        ));
    }

} 