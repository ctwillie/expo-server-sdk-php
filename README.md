# expo-server-sdk-php

Server-side library for working with Expo using PHP.

If you have any problems with the code in this repository, feel free to [open an issue](https://github.com/ctwillie/expo-server-sdk-php/issues) or make a PR!

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

$message = (new ExpoMessage())
    ->setTitle('Message Title')
    ->setBody('The notification message body')
    ->setChannelId('default')
    ->playSound();

$recipients = [
    'ExponentPushToken[xxxx-xxxx-xxxx]',
    'ExponentPushToken[yyyy-yyyy-yyyy]'
];

$expo->send($message)->to($recipients)->push();


/**
 * Or, subscribe recipients to a channel, then push
 * notification messages to that channel.
 */

// Use the "file" driver to interact with and persist your subscriptions.
// The storage is handled internally using a local file.
$expo = Expo::driver('file');

$message = (new ExpoMessage())
    ->setTitle('Message Title')
    ->setBody('The notification message body')
    ->setChannelId('default')
    ->playSound();

$recipients = [
    'ExponentPushToken[xxxx-xxxx-xxxx]',
    'ExponentPushToken[yyyy-yyyy-yyyy]'
];

$channel = 'news-letter';
// The channel will be created automatically if it doesn't exist
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
