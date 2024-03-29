<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


interface SitemapRenderer
{
	/** @param SitemapItem[] $items */
	public function render(array $items): string;
}
