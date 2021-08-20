Sitemap generator
=================

![Integrity check](https://github.com/baraja-core/sitemap/workflows/Integrity%20check/badge.svg)

Simple sitemap generator with robust and performance implementation.

- Generates a standardized site map in XML format,
- Allows custom implementation of UrlLoader and specification of custom link sources,
- The generated map is automatically cached and updated without the need to use cron.

📦 Installation
---------------

It's best to use [Composer](https://getcomposer.org) for installation, and you can also find the package on
[Packagist](https://packagist.org/packages/baraja-core/sitemap) and
[GitHub](https://github.com/baraja-core/sitemap).

To install, simply use the command:

```shell
$ composer require baraja-core/sitemap
```

You can use the package manually by creating an instance of the internal classes, or register a DIC extension to link the services directly to the Nette Framework.

Basic description
------------------

The package automatically generates a site map as a `sitemap.xml` file with the following structure:

```xml
<urlset xmlns = "http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>
            https://... // Here will be the whole absolute path
        </loc>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
</urlset>
```

URLs are retrieved from the abstract `SitemapUrlLoader` service, which can be overloaded and otherwise implemented. UrlLoader returns the entity field `SitemapItems[]` with the `getUrls()` method.

It adds its own presenter to the administration, which shows the current form and structure of the file.

Installation
------------

Composer:

```shel
$ composer require baraja-core/sitemap
```

The routing rule and services are registered automatically.

After installation, you must define an UrlLoader in the package, which gets a list of all available URLs and passes it on for rendering. You can use another existing library as the UrlLoader.

Getting a list of URLs
----------------------

The package does not include the default implementation of UrlLoader, and each project must implement it itself.

The class must contain a public `getUrls()` method that returns an array of `SitemapItem[]` instances.

Getting sitemap + cache
-----------------------

The package does not create any physical sitemap file, because it runs a PHP script with each request. In order not to always have to perform complex mapping, the package itself will use a cache with a default validity of 5 minutes.

The cache length setting can be affected by the `neon` configuration file:

```yaml
sitemap:
    cacheExpirationTime: '20 minutes'
```

📄 License
-----------

`baraja-core/sitemap` is licensed under the MIT license. See the [LICENSE](https://github.com/baraja-core/sitemap/blob/master/LICENSE) file for more details.
