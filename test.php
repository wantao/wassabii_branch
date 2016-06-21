<?php
	$A = "asdfasdf阿斯蒂芬-\'////]asdf?asdf aa#112$%!!8< asd >";
	$base64_encode_A = base64_encode($A);
	$encode_A = urlencode($A);
	$decode_base64_encode_A = urldecode($base64_encode_A);
	echo "encode_A:".$encode_A." base64_encode_A:".$base64_encode_A." decode_base64_encode_A:".$decode_base64_encode_A;
?>