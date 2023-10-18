<?php

namespace tobimori\Paparazzi;

use Closure;
use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Site;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Http\Header;
use Spatie\Browsershot\Browsershot;

class Controller
{
  private Browsershot $instance;
  private string $cacheId;
  private string $type = 'png';

  /**
   * Returns the closure for use in a Kirby controller, without further configuration.
   */
  public static function run(int $width = 1200, int $height = 630, string $type = 'png'): Closure
  {
    return (new static($width, $height, $type))();
  }

  /**
   * Returns a instance of the controller, which can be configured further.
   * Call it at the end of the chain to return the closure for use in a Kirby controller.
   */
  public static function create(int $width = 1200, int $height = 630, string $type = 'png'): static
  {
    return new static($width, $height, $type);
  }

  private function __construct(int $width, int $height, string $type)
  {
    $this->instance = new Browsershot();
    $this->instance
      ->windowSize($width, $height)
      ->setScreenshotType($type);

    if (!in_array($type, ['png', 'jpeg'])) {
      throw new InvalidArgumentException('Invalid screenshot type');
    }
  }

  /**
   * Returns the closure for use in a Kirby controller
   */
  public function __invoke()
  {
    $controller = clone $this;

    return function (Page $page, Site $site, App $kirby) use ($controller) {
      $controller->handleRequest($controller, $page, $site, $kirby);
    };
  }

  /**
   * Handles the request
   */
  private function handleRequest(self $controller, Page $page, Site $site, App $kirby)
  {
    $cache = $kirby->cache('tobimori.paparazzi');
    $cacheId = $controller->cacheId($page, $kirby);

    if ($page->isCacheable() && $data = $cache->get($cacheId)) {
      Header::contentType("image/{$controller->type()}");
      echo base64_decode($data);
      die();
    }

    // Inject Kirby data into template
    $kirby->data = array_merge($kirby->data, [
      'kirby' => $kirby,
      'site' => $site,
      'pages' => $site->children(),
      'page' => $site->visit($page)
    ]);

    // Render template
    $template = $page->representation($controller->type());
    $html = $template->render($kirby->data);

    // Start Browsershot session, with rendered Html
    $data = $controller->instance()
      ->setHtml($html)
      ->screenshot();

    // Store screenshot as file, if using staticache
    if ($page->isCacheable() && $kirby->option('cache.pages.type') === 'static') {
      $kirby->cache('pages')->set($cacheId, [
        'html' => $data
      ],  0);
    } else {
      // Store screenshot in cache
      if ($page->isCacheable()) {
        $cache->set($cacheId, base64_encode($data), 0);
      }
    }

    Header::contentType("image/{$controller->type()}");
    echo $data;
    die(); // Prevent Kirby from rendering the page again
  }

  private function cacheId(Page $page, App $kirby)
  {
    if (isset($this->cacheId)) {
      return $this->cacheId;
    }

    // cache id is a protected function
    // so we have to replicate the behaviour
    $cacheId = [$page->id()];
    if ($kirby->multilang() === true) {
      $cacheId[] = $kirby->language()->code();
    }

    $cacheId[] = $this->type();
    $cacheId = implode('.', $cacheId);

    return $this->cacheId = $cacheId;
  }

  /**
   * Magic caller for the Browsershot instance
   */
  public function __call(string $method, array $arguments)
  {
    if (method_exists($this, $method)) {
      return $this->$method(...$arguments);
    }

    if (method_exists($this->instance, $method)) {
      $this->instance->$method(...$arguments);
    }

    return $this;
  }

  /**
   * Returns the Browsershot instance or runs a callback on it
   */
  public function instance(callable $fn = null): Browsershot
  {
    if ($fn) {
      $fn($this->instance);
    }

    return $this->instance;
  }

  /**
   * Returns the screenshot type
   */
  public function type(): string
  {
    return $this->type;
  }
}
