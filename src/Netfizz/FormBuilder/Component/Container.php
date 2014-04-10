<?php namespace Netfizz\FormBuilder\Component;

use Illuminate\Support\Contracts\RenderableInterface as Renderable;
use View;

class Container implements Renderable {

    protected $name;

    protected $content;

    protected $elements = array();

    protected $template = 'form-builder::container';


    public function __construct($name, $params = array())
    {
        $this->name = $name;

        if (array_key_exists('template', $params)) {
            $this->setTemplate($params['template']);
        }
    }


    public static function create($name)
    {
        return new self($name);
    }


    public function add()
    {
        $elements = array();

        foreach(func_get_args() as $element)
        {

            if ($element instanceof Renderable)
            {
                $elements[] = $element;
            }
            elseif (is_string($element))
            {
                $elements[] = Container::create($element);
            }
        }

        $this->elements = array_merge($this->elements, $elements);

        return $this;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }


    public function render()
    {
        return View::make($this->template)
            ->withName($this->name)
            ->withElements($this->elements);
    }


    public function __toString()
    {
        return (string) $this->render();
    }
}