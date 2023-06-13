<?php

namespace Controllers;

class IndexController {
	public function helloworldAction(\Base $fff, array $args = []): void {
		echo 'Hello World!! This is a '.$fff->VERB.'.';
	}
}