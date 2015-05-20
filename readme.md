# Events

Simple events for Nette.

If you want try another events solution - there is **[Kdyby\Events](https://github.com/kdyby/events)** or **[minetro/events](https://github.com/minetro/events)** for you. 

## Install

Register in your `composer,json`.

```json
# composer.json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/tomaskubat/events"
    }
],
"require": {
    ...,
    "tomaskubat/events": "dev-master"
},
```

## Usage

### Register extension

Register in your config file (e.q. `config.neon`).

```neon
extensions:
    events: TomasKubat\Nette\Events\EventsExtension
```

### Register lazy monitors

On Container compile - **EventsExtension** collect all services which implement **IEventMonitor** and make maps to callback methods from their tags.

```neon
services:
    order:
        class: App\Model\Order

    orderMailerMonitor:
        class: App\Model\OrderMailer
        tags: [
            event.create  = {callback: 'onOrderCreateEvent', context: 'App\Model\Order'}
        ]

    appLoggerMonitor:
        class: App\Model\Logs\App\Monitor
        tags: [
            event.init    = {callback: 'onEvent', context: '*'}
            event.create  = {callback: 'onEvent', context: '*'},
            event.read    = {callback: 'onEvent', context: '*'},
            event.update  = {callback: 'onEvent', context: '*'},
            event.delete  = {callback: 'onEvent', context: '*'}
        ]
```

### Create monitors and callback methods

```php
namespace App\Model\Logs\App;

use \TomasKubat\Nette\Events\IEventMonitor;

class Monitor extends \Nette\Object implements IEventMonitor
{

    public function __construct()
    {
        // init procedure...
    }

    public function onEvent(IEvent $event)
    {
        // log procedure... $event->getName(), $event->getContext(), $event->getParameters()  
    }

}
```

```php
namespace APP\Model;

use \TomasKubat\Nette\Events\IEventMonitor;

class OrderMailer extends \Nette\Object implements IEventMonitor

    public function __construct()
    {
        // init procedure...
    }

    public function onOrderCreateEvent(\TomasKubat\Nette\Events\IEvent $event)
    {
        // mail procedure... $event->getName(), $event->getContext(), $event->getParameters()
    }

}
```


### Fire events

Inject to your class ultra-simple **EventManager**.

```php
namespace App\Model\Order;

use \TomasKubat\Nette\Events\EventManager,
    \TomasKubat\Nette\Events\Event;

class EventCreator extends \Nette\Object
{

    /** @var EventManager */
    private $em;

    public function __construct(EventManager $em)
    {
        $this->em = $em;
    }
    
    public function save()
    {
        // save procedure
        
        $event = new Event('event.create', get_class(), ['orderId' => $order->id]);
        $this->em->push($event);
    }
```
