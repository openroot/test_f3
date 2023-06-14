<?php

namespace Controllers;

class IndexController {
	public function helloworldAction(\Base $fff, array $args = []): void {
		echo 'Hello World!! This is a '.$fff->VERB.'.';

		// URI example
		// http://localhost:4000/helloworld/D Tapader/34/Software Engineer
		echo '<br><br>Query string posted:';
		echo '<br>Name = '.$args['name'];
		echo '<br>Age = '.$args['age'];
		echo '<br>Profession = '.$args['profession'];
	}
}