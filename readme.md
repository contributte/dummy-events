# Events

[![Build Status](https://travis-ci.org/minetro/events.svg?branch=master)](https://travis-ci.org/minetro/events)
[![Downloads this Month](https://img.shields.io/packagist/dm/minetro/events.svg?style=flat)](https://packagist.org/packages/minetro/events)
[![Latest stable](https://img.shields.io/packagist/v/minetro/events.svg?style=flat)](https://packagist.org/packages/minetro/events)
[![HHVM Status](https://img.shields.io/hhvm/minetro/events.svg?style=flat)](http://hhvm.h4cc.de/package/minetro/events)

Simple events for Nette.

If you want complex events solution - there is **[Kdyby\Events](https://github.com/kdyby/events)** for you. 

## Install

```sh
$ composer require minetro/events:~1.2.0
```

## Usage

### Register extension

Register in your config file (e.q. config.neon).

```neon
extensions:
    events: Minetro\Events\EventsExtension
```

### Register events

On Container compile - **EventsExtension** collect all services which implement **EventsSubscriber** and call their `onEvents($em)` method.

```php
use Minetro\Events\EventsSubscriber;
use Minetro\Events\EventsManager;

class TestService implements EventsSubscriber 
{
    /**
     * @param EventsManager $em
     */
    public function onEvents(EventsManager $em) {
        $em->on('order.update', function($state) {
            // Some logic..
        });
    }
}
```

### Register lazy events

Name tag as event name with prefix **event**.

```neon
services:
    {class: TestService, tags: [event.order.update]}
```

Or use tag arrays with key name **events**.

```neon
services:
    {class: TestService, tags: [events: [order.update]]}
```

This prevents usage of other tags.

If **EventsSubscriber** register more events and also is lazy registered (by tags in neon). Implemented method
`onEvents(EventsManager $em)` is called **only once**.

```php
use Minetro\Events\EventsSubscriber;
use Minetro\Events\EventsManager;

class TestSubscriber implements EventsSubscriber 
{
    
    public function onEvents(EventsManager $em) {
        $em->on('order.create', function($state) {
            // Some logic..
        });
        
        $em->on('order.update', function($state) {
            // Some logic..
        });
        
        $em->on('order.delete', function($state) {
            // Some logic..
        });
    }
}
```

### Fire events

Inject to your class ultra-simple **EventsManager**.

```php
use Minetro\Events\EventsManager;

/** @var EventsManager @inject **/
public $em;

public function save() {
    // Some logic..
    
    // Fire order update events
    $this->em->trigger('order.update', $order->state);
}
```
