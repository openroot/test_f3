<?php

namespace operations;

class operation_index {
	public function helloworld_default(\Base $f3, array $args = []): void {
		echo 'Hello World!! This is a '.$f3->VERB.'.';

		// URI example
		// http://localhost:4000/helloworld/D Tapader/34/Software Engineer
		echo '<br><br>Query string posted:';
		echo '<br>Name = '.$args['name'];
		echo '<br>Age = '.$args['age'];
		echo '<br>Profession = '.$args['profession'];
	}
}