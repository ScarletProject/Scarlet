<?php
include '../Scarlet.php';

$args = array(
	'src' => '/Javascript/jquery/jquery.js'
);


echo htmlspecialchars(S('javascript')->args($args));

?>