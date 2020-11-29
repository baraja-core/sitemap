<?php

declare(strict_types=1);

namespace Baraja\Sitemap;


use Baraja\Localization\Localization;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Http\Response;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class SitemapExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'cacheExpirationTime' => Expect::string(),
			'route' => Expect::string()->default('sitemap.xml'),
			'urlLoader' => Expect::string(),
		])->castTo('array');
	}


	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('sitemapXmlRenderer'))
			->setFactory(SitemapXmlRenderer::class)
			->setAutowired(SitemapXmlRenderer::class);

		$generator = $builder->addDefinition($this->prefix('sitemapGenerator'))
			->setFactory(SitemapGenerator::class)
			->setAutowired(SitemapGenerator::class)
			->addSetup('?->setSitemapRenderer(?)', ['@self', '@' . SitemapXmlRenderer::class]);

		$configValidator = new Config;
		if (isset($this->config['cacheExpirationTime']) === true) {
			if ($configValidator->setCacheExpirationTime((string) $this->config['cacheExpirationTime'])->getCacheExpirationTime() !== $this->config['cacheExpirationTime']) {
				throw new \RuntimeException('Cache expiration time "' . $this->config['cacheExpirationTime'] . '" is not valid.');
			}
			$generator->addSetup('?->getConfig()->setCacheExpirationTime(?)', ['@self', $this->config['cacheExpirationTime']]);
		}

		if (isset($this->config['urlLoader']) === true) {
			$urlLoader = $builder->getDefinitionByType($this->config['urlLoader']);
			$generator->addSetup('?->setSitemapUrlLoader(?)', ['@self', '@' . $urlLoader->getType()]);
		}
	}


	public function afterCompile(ClassType $class): void
	{
		if (PHP_SAPI === 'cli') {
			return;
		}

		/** @var ServiceDefinition $generator */
		$generator = $this->getContainerBuilder()->getDefinitionByType(SitemapGenerator::class);

		/** @var ServiceDefinition $response */
		$response = $this->getContainerBuilder()->getDefinitionByType(Response::class);

		/** @var ServiceDefinition $localization */
		$localization = $this->getContainerBuilder()->getDefinitionByType(Localization::class);

		$class->getMethod('initialize')->addBody(
			'// sitemap.' . "\n"
			. '(function () {' . "\n"
			. "\t" . 'if ($this->getService(\'http.request\')->getUrl()->getRelativeUrl() === ?) {' . "\n"
			. "\t\t" . '$sitemap = $this->getService(?)->getSitemap($this->getService(?)->getLocale());' . "\n"
			. "\t\t" . '$this->getService(?)->setHeader(\'Content-type\', \'text/xml; charset=utf-8\');' . "\n"
			. "\t\t" . 'echo $sitemap;' . "\n"
			. "\t\t" . 'die;' . "\n"
			. "\t" . '}' . "\n"
			. '})();', [
				$this->config['route'],
				$generator->getName(),
				$localization->getName(),
				$response->getName(),
			]
		);
	}
}
