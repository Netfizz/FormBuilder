<?php namespace Netfizz\FormBuilder\Component;

use Netfizz\FormBuilder\Component;
use Illuminate\Support\Facades\Form as FormBuilder;

class Form extends Component {

    protected $type = 'form';

    protected $name;

    protected $value;

    protected $options;

    protected $model;


    public function __construct($name, $content, $options = array())
    {
        $this->options = $options;

        if ($name === null) {
            $name = 'form';
        }

        $this->setPrefixId($name);

        parent::__construct('form', $name, $content, $options);
    }


    public function getPrefixId()
    {
        return null;
    }

    public function setPrefixId($prefix)
    {
        $this->getFormService()->setFormId($prefix);
        return $this;
    }


    public function bind($model)
    {
        $this->model = $model;

        $this->getFormService()->setModel($model);

        return $this;
    }


    protected function makeFormOpenTag()
    {
        $form = $this->model ?
            FormBuilder::model($this->model, $this->attributes()) :
            FormBuilder::open($this->attributes());

        return $form;
    }

    protected function makeFormCloseTag()
    {
        return '</form>';
    }


    protected function getDatas()
    {
        return array_merge(parent::getDatas(), array(
            'formOpenTag' => $this->makeFormOpenTag(),
            'formCloseTag' => $this->makeFormCloseTag()
        ));
    }

    public function render()
    {
        FormBuilder::close();
        return parent::render();
    }


} 