<?php

namespace rdx\authgate;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class PoliciesService {

	public $gate;
	public $cacheFile = null;
	public $policyClasses = [];

	public function __construct(Gate $gate) {
		$this->gate = $gate;
	}

	public function setClasses(array $classes) : void {
		$this->policyClasses = $classes;
	}

	public function setCachePath(string $filepath) : void {
		$this->cacheFile = $filepath;
	}

	public function disableGuessPolicyNames() : void {
		$this->gate->guessPolicyNamesUsing(function($class) {
			return null;
		});
	}

	public function isCached() : bool {
		return file_exists($this->cacheFile);
	}

	public function removeCache() : void {
		if ($this->isCached()) {
			unlink($this->cacheFile);
		}
	}

	public function createCache() : void {
		file_put_contents($this->cacheFile, $this->makeCacheCode($this->getLiveAbilities()));
	}

	public function defineAbilities(bool $useCache = true) : void {
		$abilities = $this->getAbilities($useCache);
		foreach ($abilities as $ability => $callable) {
			$this->gate->define($ability, $callable);
		}
	}

	public function getAbilities(bool $useCache = true) : array {
		if ($useCache && $this->isCached()) {
			return require $this->cacheFile;
		}
		return $this->getLiveAbilities();
	}

	public function getLiveAbilities() : array {
		$abilities = [];
		foreach ($this->policyClasses as $class) {
			$reflected = new ReflectionClass($class);
			foreach ($reflected->getMethods() as $method) {
				if ($this->includeMethod($method)) {
					$abilities[$this->getAbilityName($method)] = $class . '@' . $method->getName();
				}
			}
		}
		return $abilities;
	}

	protected function getAbilityName(ReflectionMethod $method) : string {
		return Str::kebab(str_replace('_', '-', $method->getName()));
	}

	protected function includeMethod(ReflectionMethod $method) : bool {
		return $method->isPublic() && strpos($method->getName(), '__') !== 0;
	}

	protected function makeCacheCode(array $abilities) : string {
		return "<?php\n\nreturn " . var_export($abilities, true) . ";\n";
	}

}
