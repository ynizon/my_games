<ul>
<?php
$dir = "db";
$tabFiles = scandir($dir);
foreach ($tabFiles as $sFile){
	if ($sFile != "." and $sFile != ".." and strpos($sFile,".xml") !== false){
	?>
		<li><a target="_blank" href='<?php echo URL."/".$dir."/".$sFile;?>'><?php echo $sFile;?></a></li>
	<?php
	}
}
?>
</ul>