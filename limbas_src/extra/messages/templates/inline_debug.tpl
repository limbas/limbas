<?php
foreach(array("_path", "mbox", "uid", "part") as $var)
	$$var = $this->search($var);
$this->add("url", urlencode("{$_path}attachments/stream.php?mbox={$mbox}&uid={$uid}&part={$part}"));
?>
<!-- [inline_debug.tpl] -->

name=${name}<br>
type=${type}<br>

<!--<pre><xmp><script type="text/javascript" language="javascript">
olFramework().load(_basePATH_ + 'classes/js/',
	['olMusicPlayer'], false, function(loaded_modules){ // onFrameworkLoaded
		player = new olMusicPlayer('olMusicPlayer', '${_path}attachments/olMusicPlayer.swf',
			"100%", 6, "${url}");
	});
</script></xmp></pre>-->
<!-- [/inline_debug.tpl] -->
