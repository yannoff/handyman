# handyman

A basic REPL for symfony applications

[![Latest Stable Version](https://poser.pugx.org/yannoff/handyman/v/stable)](https://packagist.org/packages/yannoff/handyman)
[![Total Downloads](https://poser.pugx.org/yannoff/handyman/downloads)](https://packagist.org/packages/yannoff/handyman)
[![License](https://poser.pugx.org/yannoff/handyman/license)](https://packagist.org/packages/yannoff/handyman)

## Installation

Using composer:

```
composer require --dev yannoff/handyman
```

## Usage

Call the REPL script from the application top-level directory:

```
vendor/bin/handyman
```
> _Depending on the [main `composer.json` config](https://getcomposer.org/doc/06-config.md#bin-dir), a link to the script may be available in the `bin/` directory._

### Example

```
PHP> print_r(get_class_methods(self::get('slugger')));;
Array
(
    [0] => __construct
    [1] => setLocale
    [2] => getLocale
    [3] => slug
)

PHP> echo self::get('slugger')->slug('this is my text')  
PHP> // Note: to trigger eval, line must end with a double semi-colon (;;)
PHP> ;;
this-is-my-text
PHP> 
```

> _Code will be eval'd as soon as two semi-colons (`;;`) are detected in the line end._

### Options

#### `--kernel`

*Alternative application's kernel fully-qualfied class name (instead of `App\Kernel`)*

#### `--working-dir`

*Optional override for the `%kernel.project_dir%` value*

#### `--verbose`

*Turn on verbose mode*

## License

Licensed under the [MIT Licence](LICENSE).
