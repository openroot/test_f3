<?php

namespace operations;

class operation_index {
	public function helloworld_default(\Base $f3): void {
		echo '<pre>This route: '.$f3['ALIASES.index_helloworld'].'</pre>';

		echo 'Hello World! This is a `'.$f3->VERB.'` verb.';

		// URI example
		// http://localhost:4000/helloworld/D Tapader/34/Software Engineer
		echo '<br><br>Query string posted:';
		echo '<br>Name = '.$f3['PARAMS.name'];
		echo '<br>Age = '.$f3['PARAMS.age'];
		echo '<br>Profession = '.$f3['PARAMS.profession'];
	}
}