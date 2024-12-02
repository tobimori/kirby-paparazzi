---
title: Installation
---

## Prerequisites

- [Kirby 4.0+](https://getkirby.com/releases/4.0)
- Composer for plugin installation
- [A working installation of Puppeteer](https://spatie.be/docs/browsershot/v2/requirements)

## Installation

```
composer require tobimori/kirby-paparazzi
```

Non-composer installation methods are not supported.

> **NOTE**
>
> This is a work in progress plugin. API & usage may change at any time.

## Limitations

### Using the PHPs built-in server

**The built-in PHP server is by default not capable of accepting more than 1 request at once.**
If you're loading assets or images from your server, the request will time out as Puppeteer will never go into idle state.
You'll have to use a proper webserver like Apache or Nginx in development for this, or set the `PHP_CLI_SERVER_WORKERS` environment variable to something bigger than 1.

## Roadmap

This plugin is highly specific and mostly growing with my needs. New feature are usually only added when I need for my own projects.
I’ll only consider working on funded feature requests. This doesn't mean the plugin is deprecated, it will be actively maintained.
