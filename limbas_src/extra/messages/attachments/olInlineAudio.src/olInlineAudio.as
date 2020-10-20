

package
{
	import flash.media.ID3Info;
	import flash.display.Loader;
	import flash.display.LoaderInfo;
	import flash.display.Sprite;
	import flash.external.ExternalInterface;
	import flash.events.*;
	import flash.display.Stage;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.media.SoundLoaderContext;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextField;
	import flash.media.Sound;
	import flash.media.SoundChannel;
	import flash.media.SoundTransform;
		
	[SWF(backgroundColor="#f09000", frameRate="24", width="320", height="6")]
	
	public class olInlineAudio extends Sprite
	{
		protected var baseurl:String = "playlist.php";
		protected var playlist:XML;
		protected var has_external_interface:Boolean = true;
		protected var txtStatus:TextField;
		private var mp3:Sound;
		private var mp3ch:SoundChannel;
		private var mp3tr:SoundTransform;
		private var current_idx:int = 0 ;

		private var /*progressLoad:Sprite, */progressPlay:Sprite;
		
		public function olInlineAudio()
		{
			var param:Object = LoaderInfo(this.root.loaderInfo).parameters;
			var xmlLoader:URLLoader = new URLLoader();

			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.align  = StageAlign.TOP_LEFT;

			if (param.url != null)
				baseurl = param.url;
				
			txtStatus = new TextField();
			txtStatus.autoSize = TextFieldAutoSize.LEFT;
			txtStatus.x = txtStatus.y = 0;
			addChild(txtStatus);

			//progressLoad = ui_progressbar(0x909090);
			//addChild(progressLoad);
			
			addEventListener(MouseEvent.MOUSE_DOWN,
				function (e:MouseEvent):void {
					var loadTime:Number = mp3.bytesLoaded / mp3.bytesTotal;
					var estimatedLength:int = Math.ceil(mp3.length / (loadTime));
					playmp3(e.stageX / stage.stageWidth * estimatedLength);
				}
			);
			
			progressPlay = ui_progressbar(0x000090);
			addChild(progressPlay);
	
			try	{
				ExternalInterface.addCallback("command", ei_command);
				ExternalInterface.marshallExceptions = true;
			} catch (e:Error) {
				/* we are not being called within a webpage! */
				has_external_interface = false;
			}
			
			mp3 = new Sound();
			
			addEventListener(Event.ENTER_FRAME, enterFrameHandler);
				if (has_external_interface)
					ExternalInterface.call('onPlayerReady', 0);
		}

		private function ui_progressbar(color:uint):Sprite {
			var ret:Sprite = new Sprite();
			ret.x= ret.y= 0;
			ret.graphics.beginFill(color);
			ret.graphics.drawRect(0, 0, stage.stageWidth,
				(has_external_interface) ? stage.stageHeight : 8);
			ret.graphics.endFill();
			ret.scaleX = 0;
			return ret;
		}

        private function enterFrameHandler(event:Event):void {    
            /* var loadTime:Number = mp3.bytesLoaded / mp3.bytesTotal;
            var estimatedLength:int = Math.ceil(mp3.length / (loadTime));
			txtStatus.text = 'mp3.length='+mp3.length+"\n"+
				'mp3.bytesLoaded='+mp3.bytesLoaded;

			progressLoad.scaleX = loadTime; */
			progressPlay.scaleX = (mp3ch.position / mp3.length);
        }

		public function ei_command(cmd:String, param:String):void
		{
			try	{
				switch(cmd)	{
					case 'play':
						playmp3(0);
						break;
					default: // stop
						try {
							mp3ch.stop();
							mp3.close();
						} catch (e:Error) {}
				}
			} catch (e:Error) {
				print_message(e.toString());
			}
		}
		
		public function playmp3(pos:Number):void
		{
			var req:URLRequest, title:String;
			
			print_message("loading...");
			req = new URLRequest(baseurl);

			ei_command('stop', '');
			var ctx:SoundLoaderContext = new SoundLoaderContext(5000);
			mp3 = new Sound(req, ctx);
			//mp3.load();
			
			/* handle HTTP Status Codes */
			mp3.addEventListener(HTTPStatusEvent.HTTP_STATUS,
				function (e:HTTPStatusEvent):void {
					var code:int = HTTPStatusEvent(e).status;
					if (code && (code!=200))
						print_message(e.type + " (" + code + ") for " + title );
					
				} // anonymous function
			); // ErrorEvent.ERROR

			mp3.addEventListener(ErrorEvent.ERROR,
				function (e:ErrorEvent):void {
					print_message(e.type + " (" + e.text + ") for " + title );
				}
			);

		
			mp3.addEventListener(IOErrorEvent.IO_ERROR,
				function (e:IOErrorEvent):void {
					print_message(e.type + " (" + e.text + ") for " + title );
				}
			);

			/*mp3.addEventListener(Event.COMPLETE,
				function (e:Event):void { 
			*/
					print_message("playing...");
					mp3ch = mp3.play(pos);
					mp3.addEventListener(Event.ID3,
						function (evt:Event):void {
							var myID3:ID3Info = evt.target.id3;
							print_message("playing "+myID3.artist +":"+ myID3.songName + " ("+ myID3.album+")...");   
						}
					);

					mp3tr = mp3ch.soundTransform;
					mp3tr.volume = .97;
					mp3ch.soundTransform = mp3tr;
					

					mp3ch.addEventListener(Event.SOUND_COMPLETE, mp3_complete);
			/*
				} 
			);*/
		}
		
		private function mp3_complete(evt:Event):void {
			mp3ch.removeEventListener(Event.SOUND_COMPLETE, mp3_complete, true);
			//playmp3(0);
		}

		public function print_message(param:String):void
		{
			if (has_external_interface)
				ExternalInterface.call('onMessage', param);
			else
				txtStatus.text = param;
		}

	}
	
}
