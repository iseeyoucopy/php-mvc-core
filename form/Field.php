<?php

namespace iseeyoucopy\phpmvc\form;

use iseeyoucopy\phpmvc\Model;
/**
 * Class Field
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc\form
 */
class Field extends BaseField
{
    const TYPE_TEXT = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_FILE = 'file';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_INPUT = 'type';
    const TYPE_INPUT_HIDDEN = 'hidden';
    const TYPE_SELECT = 'select'; // Add a new field type for select
    protected bool $readOnly = false;

    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }
    public function setReadOnly(bool $readOnly = true): self
    {
        $this->readOnly = $readOnly;
        return $this;
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
            $readOnlyAttribute = $this->readOnly ? ' readonly' : ''; // Check if field is read-only
            return sprintf('<input type="%s" class="form-control%s" name="%s" value="%s" placeholder="%s" %s>',
                $this->type,
                $this->model->hasError($this->attribute) ? ' is-invalid' : '',
                htmlspecialchars($this->attribute),
                htmlspecialchars($this->model->{$this->attribute}),
                htmlspecialchars($this->attribute), // Repeated for placeholder
                $readOnlyAttribute,
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
    public function inputField($options = [])
    {
        $this->type =  self::TYPE_INPUT;
        $this->type = self::TYPE_INPUT;
        error_log("Setting type to " . self::TYPE_INPUT);
        return $this;
    }
    public function setHiddenInput($options = [])
    {
        $this->type = self::TYPE_INPUT_HIDDEN;
        return $this;
    }
    public function selectField(array $options)
    {
        $this->type = self::TYPE_SELECT;
        $selectOptions = '';

        $selectedValue = (string) $this->model->{$this->attribute};

        foreach ($options as $value => $label) {
            $selected = $value === $selectedValue ? 'selected' : '';
            $selectOptions .= sprintf('<option value="%s" %s>%s</option>', htmlspecialchars($value), $selected, htmlspecialchars($label));
        }

        $formControlClass = $this->model->hasError($this->attribute) ? ' is-invalid' : '';
        $fieldName = htmlspecialchars($this->attribute);

        return sprintf('<select class="form-control%s" name="%s">%s</select>', $formControlClass, $fieldName, $selectOptions);
    }
    public function fileInput()
    {
        $this->type = self::TYPE_FILE;
        return $this;
    }
}
