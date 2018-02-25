<?php


namespace MigEvents\Traits;

use MigEvents\Enum\EnumInstanceInterface;

/**
 * @method static EnumInstanceInterface getFieldsEnum()
 */
trait FieldValidation {

    /**
     * @param string $name
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @staticvar array $fields
     */
    public function __set($name, $value) {
        if (static::getFieldsEnum()->isValidValue($name)) {
            parent::__set($name, $value);
        } else {
            throw new \InvalidArgumentException(
            $name . ' is not a field of ' . get_class($this));
        }
    }
}
