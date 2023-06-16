<?php

namespace operations;

class operation_index {
	public function helloworld_default(\Base $f3): void {
		echo 'Hello World! This is a '.$f3->VERB.'.';

		// URI example
		// http://localhost:4000/helloworld/D Tapader/34/Software Engineer
		echo '<br><br>Query string posted:';
		echo '<br>Name = '.$f3['PARAMS.name'];
		echo '<br>Age = '.$f3['PARAMS.age'];
		echo '<br>Profession = '.$f3['PARAMS.profession'];
	}
}