<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


interface UrlLoader
{
	/**
	 * Load all URL items and return in simple scalar structure:
	 * [
	 *    url (string),
	 *    lastModificationDate (\DateTime|null)
	 * ]
	 *
	 * @return mixed[][]
	 */
	public function getSitemapItems(string $locale): array;
}
