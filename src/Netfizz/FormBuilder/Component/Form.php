<?php namespace Netfizz\FormBuilder\Component;

use Netfizz\FormBuilder\Component\Container;
use Illuminate\Support\Facades\Form as FormBuilder;

class Form extends Container {

    protected $type = 'form';

    protected $name;

    protected $value;

    protected $options;

    protected $model;


    public function __construct($name, $content, $options = array())
    {
        $this->options = $options;
        parent::__construct('form', $name, $content, $options);
    }

    public function bind($model) {
        $this->model = $model;
    }


    protected function makeOpenFormTag()
    {
        $form = $this->model ?
            FormBuilder::model($this->model, $this->getOptions()) :
            FormBuilder::open($this->getOptions());

        return $form;
    }

    protected function makeCloseFormTag() {
        return FormBuilder::close();
    }


    protected function getDatas() {
        return array_merge(parent::getDatas(), array(
            'openFormTag' => $this->makeOpenFormTag(),
            //'closeFormTag' => $this->makeCloseFormTag()
        ));
    }

} 