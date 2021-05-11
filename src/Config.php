<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


final class Config
{
	private string $cacheExpirationTime = '5 minutes';


	public function getCacheExpirationTime(): string
	{
		return $this->cacheExpirationTime;
	}


	public function setCacheExpirationTime(string $time): self
	{
		$time = strtolower(trim($time));
		if (!preg_match('/^(?:\d+\s+(?:seconds?|minutes?|hours?|days?|weeks?|months?|years?)(?:$|\s+))+$/', $time)) {
			throw new \InvalidArgumentException(
				'Expiration time "' . $time . '" is invalid. Did you mean format "5 minutes"?',
			);
		}

		$this->cacheExpirationTime = $time;

		return $this;
	}
}
