# phergie/phergie-irc-plugin-react-db

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for adding phergie events to database.

[![Build Status](https://secure.travis-ci.org/phergie/phergie-irc-plugin-react-db.png?branch=master)](http://travis-ci.org/phergie/phergie-irc-plugin-react-db)

## Install

The recommended method of installation is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "phergie/phergie-irc-plugin-react-db": "dev-master"
    }
}
```

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

```php
new \Phergie\Irc\Plugin\React\Db\Plugin(array(



))
```

## Tests

To run the unit test suite:

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
./vendor/bin/phpunit
```

## License

Released under the BSD License. See `LICENSE`.
