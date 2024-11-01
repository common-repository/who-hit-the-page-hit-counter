## Material PHP

[![Build Status](https://travis-ci.org/mahlamusa/material-php.svg?branch=master)](https://travis-ci.org/mahlamusa/material-php)
[![StyleCI](https://styleci.io/repos/REPO_ID_CHANGE_THIS/shield?branch=master)](https://styleci.io/repos/REPO_ID_CHANGE_THIS)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mahlamusa/material-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mahlamusa/material-php/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/mahlamusa/material-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mahlamusa/material-php/code-structure/master/code-coverage)
[![Packagist Version](https://img.shields.io/packagist/v/mahlamusa/material-php.svg?style=flat-square)](https://github.com/mahlamusa/material-php/releases)
[![Packagist Downloads](https://img.shields.io/packagist/dt/mahlamusa/material-php.svg?style=flat-square)](https://packagist.org/packages/mahlamusa/material-php)

Material Design Components helper class for PHP. This php library helps you create material design components using PHP and Google's Material Design Framework.


## Install

Require this package with composer using the following command:

``` bash
$ composer require mahlamusa/material-php
```

### Usage example:

    <?php
    require 'vendor/autoload.php';
    
    echo Lite::text_field(array('id'=>'field_id'));

    // OR
    echo Input(array('id'=>'field_id'));

    echo Button( $args );

    echo Card( array('title'=>'The title', 'content'=> 'This is the card content'));
    

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

 - [Lindeni Mahlalela](http://github.com/mahlamusa)
 - [All Contributors](../../contributors)

## License

The GPL v2 or later. Please see [License File](LICENSE) for more information.
