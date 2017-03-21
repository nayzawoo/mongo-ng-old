<?php

namespace App\Libs\Html;

/*
 * Created by Nay Zaw Oo<nayzawoo.me@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM
 * @see https://gist.github.com/stidges/1d6e0999fdbd532960e9
 */

use Illuminate\Html\FormBuilder as IlluminateFormBuilder;

class FormBuilder extends IlluminateFormBuilder
{
    /**
     * An array containing the currently opened form groups.
     *
     * @var array
     */
    protected $groupStack = array();

    /**
     * Open a new form group.
     *
     * @param string $name
     * @param mixed  $label
     * @param array  $options
     *
     * @return string
     */
    public function openGroup($name, $label = null, $options = array())
    {
        $options = $this->appendClassToOptions('form-group', $options);
        // Append the name of the group to the groupStack.
        $this->groupStack[] = $name;
        // If a label is given, we set it up here. Otherwise, we will just
        // set it to an empty string.
        $label = $label ? $this->label($name, $label) : '';

        return '<div'.$this->html->attributes($options).'>'.$label;
    }

    /**
     * Close out the last opened form group.
     *
     * @return string
     */
    public function closeGroup()
    {
        $name = array_pop($this->groupStack);

        return '</div>';
    }

    /**
     * Create a form input field.
     *
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array  $options
     *
     * @return string
     */
    public function input($type, $name, $value = null, $options = array())
    {
        $options = $this->appendClassToOptions('form-control', $options);

        return parent::input($type, $name, $value, $options);
    }

    /**
     * Create a select box field.
     *
     * @param string $name
     * @param array  $list
     * @param string $selected
     * @param array  $options
     *
     * @return string
     */
    public function select($name, $list = array(), $selected = null, $options = array())
    {
        $options = $this->appendClassToOptions('form-control', $options);

        return parent::select($name, $list, $selected, $options);
    }

    /**
     * Create a plain form input field.
     *
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array  $options
     *
     * @return string
     */
    public function plainInput($type, $name, $value = null, $options = array())
    {
        return parent::input($type, $name, $value, $options);
    }

    /**
     * Create a plain select box field.
     *
     * @param string $name
     * @param array  $list
     * @param string $selected
     * @param array  $options
     *
     * @return string
     */
    public function plainSelect($name, $list = array(), $selected = null, $options = array())
    {
        return parent::select($name, $list, $selected, $options);
    }

    /**
     * Create a checkable input field.
     *
     * @param string $type
     * @param string $name
     * @param mixed  $value
     * @param bool   $checked
     * @param array  $options
     *
     * @return string
     */
    protected function checkable($type, $name, $value, $checked, $options)
    {
        $checked = $this->getCheckedState($type, $name, $value, $checked);
        if ($checked) {
            $options['checked'] = 'checked';
        }

        return parent::input($type, $name, $value, $options);
    }

    /**
     * Create a checkbox input field.
     *
     * @param string $name
     * @param mixed  $value
     * @param mixed  $label
     * @param bool   $checked
     * @param array  $options
     *
     * @return string
     */
    public function checkbox($name, $value = 1, $label = null, $checked = null, $options = array())
    {
        $checkable = parent::checkbox($name, $value, $checked, $options);

        return $this->wrapCheckable($label, 'checkbox', $checkable);
    }

    /**
     * Create an inline checkbox input field.
     *
     * @param string $name
     * @param mixed  $value
     * @param mixed  $label
     * @param bool   $checked
     * @param array  $options
     *
     * @return string
     */
    public function inlineCheckbox($name, $value = 1, $label = null, $checked = null, $options = array())
    {
        $checkable = parent::checkbox($name, $value, $checked, $options);

        return $this->wrapInlineCheckable($label, 'checkbox', $checkable);
    }

    /**
     * Create an inline radio button input field.
     *
     * @param string $name
     * @param mixed  $value
     * @param mixed  $label
     * @param bool   $checked
     * @param array  $options
     *
     * @return string
     */
    public function radio($name, $value = null, $label = null, $checked = null, $options = array())
    {
        $checkable = parent::radio($name, $value, $checked, $options);

        return $this->wrapInlineCheckable($label, 'radio', $checkable);
    }

    /**
     * Create a textarea input field.
     *
     * @param string $name
     * @param string $value
     * @param array  $options
     *
     * @return string
     */
    public function textarea($name, $value = null, $options = array())
    {
        $options = $this->appendClassToOptions('form-control', $options);

        return parent::textarea($name, $value, $options);
    }

    /**
     * Create a plain textarea input field.
     *
     * @param string $name
     * @param string $value
     * @param array  $options
     *
     * @return string
     */
    public function plainTextarea($name, $value = null, $options = array())
    {
        return parent::textarea($name, $value, $options);
    }

    /**
     * Append the given class to the given options array.
     *
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    private function appendClassToOptions($class, array $options = array())
    {
        $options['class'] = isset($options['class']) ? $options['class'].' ' : '';
        $options['class'] .= $class;

        return $options;
    }

    /**
     * Wrap the given checkable in the necessary wrappers.
     *
     * @param mixed  $label
     * @param string $type
     * @param string $checkable
     *
     * @return string
     */
    private function wrapCheckable($label, $type, $checkable)
    {
        return '<div class="'.$type.
        '"><label>'.$checkable.
        ' '.$label.
        '</label></div>';
    }

    /**
     * Wrap the given checkable in the necessary inline wrappers.
     *
     * @param mixed  $label
     * @param string $type
     * @param string $checkable
     *
     * @return string
     */
    private function wrapInlineCheckable($label, $type, $checkable)
    {
        return '<div class="'.$type.
        '-inline">'.$checkable.
        ' '.$label.
        '</div>';
    }
}
