<?php
namespace X\Tool;

trait urlSafe 
{
	protected function urlSafeB64Encode($data){return str_replace(['+', '/', '\r', '\n', '='], ['-', '_'], base64_encode($data));}
	protected function urlSafeB64Decode($b64){return base64_decode(str_replace( ['-', '_'], ['+', '/'], $b64));}	
}
?>