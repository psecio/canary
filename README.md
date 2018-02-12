Canary: Input Detection and Response
==================

In security there is the concept of a "canary". Those that used to work deep in mines would take a canary (the bird) with them to
detect gas or other reasons they needed to leave. Similarly, the `Canary` library allows you to define key/value combinations that
can be used to detect when certain data is used.

For example, you may generate a special username that you want to use as a trigger. This username isn't actually a user in your system
but you do want to be notified if a login attempt is made using it. `Canary` makes this simple by defining checks with an `if` method and,
optionally, a handler using a `then` method. For example, say we generated the username of `canary1234@foo.com` and we want to detect when
it's used. You can define this in a `Canary` expression like so:

```php
<?php
$_POST = ['username' => 'canary1234@foo.com'];

\Psecio\Canary\Instance::build()->if('username', 'canary1234@foo.com')->execute();
?>
```

In this example we're looking at the current input and checking to see if there's a `username` value of `canary1234@foo.com`. In the case
of our current `$_POST` values, there's a match. By default (if no `then` handler is defined) the information about the match is output to
the error like (via the `Psecio\Canary\Notify\ErrorLog` handler). The JSON encoded result looks like this:

```
{"type":"equals","key":"username","value":"test"}
```

> **NOTE:** Canary automatically pulls in the `$_GET` and `$_POST` superglobal values for evaluation so you don't need to manually pass
then in.


### Creating a Custom Handler

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
