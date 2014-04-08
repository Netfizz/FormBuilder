<?php namespace Netfizz\FormBuilder\Component;

use Netfizz\Core\Traits\Attributes;

class Element {

    use Attributes;

    /**
    /**
     * @var element attributes
     */
    protected $attributes;


    /**
     * @var content parts of the component
     */
    protected $contents = array();



    public function __construct($contents = null, $attributes = array())
    {
        $this->setContents($contents);
        $this->setAttributes($attributes);
    }


    public function render()
    {
        if ( ! is_array($this->contents) || empty($this->contents) ) {
            return null;
        }

        return implode(PHP_EOL, $this->contents);
    }


    public function __toString()
    {
        return $this->render();
    }


    public function setContents($contents)
    {
        if ( ! is_array($contents)) {
            $contents = array($contents);
        }

        $this->contents = $contents;
        return $this;
    }


    public function getContents()
    {
        return $this->contents;
    }


    public function addContent($content)
    {
        $this->contents[] = $content;
        return $this;
    }


}