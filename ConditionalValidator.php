<?php

namespace kop\y2cv;

use yii\base\InvalidConfigException;
use yii\validators\Validator;

/**
 * Validates multiple attributes using any Yii2 validator
 * depending on some another attribute's condition (validation) is true.
 *
 * Example of rule in a hypothetical invoice checking if id_payment_method is Money
 * or Card, if so, then dueDate is required and must be numerical:
 *
 * <code>
 * ['dueDate', ConditionalValidator::className(),
 *      'if' => [
 *          [['id_payment_method'], 'in', 'range' => [PaymentMethod::MONEY, PaymentMethod::CARD], 'allowEmpty' => false]
 *      ],
 *      'then' => [
 *          [['dueDate'], 'required'],
 *          [['dueDate'], 'numerical']
 *      ]
 * ]
 * </code>
 *
 * This validator is inspired by {@link https://github.com/sidtj/Yii-Conditional-Validator}.
 *
 * @license https://github.com/kop/Yii2-Conditional-Validator/blob/master/LICENSE.md MIT
 * @link    http://kop.github.io/yii2-conditional-validator Project page
 *
 * @author  Ivan Koptiev <ikoptev@gmail.com>
 * @version 0.1
 */
class ConditionalValidator extends Validator
{
    /**
     * @var array $if "If" conditions.
     */
    public $if = [];

    /**
     * @var array $then "Then" conditions.
     */
    public $then = [];

    /**
     * @var boolean $skipOnEmpty Whether this validation rule should be skipped if the attribute value is null or an empty string.
     */
    public $skipOnEmpty = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Validate given conditions
        foreach (['if', 'then'] as $attribute) {
            if (!is_array($this->$attribute)) {
                $className = self::className();
                throw new InvalidConfigException("Invalid argument \"{$attribute}\" for \"{$className}\". Please, supply an array.");
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($object, $attribute)
    {
        // Check "If" condition
        $internalObject = clone $object;
        $internalObject->clearErrors();
        $attributes = $internalObject->activeAttributes();
        $ifValidators = $this->createValidators($object, $this->if);
        foreach ($ifValidators as $validator) {
            $validator->validateAttributes($internalObject, $attributes);
        }

        // Apply "Then" condition
        if (!$internalObject->hasErrors()) {
            $thenValidators = $this->createValidators($object, $this->then);
            foreach ($thenValidators as $validator) {
                $validator->validateAttributes($object, $attributes);
            }
        }
    }

    /**
     * Creates validator objects based on the given model object and validation rules.
     *
     * @param \yii\base\Model $object Model instance to create validators for.
     * @param array           $rules  List of the validation rules declarations.
     *
     * @throws \yii\base\InvalidConfigException If errors in validation rules declarations found.
     * @return Validator[] Validators objects.
     */
    protected function createValidators($object, $rules)
    {
        $validators = new \ArrayObject();
        foreach ($rules as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) {
                $validator = Validator::createValidator($rule[1], $object, (array)$rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }

        return $validators;
    }
}