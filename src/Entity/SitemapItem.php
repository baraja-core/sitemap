<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


use Nette\Utils\Validators;

final class SitemapItem
{
	private string $url;

	private ?\DateTimeInterface $lastModificationDate;


	public function __construct(string $url, ?\DateTimeInterface $lastModificationDate = null)
	{
		if (Validators::isUrl($url) === false) {
			throw new \InvalidArgumentException(sprintf('Location "%s" is not valid absolute URL.', $url));
		}

		$this->url = $url;
		$this->lastModificationDate = $lastModificationDate;
	}


	public function getUrl(): string
	{
		return $this->url;
	}


	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}
}
