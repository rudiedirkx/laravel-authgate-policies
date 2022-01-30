<?php

namespace rdx\authgate;

use Illuminate\Console\Command;

class PoliciesCommand extends Command {

	protected $signature = 'auth:policies
		{--if-cached : Only cache if currenly cached}
		{--status : Show cached status}
		{--clean : Remove cache}
	';
	protected $description = "Show, cache or clean policies' abilities";

	protected $policies;

	protected function handleStatus() {
		if ($this->policies->isCached()) {
			$this->line("The policies file is cached.");
		}
		else {
			$this->line("The policies file is NOT cached.");
		}
	}

	protected function handleClean() {
		$this->policies->removeCache();
		if ($this->policies->isCached()) {
			$this->error("Cache could NOT be removed!");
			return 1;
		}
		else {
			$this->line("Cache removed.");
		}
	}

	protected function handleCache() {
		if ($this->option('if-cached') && !$this->policies->isCached()) {
			$this->line("Not re-cached, because of if-cached.");
			return;
		}

		$this->policies->createCache();
		if ($this->policies->isCached()) {
			$this->line("Cache created.");
		}
		else {
			$this->error("Cache could NOT be created!");
			return 1;
		}
	}

	public function handle(PoliciesService $policies) {
		$this->policies = $policies;

		if ($this->option('status')) {
			return $this->handleStatus();
		}
		elseif ($this->option('clean')) {
			return $this->handleClean();
		}
		else {
			return $this->handleCache();
		}
	}

}
