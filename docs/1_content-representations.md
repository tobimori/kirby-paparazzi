---
title: Content Representations
---

Content representations allow you to output the content in different formats. You can read more about them in the Kirby documentation.

## 1. Creating the template

For our image representation, let's create a file `default.png.php` in our `/site/templates` directory. This file contains the template that will later be rendered to an image by Puppeteer.

It works just like writing a normal HTML template.

Content representations use the same name as its HTML counterpart. This means, if we want to create an image for all `project` pages, our template name has to be `project.png.php`.

## 2. Creating the Paparazzi controller

Paparazzi uses controllers to inject its logic in your templates. Let's create a file with the same name as your template in our `/site/controllers` directory.

```php
<?php

use tobimori\Paparazzi\Controller;

return Controller::run(width: 1200, height: 630);
```

## 3. Adding content to the template

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

## 4. Admire the result in your browser

We can now open our page in the browser, and simply add `.png` to the URL. This will render our template to an image.

## 5. Usage with Kirby SEO (optional)

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
      'og:image' => $this->url() . '.png'
    ];
  }
}
```

Please note, that this will require kirby-seo v1.0.0 or higher.
