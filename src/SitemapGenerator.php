<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


use Nette\Caching\Cache;
use Nette\Caching\Storage;

final class SitemapGenerator
{
	private Config $config;

	private ?Cache $cache;

	private ?UrlLoader $customUrlLoader = null;

	private ?SitemapRenderer $sitemapRenderer = null;


	public function __construct(
		?Storage $storage = null,
		private ?UrlLoader $commonUrlLoader = null
	) {
		$this->config = new Config;
		$this->cache = $storage === null ? null : new Cache($storage, 'sitemap');
	}


	public function generate(string $locale): string
	{
		$urlLoader = $this->customUrlLoader ?? $this->commonUrlLoader;
		if ($urlLoader === null) {
			throw new \LogicException('Sitemap URL loader is not defined. Did you implement URL loader for this project?');
		}

		$items = [];
		foreach ($urlLoader->getSitemapItems($locale) as $item) {
			if (isset($item['url']) === false) {
				throw new \RuntimeException('Invalid SitemapItem: Key "url" is required.');
			}
			$items[] = new SitemapItem(
				url: (string) $item['url'],
				lastModificationDate: $item['lastModificationDate'] ?? null,
			);
		}

		return ($this->sitemapRenderer ?? new SitemapXmlRenderer)->render(Paginator::process($items));
	}


	/**
	 * Get generated sitemap as XML.
	 * When sitemap does not exist, it will be generated to cache first.
	 *
	 * @throws SitemapException
	 */
	public function getSitemap(string $locale): string
	{
		$processLogic = function (string $locale): string {
			try {
				return $this->generate($locale);
			} catch (\RuntimeException $e) {
				throw $e;
			} catch (\Throwable $e) {
				throw new SitemapException('Can not create sitemap: ' . $e->getMessage(), 500, $e);
			}
		};

		if ($this->cache === null) {
			return $processLogic($locale);
		}
		$sitemap = $this->cache->load($key = 'sitemap.' . $locale . '.xml');
		if ($sitemap === null) {
			$sitemap = $processLogic($locale);
			$this->cache->save($key, $sitemap, [
				Cache::EXPIRE => $this->config->getCacheExpirationTime(),
				Cache::TAGS => ['sitemap-' . $locale, 'sitemap'],
			]);
		}

		return (string) $sitemap;
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
		if ($this->cache === null) {
			return;
		}
		$this->cache->clean(
			$locale === null
			? [Cache::ALL => true]
			: [Cache::TAGS => ['sitemap-' . $locale]],
		);
	}
}
