# Kirby Paparazzi

A toolkit for programatically creating images with Kirby, by simply writing HTML, JS & CSS code. Under the hood, it uses [Browsershot](https://github.com/spatie/browsershot/) & [Puppeteer](https://github.com/puppeteer/puppeteer).

> **NOTE**
>
> This is a work in progress plugin. API & usage may change at any time.

## Prerequisites

- [Kirby 4.0+](https://getkirby.com/releases/4.0)
- Composer for plugin installation
- [A working installation of Puppeteer](https://spatie.be/docs/browsershot/v2/requirements)

## Installation

```
composer require tobimori/kirby-paparazzi
```

Non-composer installation methods are not supported.

## Usage

Currently, Paparazzi supports the following ways of creating images:

### Content representations

#### 1. Creating the template

For our image representation, let's create a file `default.png.php` in our `/site/templates` directory. This file contains the template that will later be rendered to an image by Puppeteer.

It works just like writing a normal HTML template.

Content representations use the same name as its HTML counterpart. This means, if we want to create an image for all `project` pages, our template name has to be `project.png.php`.

#### 2. Creating the Paparazzi controller

Paparazzi uses controllers to inject its logic in your templates. Let's create a file with the same name as your template in our `/site/controllers` directory.

```php
<?php

use tobimori\Paparazzi\Controller;

return Controller::create([
  'width' => 1200,
  'height' => 630
]);
```

#### 3. Adding content to the template

Now, let's add some content to our template. We can use all the Kirby variables & helpers just like in a normal template.

Our example template could look something like this:

```php
<h1><?= $page->title() ?></h1>
<img src="<?= $page->image()->url() ?>">

<?php foreach ($page->children()->listed() as $child): ?>
  <h2><?= $child->title() ?></h2>
  <p><?= $child->text()->excerpt(50) ?></p>
<?php endforeach ?>
```

#### 4. Admire the result in your browser

We can now open our page in the browser, and simply add `.png` to the URL. This will render our template to an image.

#### 5. Usage with [kirby-seo](https://github.com/tobimori/kirby-seo) (optional)

If you're using my kirby-seo plugin, and you want to use the image as Open Graph image, you can use the programmatic defaults feature for that.

1. Create a page model, e.g. `site/models/project.php`. You can read more about [Page models in the Kirby Guide](https://getkirby.com/docs/guide/templates/page-models).
2. Add a `metaDefaults()` function that returns an array with the Open Graph image URL, like so:

```php
<?php

use Kirby\Cms\Page;

class ProjectPage extends Page
{
  public function metaDefaults()
  {
    return [
      'ogImage' => $this->url() . '.png'
    ];
  }
}
```

Please note, that this will require kirby-seo v0.4.0 or higher.

## Limitations

### Using the PHPs built-in server

**The built-in PHP server is not capable of accepting more than 1 request at once.**
If you're loading assets or images from your server, the request will time out as Puppeteer will never go into idle state.
You'll have to use a proper webserver like Apache or Nginx in development for this to work. I recommend [Laravel Valet](https://laravel.com/docs/10.x/valet).

## Roadmap

This plugin is highly specific and mostly growing with my needs. New feature are usually only added when I need for my own projects.
I’ll only consider working on funded feature requests. This doesn't mean the plugin is deprecated, it will be actively maintained.

## Support

> This plugin is provided free of charge & published under the permissive MIT License. If you use it in a commercial project, please consider to [sponsor me on GitHub](https://github.com/sponsors/tobimori) to support further development and continued maintenance of my plugins.

## License

[MIT License](./LICENSE)
Copyright © 2023 Tobias Möritz
