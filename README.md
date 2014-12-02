Yii2 Conditional Validator
==========================

> Note:
Since version `2.0.0-beta`, [Yii2 has it's own conditional validator](http://www.yiiframework.com/doc-2.0/guide-input-validation.html#conditional-validation) built in to the core.
Please use validator provided by the framework. This extension **will no receive updates any longer**.

Yii2 Conditional Validator (Y2CV) validates some attributes depending on certain conditions (rules).
You can use any core validator as you usually would do or any other class based or inline validator.
An interesting feature is that you can even use the own Y2CV inside itself to perform more complex conditions.
Basically, Y2CV executes the rules set in the param `if` and if there are no errors executes the rules set in the param `then`.

## Requirements

- Yii 2.0
- PHP 5.4


## Installation


The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

``` php composer.phar require kop/yii2-conditional-validator "dev-master" ```

or add

``` "kop/yii2-conditional-validator": "dev-master"```

to the `require` section of your `composer.json` file.


## Syntax Example

```php
[['safeAttributes'], `path.to.ConditionalValidator`,
    'if' => [
        // rule1: [['attrX', 'attrY'], 'required', ... ]
        // ruleN: ...
    ],
    'then' => [
        // rule1: [['attrZ', 'attrG'], 'required', ... ]
        // ruleN: ...
    ]
]
```

- `safeAttributes`: The name of the attributes that should be turned safe (since Yii has no way to make dynamic validators to turn attributes safe);
- `path.to.ConditionalValidator`: In the most of cases will be `ConditionalValidator::className()`;
- `if`: (bidimensional array) The conditional rules to be validated. *Only* if they are all valid (i.e., have no errors) then the rules in `then` will be validated;
- `then`: (bidimensional array) The rules that will be validated *only* if there are no errors in rules of `if` param.

> Note:
Errors in the rules set in the param `if` are discarded after checking. Only errors in the rules set in param `then` are really kept.


## Usage Examples

`If` *customer_type* is "active" `then` *birthdate* and *city* are `required`:
```php
public function rules()
{
    return [
        [['customer_type'], ConditionalValidator::className(),
            'if' => [
                [['customer_type'], 'compare', 'compareValue' => 'active']
            ],
            'then' => [
                [['birthdate', 'city'], 'required']
            ]
        ]
    ];
}
```

`If` *customer_type* is "inactive" `then` *birthdate* and *city* are `required` **and** *city* must be "sao_paulo", "sumare" or "jacarezinho":
```php
public function rules()
{
    return [
        [['customer_type'], ConditionalValidator::className(),
            'if' => [
                [['customer_type'], 'compare', 'compareValue' => 'active']
            ),
            'then' => [
                [['birthdate', 'city'], 'required'],
                [['city'], 'in', 'range' => ['sao_paulo', 'sumare', 'jacarezinho']]
            ]
        ]
    ];
}
```

`If` *information* starts with 'http://' **and** has at least 24 chars length `then` the own *information* must be a valid url:
```php
public function rules()
{
    return [
        [['information'], ConditionalValidator::className(),
            'if' => [
                [['information'], 'match', 'pattern' => '/^http:\/\//'],
                [['information'], 'string', 'min' => 24, 'allowEmpty' => false]
            ),
            'then' => [
                [['information'], 'url']
            ]
        ]
    ];
}
```


## License

**yii2-conditional-validator** is released under the MIT License. See the bundled `LICENSE.md` for details.


## Resources

- [Project Page](http://kop.github.io/yii2-conditional-validator)
- [Packagist Package](https://packagist.org/packages/kop/yii2-conditional-validator)
- [Source Code](https://github.com/kop/yii2-conditional-validator)
