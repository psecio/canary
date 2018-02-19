Canary: Input Detection and Response
==================

[![Build Status](https://travis-ci.org/psecio/canary.svg?branch=master)](https://travis-ci.org/psecio/canary)

The origin of the term "canary" (as a method of detection) was originally used by those that worked deep in mines and would take a canary
(the bird) with them to detect gas or other reasons they needed to leave. If the bird started behaving oddly they knew something was amiss.
This same concept is applied in the security world and is similarly called a "canary".

Similarly, the `Canary` library allows you to define key/value combinations that can be used to detect when certain data is used and notify
you using a variety of methods including the default PHP error log, log handling via `Monolog` and messages to `Slack` channels.

For example, you may generate a special username that you want to use as a trigger. This username isn't actually a user in your system
but you do want to be notified if a login attempt is made using it. `Canary` makes this simple by defining checks with an `if` method and,
optionally, a handler using a `then` method. For example, say we generated the username of `canary1234@foo.com` and we want to detect when
it's used. You can define this in a `Canary` expression like so:

```php
<?php
$_POST = [
    'username' => 'canary1234@foo.com',
    'password' => 'sup3rs3cr3t'
];

\Psecio\Canary\Instance::build()->if('username', 'canary1234@foo.com')->execute();

// Or you can set multiple match values to look for with an array
$matches = [
    'username' => 'canary1234@foo.com',
    'password' => 'sup3rs3cr3t'
];
\Psecio\Canary\Instance::build()->if($matches)->execute();
?>
```

In this example we're looking at the current input and checking to see if there's a `username` value of `canary1234@foo.com`. In the case
of our current `$_POST` values, there's a match. By default (if no `then` handler is defined) the information about the match is output to
the error like (via the `Psecio\Canary\Notify\ErrorLog` handler). The JSON encoded result looks like this:

```
{"type":"equals","key":"username","value":"canary1234@foo.com"}
```

> **NOTE:** Canary automatically pulls in the `$_GET` and `$_POST` superglobal values for evaluation so you don't need to manually pass
then in.

### Supported Notifier Methods

Currently `Canary` supports the following notification methods:

| Type      | Class                            | Expected Input              |
| --------- | -------------------------------- | --------------------------- |
| Error log | `\Psecio\Canary\Notify\ErrorLog` | None, uses default location |
| Monolog   | `\Psecio\Canary\Notify\Monolog`  | `\Monolog\Logger`           |
| Callback  | `\Psecio\Canary\Notify\Callback` | Callable function           |
| Slack     | `\Psecio\Canary\Notify\Slack`    | `\Maknz\Slack\Client`       |
| PagerDuty | `\Psecio\Canary\Notify\PagerDuty`| `\PagerDuty\Event`          |

### Creating a Custom Handler (Callback)

If you don't want your results to go to the error log, you can create your own handler via the `then` method. Currently the only custom
handler supported is a callable method. So, say we wanted to output a message to the user of our special username and kill the script. We
might use something like this:

```php
<?php
$_POST = ['username' => 'canary1234@foo.com'];

\Psecio\Canary\Instance::build()->if('username', 'canary1234@foo.com')
    ->then(function($criteria) {
        die("You shouldn't have done that!");
    })
    ->execute();
?>
```

In this handler, when it detects that the username value matches our criteria, the callback is executed and the `die` call kills the script.

### Passing in custom data

You can also provide your own data set if you don't want to auto-load the current `$_GET` and `$_POST` values. To pass the data in you can use the
`data` value in the configuration and passing it in:

```php
<?php
$config = ['data' => [
    'username' => 'foobar@baz.com'
]];
\Psecio\Canary\Instance::build($config)->if('username', 'canary1234@foo.com')->execute();
?>
```

### Using a default logger

You can set it as the default logger for **all** `if` checks via the `notify` key in the `build()` configuration options:

```php
<?php

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler('/tmp/mylog.log', Logger::WARNING));

$config = [
    'notify' => $log
];
\Psecio\Canary\Instance::build($config)->if('username', 'canary1234@foo.com')->execute();

?>
```

> **NOTE:** If you provide a default handler via the `notify` configuration it will override all other custom notification methods.


### Using Monolog

The `Canary` tool also allows you to use the [Monolog](https://github.com/Seldaek/monolog) logging library to define a bit more customization to the structure of the data and how it's output. Like before, we create the `Canary` instance but for the input of the `then` method we provide a `Monolog\Logger` instance:

```php
<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once 'vendor/autoload.php';

$_GET = ['username' => 'test'];

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler('/tmp/mylog.log', Logger::WARNING));

\Psecio\Canary\Instance::build()
    ->if('username', 'canary1234@foo.com')
    ->then($log)
    ->execute();
?>
```

### Using Slack

You can also make use of the [Maknz\Slack](https://github.com/maknz/slack) library to send messages to Slack when a canary is triggered:

```php
<?php
$settings = [
	'channel' => '#my-channel-name',
	'link_names' => true
];
$slack = new Maknz\Slack\Client('https://hooks.slack.com/services/.....', $settings);

\Psecio\Canary\Instance::build($config)->if('username', 'canary1234@foo.com')->then($slack);
?>
```

You'll need to [set up an incoming webhook](https://my.slack.com/services/new/incoming-webhook) and replace the URL value in the `Client`
create with the custom URL you're given. The default name for the notifications is `Canary Agent` and the output includes the same JSON
information as the other notification methods.

### Using PagerDuty

`Canary` also allows you to send notifications to your account on the PagerDuty service using the [nmcquay/pagerduty](https://github.com/nmcquay/pagerduty) library:

```php
<?php
$pager = new \PagerDuty\Event();
$pager->setServiceKey('[.... your service ID ....]');

\Psecio\Canary\Instance::build($config)->if('username', 'canary1234@foo.com')->then($pager);
?>
```

You can find the service ID by going to your services page (`https://[your domain].pagerduty.com/services`) and clicking on the service you want to use. The ID is under the "Integrations" tab as the "Integration Key".
