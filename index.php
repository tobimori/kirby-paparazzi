<?php


use Kirby\Cms\App;

@include_once __DIR__ . '/vendor/autoload.php';

if (
  version_compare(App::version() ?? '0.0.0', '4.0.0-beta.2', '<') === true ||
  version_compare(App::version() ?? '0.0.0', '5.0.0', '>') === true
) {
  throw new Exception('Kirby Paparazzi requires Kirby 4');
}

App::plugin('tobimori/paparazzi', [
  'options' => [
    'cache' => true
  ]
]);
