<?php

namespace rdx\authgate;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AbilityName {

	public function __construct(
		public string $name,
	) {}

}
