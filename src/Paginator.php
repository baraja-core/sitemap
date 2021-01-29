<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


final class Paginator
{
	/** @throws \Error */
	public function __construct()
	{
		throw new \Error('Class ' . static::class . ' is static and cannot be instantiated.');
	}


	/**
	 * @param SitemapItem[] $items
	 * @return SitemapItem[]
	 */
	public static function process(array $items, bool $parted = false, int $page = 0, int $limit = 100): array
	{
		$urls = [];
		$iterator = 0;
		foreach ($items as $url) {
			if ($parted === true) {
				if ($iterator < $page * $limit) {
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

		return $urls;
	}
}
