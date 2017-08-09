# Audit Client

## Installation

Just add the following requirement to your `composer.json` file:

```
    "fei/audit-client": "^1.2.0"
```

## Configuration

The audit event client needs some options to work properly. The available options that can be passed to the `__construct()` or `setOptions()` methods are :


| Option           | Description                                                                | Type   | Possible Values                                | Default                 |
|------------------|----------------------------------------------------------------------------|--------|------------------------------------------------|-------------------------|
| OPTION_BASEURL   | This is the server to which send the requests.                             | string | Any URL, including protocol but excluding path | --                      |
| OPTION_FILTER    | Minimum notification level required for notifications to be actually sent. | int    | Any AuditEvent::LVL_* constant               | AuditEvent::LVL_ERROR |
| OPTION_BACKTRACE | Should backtrace be added to notifications before they are sent.           | bool   | true / false                                   | true                    |

Notes:
*Audit is an alias of Fei\Service\AuditEvent\Client\Audit*
*AuditEvent is an alias of Fei\Service\Audit\Entity\AuditEvent*

## Usage

### Initialization

An Audit client should always be initialized by a dependency injection component, since it requires at least one dependency, which is the transport. Moreover, the BASEURL parameter should also depends on environment.

```php
// sample configuration for production environment
$audit = new Audit(array(
                            Audit::OPTION_BASEURL  => 'http://audit.flash-global.net',
                            Audit::OPTION_FILTER   => AuditEvent::LVL_DEBUG,
                          )
                    );
// inject transport classes
$audit->setTransport(new BasicTransport());

// optionnal asynchronous transport, that will be automatically used to push notifications
//
// NOTE this transport requires a beanstalk queue able to listen to its requests
$pheanstalk = new Pheanstalk('localhost');
$asyncTransport = new BeanstalkProxyTransport;
$asyncTransport->setPheanstalk($pheanstalk);
$audit->setAsyncTransport($asyncTransport);
```


### Pushing a simple notification

Once you have set up the Audit, you can start pushing notifications by calling the `notify()` method on the Audit:

```php

$audit = $container->get('audit.client');

$audit->notify('AuditEvent message'); // default level is AuditEvent::LVL_INFO
$audit->notify('Debug message', array('level' => AuditEvent::LVL_DEBUG));
```

While its possible to pass more than just the level using the second (array) parameter, it is recommended not to do so. If you want to pass more informations, like a context, please take a look at the following section.

### Pushing a AuditEvent instance

The more reliable way to push a notification is to instantiate it by yourself, and then send it through `notify()`, that will also accept AuditEvent instances:

```php

$audit = $container->get('audit.client');

$auditEvent = new AuditEvent(array('message' => 'AuditEvent message'));
$auditEvent
        ->setLevel(AuditEvent::LVL_WARNING)
        ->setContext(array('key' => 'value')
        ;
        
$audit->notify($auditEvent);

```

