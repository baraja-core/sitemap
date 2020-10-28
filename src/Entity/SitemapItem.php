<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


use Nette\Utils\Validators;

final class SitemapItem
{
	private string $url;

	private ?\DateTime $lastModificationDate;


	public function __construct(string $url, ?\DateTime $lastModificationDate = null)
	{
		if (Validators::isUrl($url) === false) {
			throw new \InvalidArgumentException('Location "' . $url . '" is not valid absolute URL.');
		}

		$this->url = $url;
		$this->lastModificationDate = $lastModificationDate;
	}


	public function getUrl(): string
	{
		return $this->url;
	}


	public function getLastModificationDate(): ?\DateTime
	{
		return $this->lastModificationDate;
	}
}
