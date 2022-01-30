<?php

namespace rdx\authgate;

use Illuminate\Support\ServiceProvider;

class PoliciesServiceProvider extends ServiceProvider {

	protected $policies = [];
	protected $useCache = true;
	protected $disableGuessPolicyNames = true;
	protected $cacheStoragePath = 'framework/abilities.php';

	public function register() {
		$this->app->singleton(PoliciesService::class);

		$this->commands([
			PoliciesCommand::class,
		]);
	}

	public function boot(PoliciesService $policies) {
		// $t = microtime(1);

		if ($this->disableGuessPolicyNames) {
			$policies->disableGuessPolicyNames();
		}

		$policies->setCachePath(storage_path($this->cacheStoragePath));
		$policies->setClasses($this->policies);
		$policies->defineAbilities($this->useCache);

		// $t = 1000 * (microtime(1) - $t);
	}

}
