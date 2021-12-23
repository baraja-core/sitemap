<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


interface UrlLoader
{
	/**
	 * Load all URL items and return in simple scalar structure.
	 *
	 * @return array<int, array{
	 *     url?: string,
	 *     lastModificationDate?: \DateTimeInterface|null
	 * }>
	 */
	public function getSitemapItems(string $locale): array;
}
