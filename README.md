Yii2 Conditional Validator
==========================

Yii2 Conditional Validator (Y2CV) validates some attributes depending on certain conditions (rules).
You can use any core validator as you usually would do or any other class based or inline validator.
An interesting feature is that you can even use the own Y2CV inside itself to perform more complex conditions.
Basically, Y2CV executes the rules set in the param `if` and if there are no errors executes the rules set in the param `then`.


## Installation

Describe composer here.


## Usage Examples

```php
['safeAttribsList', ConditionalValidator::className(),
    'if' => [
        // rule1: ['attrX, attrY', 'required', ... ]
        // ruleN: ...
    ],
    'then' => [
        // rule1: ['attrZ, attrG', 'required', ... ]
        // ruleN: ...
    ]
]
```

- `safeAttribsList`: The name of the attributes that should be turned safe (since Yii has no way to make dinamic validators to turn attributes safe);
- `path.to.YiiConditionalValidator`: In the most of cases will be `ext.YiiConditionalValidator`;
- `if`: (bidimensional array) The conditional rules to be validated. *Only* if they are all valid (i.e., have no errors) then the rules in `then` will be validated;
- `then`: (bidimensional array) The rules that will be validated *only* if there are no errors in rules of `if` param;

> Note:
Errors in the rules set in the param `if` are discarded after checking. Only errors in the rules set in param `then` are really kept.


## Examples

`If` *customer_type* is "active" `then` *birthdate* and *city* are `required`:
```php
public function rules()
{
    return array(
        array('customer_type', 'ext.YiiConditionalValidator',
            'if' => array(
                array('customer_type', 'compare', 'compareValue'=>"active"),
            ),
            'then' => array(
                array('birthdate, city', 'required'),
            ),
        ),
    );
}
```

`If` *customer_type* is "inactive" `then` *birthdate* and *city* are `required` **and** *city* must be "sao_paulo", "sumare" or "jacarezinho":
```php
public function rules()
{
    return array(
        array('customer_type', 'ext.YiiConditionalValidator',
            'if' => array(
                array('customer_type', 'compare', 'compareValue'=>"active"),
            ),
            'then' => array(
                array('birthdate, city', 'required'),
                array('city', 'in', 'range' => array("sao_paulo", "sumare", "jacarezinho")),
            ),
        ),
    );
}
```

`If` *information* starts with 'http://' **and** has at least 24 chars length `then` the own *information* must be a valid url:
```php
public function rules()
{
    return array(
        array('information', 'ext.YiiConditionalValidator',
            'if' => array(
                array('information', 'match', 'pattern'=>'/^http:\/\//'),
                array('information', 'length', 'min'=>24, 'allowEmpty'=>false),
            ),
            'then' => array(
                array('information', 'url'),
            ),
        ),
    );
}
```