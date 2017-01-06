<?php
	include_once('StringFrequency.php');
	$sf = new StringFrequency;
	$sf->setSourceAddress('data-scientist');
	$sf->setExclusionList('exclusionlists/el-2.txt');
	$sf->setFilter('filters/f-1.txt');
	print_r($sf->getWordFrequencies());
?>