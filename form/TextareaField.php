<?php

namespace iseeyoucopy\phpmvc\form;


/**
 * Class TextareaField
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc\form
 */
class TextareaField extends BaseField
{
    public function renderInput()
    {
        return sprintf('<textarea class="form-control%s" name="%s">%s</textarea>',
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            htmlspecialchars($this->attribute),
            htmlspecialchars($this->model->{$this->attribute}),
        );
    }
}