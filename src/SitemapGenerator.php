<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


use Nette\Caching\Cache;
use Nette\Caching\IStorage;

final class SitemapGenerator
{
	private Config $config;

	private Cache $cache;

	private ?UrlLoader $commonUrlLoader;

	private ?UrlLoader $customUrlLoader = null;

	private ?SitemapRenderer $sitemapRenderer = null;


	public function __construct(IStorage $storage, ?UrlLoader $urlLoader = null)
	{
		$this->config = new Config;
		$this->cache = new Cache($storage, 'sitemap');
		$this->commonUrlLoader = $urlLoader;
	}


	public function generate(string $locale): string
	{
		if (($urlLoader = $this->customUrlLoader ?? $this->commonUrlLoader) === null) {
			throw new \LogicException('Sitemap URL loader is not defined. Did you implement URL loader for this project?');
		}

		$items = [];
		foreach ($urlLoader->getSitemapItems($locale) as $item) {
			if (isset($item['url']) === false) {
				throw new \RuntimeException('Invalid SitemapItem: Key "url" is required.');
			}
			$items[] = new SitemapItem((string) $item['url'], $item['lastModificationDate'] ?? null);
		}

		return ($this->sitemapRenderer ?? new SitemapXmlRenderer)->render($items);
	}


	/**
	 * Get generated sitemap as XML.
	 * When sitemap does not exist, it will be generated to cache first.
	 *
	 * @throws SitemapException
	 */
	public function getSitemap(string $locale): string
	{
		if (($sitemap = $this->cache->load($key = 'sitemap.' . $locale . '.xml')) === null) {
			try {
				$this->cache->save($key, $sitemap = $this->generate($locale), [
					Cache::EXPIRE => $this->config->getCacheExpirationTime(),
					Cache::TAGS => ['sitemap-' . $locale, 'sitemap'],
				]);
			} catch (\RuntimeException $e) {
				throw $e;
			} catch (\Throwable $e) {
				throw new SitemapException('Can not create sitemap: ' . $e->getMessage(), 500, $e);
			}
		}

		return $sitemap;
	}


	public function setSitemapRenderer(SitemapRenderer $sitemapRenderer): void
	{
		$this->sitemapRenderer = $sitemapRenderer;
	}


	public function setSitemapUrlLoader(UrlLoader $urlLoader): void
	{
		$this->customUrlLoader = $urlLoader;
	}


	public function getConfig(): Config
	{
		return $this->config;
	}


	public function setConfig(Config $config): void
	{
		$this->config = $config;
	}


	public function clearCache(?string $locale = null): void
	{
		$this->cache->clean($locale === null
			? [Cache::ALL => true]
			: [Cache::TAGS => ['sitemap-' . $locale]]
		);
	}
}
