# expo-server-sdk-php

Server-side library for working with Expo using PHP.

If you have any problems with the code in this repository, feel free to [open an issue](https://github.com/ctwillie/expo-server-sdk-php/issues) or make a PR!

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ctwillie/expo-server-sdk-php.svg?style=flat-square)](https://packagist.org/packages/ctwillie/expo-server-sdk-php)

<!-- [![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/ctwillie/expo-server-sdk-php/run-tests?label=tests)](https://github.com/ctwillie/expo-server-sdk-php/actions?query=workflow%3Arun-tests+branch%3Amain) -->

[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/ctwillie/expo-server-sdk-php/Check%20&%20fix%20styling?label=code%20style)](https://github.com/ctwillie/expo-server-sdk-php/actions?query=workflow%3A"Check+%26+fix+styling")

Server-side library for working with Expo using PHP.

## Installation

You can install the package via composer:

```bash
composer require ctwillie/expo-server-sdk-php
```

## Usage

```php
/**
 * Send a one time push notification message to
 * one or more recipients.
 */
$expo = new Expo();
$recipients = ['ExponentPushToken[xxxx-xxxx-xxxx]', 'ExponentPushToken[yyyy-yyyy-yyyy]'];
$message = (new ExpoMessage())
    ->setTitle('Message Title')
    ->setBody('The notification message body')
    ->setChannelId('default')
    ->playSound();

$expo->send($message)->to($recipients)->push();


/**
 * Or, subscribe recipients to a channel, then push
 * notification messages to that channel.
 */
$expo = new Expo();
$recipients = ['ExponentPushToken[xxxx-xxxx-xxxx]', 'ExponentPushToken[yyyy-yyyy-yyyy]'];
$message = (new ExpoMessage())
    ->setTitle('Message Title')
    ->setBody('The notification message body')
    ->setChannelId('default')
    ->playSound();

$channel = 'default';
$expo->subscribe($channel, $recipients);

$expo->send($message)->toChannel($channel)->push();

// You can unsubscribe one or more recipients
// from a channel at any time.
$expo->unsubscribe($channel, $recipients);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Cedric Twillie](https://github.com/ctwillie)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
