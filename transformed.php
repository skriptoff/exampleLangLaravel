<?php

// using string
function transformed($num) {
	$result = '';
	foreach (str_split($num) as $i){
		$result .= $i+1;
	}
	return (int) $result;
}

var_dump(transformed(991));
var_dump(transformed(982));
