<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


final class SitemapXmlRenderer implements SitemapRenderer
{

	/**
	 * @param SitemapItem[] $items
	 */
	public function render(array $items, bool $parted = false, int $pageOrder = 0, int $limit = 100): string
	{
		$urls = [];
		$iterator = 0;
		foreach ($items as $url) {
			if ($parted === true) {
				if ($iterator < $pageOrder * $limit) {
					$iterator++;
					continue;
				}

				$limit--;
				if ($limit < 0) {
					break;
				}
			}

			$urls[] = $url;
		}

		return $this->renderUrls($urls);
	}


	/**
	 * Safe way how to render sitemap to XML with special characters escaping.
	 *
	 * @param SitemapItem[] $urls
	 */
	private function renderUrls(array $urls): string
	{
		$return = [];
		foreach ($urls as $url) {
			$return[] = '<url>'
				. '<loc>' . htmlspecialchars($url->getUrl(), ENT_QUOTES) . '</loc>'
				. (($lastMod = $url->getLastModificationDate()) !== null
					? '<lastmod>' . htmlspecialchars($lastMod->format('Y-m-d\TH:i:sP'), ENT_QUOTES) . '</lastmod>'
					: ''
				) . '</url>';
		}

		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
			. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
			. implode("\n", $return) . "\n"
			. '</urlset>';
	}
}
