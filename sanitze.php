<?php

	function filterString($name){
			$name = str_replace("&","&amp", $name);
			$name = str_replace("\+","&plus", $name);
			$name = str_replace("\"","&quot", $name);
			$name = str_replace("//","&sol", $name);
			$name = str_replace("\\","&bsol;", $name);
			$name = str_replace("\'","&apos", $name);
			$name = str_replace(">","&gt", $name);
			$name = str_replace("<","&lt", $name);
			$name = str_replace("\%","&percnt", $name);
			$name = str_replace("\-","&lowbar", $name);
			$name = str_replace("\`","&grave", $name);
			$name = str_replace("\(","&lpar", $name);
			$name = str_replace("\)","&rpar", $name);
			$name = str_replace("\{","&lcub", $name);
			$name = str_replace("\}","&rcub", $name);
			$name = str_replace("\[","&lsqb", $name);
			$name = str_replace("\]","&rsqb", $name);
			$name = str_replace("\;","&semi", $name);
			return $name;
	}
?>