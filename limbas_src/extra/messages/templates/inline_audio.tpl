<?php
foreach(array("_self", "mbox", "uid", "part") as $var)
	$$var = $this->search($var);

$this->add("url", urlencode("{$_self}&mbox={$mbox}&uid={$uid}&part={$part}&stream=1"));
?>
<!-- [inline_audio.tpl] -->
<div id="olInlineAudio_container">
	<div id="olInlineAudio_status">
	<div id="olInlineAudio">[Inline Audio]</div>
		<div id="olInlineAudio_menu">
			<table border="0">
				<tr>
					<td><a id="olInlineAudio_play" href="#play"
						 onclick="player.play(); return false"><i
						 class="lmb-icon lmb-play-circle"
                                                 alt="play" border="0"></i></a>
						<a id="olInlineAudio_stop" href="#stop"
						 onclick="player.stop(); return false"><i
						 class="lmb-icon lmb-stop-cricle"
                                                 alt="stop" border="0"></i></a>
					</td>
					<td>${name}</td>
					<td><div id="olInlineAudio_message"></div></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript" language="javascript">//<!--
	_inline_audio = "${url}";
//--></script>
<!-- [/inline_audio.tpl] -->
