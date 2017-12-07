<div class='wrap'>
	<h1>Something goes here!</h1>
	<h2>Something else here!</h2>
	<h3> Even more stuff here!</h3>
	<h4>This is the smallest isn't it?</h4>
</div>
<div id='dc-test'></div>

<div id='dc-entry-container'>
<?php foreach ($entries as $entry):?>
	<pre>
		<?php print_r($entry);?>
	</pre>
<?php endforeach;?>
</div>