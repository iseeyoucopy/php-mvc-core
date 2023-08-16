<?php

namespace iseeyoucopy\phpmvc\form;

use iseeyoucopy\phpmvc\Model;

class Field extends BaseField
{
    const TYPE_TEXT = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_FILE = 'file';
    const TYPE_TEXTAREA = 'textarea';

    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    public function renderInput()
    {
        if ($this->type === self::TYPE_TEXTAREA) {
            return sprintf('<textarea class="form-control%s" name="%s">%s</textarea>',
                $this->model->hasError($this->attribute) ? ' is-invalid' : '',
                htmlspecialchars($this->attribute),
                htmlspecialchars($this->model->{$this->attribute}),
            );
        } else {
            return sprintf('<input type="%s" class="form-control%s" name="%s" value="%s">',
                $this->type,
                $this->model->hasError($this->attribute) ? ' is-invalid' : '',
                htmlspecialchars($this->attribute),
                htmlspecialchars($this->model->{$this->attribute}),
            );
        }
    }

    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function fileField()
    {
        $this->type = self::TYPE_FILE;
        return $this;
    }

    public function textareaField()
    {
        $this->type = self::TYPE_TEXTAREA;
        return $this;
    }
}
