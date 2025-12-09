![](https://heatbadger.vercel.app/github/readme/contributte/dummy-events/?deprecated=1)

<p align=center>
    <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
    <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
    <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
    Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

## Disclaimer

| :warning: | This project is no longer being maintained. Please use [contributte/event-dispatcher](https://github.com/contributte/event-dispatcher).
|---|---|

| Composer | [`contributte/dummy-events`](https://packagist.org/packages/contributte/dummy-events) |
|---| --- |
| Version | ![](https://badgen.net/packagist/v/contributte/dummy-events) |
| PHP | ![](https://badgen.net/packagist/php/contributte/dummy-events) |
| License | ![](https://badgen.net/github/license/contributte/dummy-events) |

## Documentation

## Usage

### Register extension

Register in your config file (e.q. config.neon).

```yaml
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

```yaml
services:
    {class: TestService, tags: [event.order.update]}
```

Or use tag arrays with key name **events**.

```yaml
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



## Development

This package was maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
