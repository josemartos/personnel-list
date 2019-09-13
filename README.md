# Personnel list - Headless CMS and React

> React, Webpack, Sass, PHP, WordPress

## Description

Simple React plugin to handle and visualize the members of a company. It contains also a Gutenberg Block to select members and show them on the public side of the site.

## Motivation

Try and experience the headless CMS concept, using one of the most well-known and used CMS in the market together with React on the Front-end.

## Notes

A basic REST API has been created to do CRUD operations, to experience the whole plugin life-cycle.

Wordpress-Plugin-Boilerplate has been used to follow best practises and folder structure.

React is the de facto JS library for its flexible and reusable component system, state management, wide adoption, etc. Besides, to work with Gutenberg is fundamental.

Axios has been used as HTTP client because it’s more straightforward than Fetch. A light JavaScript modal library has also been added.

The required styles have been whether self-written or combined with the WordPress existing ones. There was no need to use an aditional CSS framework.

The code is intended to be as self-descriptive as possible. Extra documentation can be added if needed.

## Installation

copy in `wp-content/plugins/personnel-list` and activate the plugin in the WordPress admin panel

composer:

```sh
composer install
```

### Development

in `./frontend`:

install front-end modules:

```sh
npm install
```

watch for changes:

```sh
npm run watch
```

compile front-end to production:

```sh
npm run build
```

### Requirements

Node.js 10+, WordPress 5+, PHP 7+, Composer 1.9

## To be improved

- Translations
- Validation and error handling
- UI design and tests
- List notifications, pagination, order, filters, etc.
- Company member image
- Template engine PHP

## References

- Plugin [Handbook](https://developer.wordpress.org/plugins/)
- Block Editor [Handbook](https://developer.wordpress.org/block-editor/)
