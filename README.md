# Service Audit - Client

[![GitHub release](https://img.shields.io/github/release/flash-global/audit-client.svg?style=for-the-badge)](README.md) 


## Table of contents
- [Purpose](#purpose)
- [Requirements](#requirements)
    - [Runtime](#runtime)
- [Step by step installation](#step-by-step-installation)
    - [Initialization](#initialization)
    - [Settings](#settings)
    - [Known issues](#known-issues)
- [Link to documentation](#link-to-documentation)
    - [Examples](#examples)
- [Contribution](#contribution)

## Purpose
This client permit to use the `Audit Api`. Thanks to it, you could request the API to :
* Fetch data
* Create data

easily

## Requirements 

### Runtime
- PHP 5.5

## Step by step Installation
> for all purposes (development, contribution and production)

### Initialization
- Add the following requirement to your `composer.json` file:
```"fei/audit-client": "^1.2.0"```
- Run Composer depedencies installation
```composer install```

### Settings

Don't forget to set the right `baseUrl` in files located in examples.

```php
<?php 
$audit = new Audit([Audit::OPTION_BASEURL =>'http://127.0.0.1:8084']);
$audit->setTransport(new Fei\ApiClient\Transport\BasicTransport());
```

### Known issues
No known issue at this time.

## Link to documentation 

### Examples
You can test this client easily thanks to the folder [examples](examples)

Here, an example on how to use example : `php /my/audit-client/examples/search.php` 

#### Pushing a simple notification

Once you have set up the Audit, you can start pushing notifications by calling the `notify()` method on the Audit:

```php

$audit = $container->get('audit.client');

$audit->notify('AuditEvent message'); // default level is AuditEvent::LVL_INFO
$audit->notify('Debug message', array('level' => AuditEvent::LVL_DEBUG));
```

While its possible to pass more than just the level using the second (array) parameter, it is recommended not to do so. If you want to pass more informations, like a context, please take a look at the following section.

#### Pushing a AuditEvent instance

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

## Contribution
As FEI Service, designed and made by OpCoding. The contribution workflow will involve both technical teams. Feel free to contribute, to improve features and apply patches, but keep in mind to carefully deal with pull request. Merging must be the product of complete discussions between Flash and OpCoding teams :) 


