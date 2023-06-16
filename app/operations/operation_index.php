<?php
namespace operations;

class operation_index {
	public function helloworld_default(\Base $f3): void {
		echo '<html><head><title>Test F3</title></head><body>';
		echo '<div id="header"><h2>'.$f3->site. '</h2></div>';

		echo '<div id="content">';
		echo '<pre>This Route: '.$f3['ALIASES.index_helloworld'].'</pre>';
		echo '<p>Hello World! This is a `'.$f3->VERB.'` verb.<br><br>';
		echo 'Query string posted:';
		echo '<pre>Name = '.$f3['PARAMS.name']. '</pre>';
		echo '<pre>Age = '.$f3['PARAMS.age'] . '</pre>';
		echo '<pre>Profession = '.$f3['PARAMS.profession'] . '</pre></p>';
		echo '</div>';

		echo '<div id="footer"><h2>This site is powered by <a href="http://fatfree.sourceforge.net">F3</a> - the common sense PHP framework</h2></div>';

		echo '</body></html>';
	}
}