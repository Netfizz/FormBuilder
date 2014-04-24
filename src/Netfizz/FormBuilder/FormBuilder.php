<?php namespace Netfizz\FormBuilder;

use Illuminate\Html\FormBuilder as DefaultFormBuilder;

class FormBuilder extends DefaultFormBuilder {

    public function getModel()
    {
        return $this->model;
    }

}
