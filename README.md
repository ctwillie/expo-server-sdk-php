# expo-server-sdk-php ![tests](https://github.com/ctwillie/expo-server-sdk-php/actions/workflows/tests.yml/badge.svg) [![codecov](https://codecov.io/gh/ctwillie/expo-server-sdk-php/branch/master/graph/badge.svg?token=8QO3NL131R)](https://codecov.io/gh/ctwillie/expo-server-sdk-php) ![GitHub](https://img.shields.io/github/license/ctwillie/expo-server-sdk-php?color=%2361c82e)

Server-side library for working with Expo using PHP.

If you have any problems with the code in this repository, feel free to [open an issue](https://github.com/ctwillie/expo-server-sdk-php/issues) or make a PR!

<details open="open">
<summary>Table of Contents</summary>

-   [Testing](#testing)
-   [Installation](#installation)
-   [Use Cases](#use-cases)
    -   [One Time Notifications](#one-time-notifications)
    -   [Channel Subscriptions](#channel-subscriptions)
-   [Changelog](#changelog)
-   [Contributing](#contributing)
-   [License](#license)

</details>

## Testing

You can run the test suite via composer:

```bash
composer test
```

## Installation

You can install the package via composer:

```bash
composer require ctwillie/expo-server-sdk-php
```

## Use Cases

This package was written with two main use cases in mind.

1. Sending one time push notifications. Simply push a message to one or more tokens, then your done!
2. And channel subscriptions, used to subscribe one or more tokens to a channel, then send push notifications to all tokens subscribed to that channel. Subscriptions are persisted until a token unsubscribes from a channel. Maybe unsubscribing upon the end users request.

Keep this in mind as you decide which is the best use case for your back end.

### One time notifications:

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
```

> :warning: **If you are are running multiple app servers**: Be very careful here! Channel subscriptions are stored in an internal local file. Subscriptions will not be shared across multiple servers. Database drivers coming in the near future to handle this use case.

### Channel subscriptions:

```php
/**
 * Subscribe tokens to a channel, then push notification
 * messages to that channel. Subscriptions are
 * persisted internally in a local file. Unsubscribe
 * the token from the channel at any time.
 */

// Specify the file driver to persist subscriptions
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
// The channel will be created automatically if it doesn't already exist
$expo->subscribe($channel, $recipients);

$expo->send($message)->toChannel($channel)->push();

// You can unsubscribe one or more recipients
// from a channel.
$expo->unsubscribe($channel, $recipients);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

-   [Cedric Twillie](https://github.com/ctwillie)
-   [All Contributors](../../contributors)
