# Events

Simple events for Nette.

## Content

- [Usage - how to register](#usage)
  - [Register events](#register-events)
  - [Register lazy events](#register-lazy-events)
  - [Fire events](#fire-events)

## Usage

### Register extension

Register in your config file (e.q. config.neon).

```neon
extensions:
    events: Contributte\DummyEvents\DI\EventsExtension
```

### Register events

On Container compile - **EventsExtension** collect all services which implement **EventsSubscriber** and call their `onEvents($em)` method.

```php
use Contributte\DummyEvents\EventsSubscriber;
use Contributte\DummyEvents\EventsManager;

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
use Contributte\DummyEvents\EventsSubscriber;
use Contributte\DummyEvents\EventsManager;

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
use Contributte\DummyEvents\EventsManager;

/** @var EventsManager @inject **/
public $em;

public function save() {
    // Some logic..
    
    // Fire order update events
    $this->em->trigger('order.update', $order->state);
}
```

