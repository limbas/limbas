package {
	import flash.accessibility.Accessibility;
	import flash.errors.IOError;
	import flash.geom.Rectangle;
	import flash.media.Sound;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.external.ExternalInterface;
	import flash.utils.Timer;
	import flash.text.TextFieldAutoSize;

	import flash.display.*;
	import flash.events.*
	import flash.net.*;
	import flash.display.SimpleButton;
    import flash.display.Sprite;
    import flash.events.*;
    import flash.net.FileReference;
    import flash.net.FileReferenceList;
 
    public class olUpload extends Sprite {
        public static var LIST_COMPLETE:String = "listComplete";
        public static var LIST_CANCEL:String = "listCancel";
		
		public var url:String = "http://192.168.178.77/imap/test/"; /* index.php";*/
		public var maxlen:int = (4 * 1024 * 1024);
		public var whole_size:uint = 0;
		public var done_size:uint = 0;
		
		private var ftypes:String = "*.*";
		private var status:TextField;
		private var prg:Sprite;
		private var has_external_interface:Boolean = true;
		private var has_debug_interface:Boolean = true;
	
		private var selectButton:SimpleButton;

		public var session_id:String = "";
		
        public function updateProgress():void {
			prg.scaleX = done_size / whole_size  * stage.stageWidth;
			print_message("upload_status", (done_size / whole_size * 100) + "", "0");
		}
        public function updateProgress2(total:uint, loaded:uint):void {
			prg.scaleX = (done_size - total + loaded) / whole_size  * stage.stageWidth;
			print_message("upload_status", ((done_size - total + loaded) / whole_size * 100) + "", "0");
		}
        public function olUpload() {
			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.align  = StageAlign.TOP_LEFT;
			
/* overwrite default values */
			var param:Object = LoaderInfo(this.root.loaderInfo).parameters;
				
			if (param.url != null)
				this.url = param.url;

			if (param.ftypes != null)
				this.ftypes = param.ftypes;

			if (param.maxlen != null)
				this.maxlen = int(param.maxlen);

			if (param.debug != null)
				this.has_debug_interface = true;
				
			if (param.sid != null)
				this.session_id = param.sid;
			
/* initialize UI */
			var btn_txt:TextField;
			btn_txt = new TextField();
			btn_txt.autoSize = TextFieldAutoSize.CENTER;

			var tf:TextFormat;
			tf = new TextFormat();
			tf.font = "Arial";
			btn_txt.defaultTextFormat = tf;
					
			btn_txt.htmlText = "Datei(en) anhängen...";
			btn_txt.mouseEnabled = true;
			btn_txt.x = btn_txt.y = 1;

			var btn_spr:Sprite;
			btn_spr = new Sprite();
			btn_spr.graphics.beginFill (0xcccccc, 1);
			btn_spr.graphics.lineStyle(1, 0x000000);
            btn_spr.graphics.drawRect (0, 0, btn_txt.width + 2, btn_txt.height + 2);
			
            btn_spr.graphics.endFill();
			btn_spr.x = 0;
			btn_spr.addChild(btn_txt);
			
			selectButton = new SimpleButton(btn_spr,btn_spr,btn_spr,btn_spr);
			selectButton.width = 140;
			selectButton.x = (stage.stageWidth - selectButton.width) / 2;
			selectButton.addEventListener(MouseEvent.CLICK, initiateFileUpload);
			selectButton.visible = true;
			addChild(selectButton);

			prg = new Sprite();
			prg.graphics.beginFill(0x0000C0);
			prg.graphics.drawRect(0, 0, 1, selectButton.height);
			prg.graphics.endFill();
			prg.scaleX = 0;	prg.scaleY = 1;
			addChild(prg);

			status = new TextField();
			status.autoSize = TextFieldAutoSize.LEFT;
			status.x = 0;
			status.y = selectButton.height;

			addChild(status);

			/* check if called within a webpage */
			try	{
				ExternalInterface.addCallback("dummy", eiDummy); }
			catch (e:Error)	{
				has_external_interface = false;
			}
          
        }

        private function initiateFileUpload(e:MouseEvent):void {
            var fileRef:CustomFileReferenceList = new CustomFileReferenceList(this);
            fileRef.addEventListener(olUpload.LIST_COMPLETE, listCompleteHandler);
            fileRef.addEventListener(olUpload.LIST_CANCEL, listCompleteHandler);

			selectButton.visible = false;
			prg.visible = true;
			prg.scaleX = 0;

			print_message("upload_browse", "0", "0");
            fileRef.browse(fileRef.getTypes(ftypes));
        }

		public function eiDummy():void {
		}

		public function print_message(type:String, paramA:String, paramB:String):void
		{
			if (type != "upload_info"){
				if (has_external_interface){
					if (has_debug_interface)
						status.appendText(type + ': ' + paramA + ' ' + paramB + '\n');
					ExternalInterface.call(type, paramA, paramB);
					return;
				}
			} else
				if (has_debug_interface)
						status.appendText(type + ': ' + paramA + ' ' + paramB + '\n');
			 
			if (has_debug_interface || !has_external_interface)
			{
				status.appendText(type + ': ' + paramA + ' ' + paramB + '\n');
			}
		}

        private function listCompleteHandler(event:Event):void {
			print_message("upload_finished", (done_size == whole_size) ? "0" : "1", 
				done_size + '/' + whole_size + ' bytes hochgeladen.');
			selectButton.visible = true;
			prg.visible = false;
        }
    }
}
import flash.events.*;
import flash.net.FileReference;
import flash.net.FileReferenceList;
import flash.net.FileFilter;
import flash.net.URLRequest;
import flash.net.URLLoaderDataFormat;
import flash.net.URLRequestMethod;
import flash.net.URLLoader;
import flash.net.URLVariables;


class CustomFileReferenceList extends FileReferenceList {
    private var uploadURL:URLRequest;
    private var pendingFiles:Array;
	private var caller:Object;
	
    public function CustomFileReferenceList(caller:Object) {
        uploadURL = new URLRequest();

        uploadURL.url = caller.url;
		this.caller = caller;
		
        addEventListener(Event.SELECT, selectHandler);
        addEventListener(Event.CANCEL, cancelHandler);
    }

    public function getTypes(ftypes:String):Array {
        var allTypes:Array = new Array();
        allTypes.push(new FileFilter("Bilder", ftypes));
        return allTypes;
    }
 
    private function do_listComplete():void {
        var event:Event = new Event(olUpload.LIST_COMPLETE);
        dispatchEvent(event);
    }
 
    private function do_listCancel():void {
        var event:Event = new Event(olUpload.LIST_CANCEL);
        dispatchEvent(event);
    }
 
    private function removePendingFile(file:FileReference):String {
        for (var i:uint; i < pendingFiles.length; i++) {
            if (pendingFiles[i].name == file.name) {
				var realname:String = pendingFiles[i].realname;
                pendingFiles.splice(i, 1);
                if (pendingFiles.length == 0)
                    do_listComplete();
                return realname;
            }
        }
		return null;
    }
	
    private function get_realname(file:FileReference):String {
        for (var i:uint; i < pendingFiles.length; i++) {
            if (pendingFiles[i].name == file.name)
                return pendingFiles[i].realname;
        }
		return file.name;
    }
 
    private function selectHandler(event:Event):void {
        pendingFiles = new Array();
        var file:FileReference;

		this.caller.print_message("upload_start", fileList.length.toString(), 0);

        for (var i:uint = 0; i < fileList.length; i++) {
            file = FileReference(fileList[i]);
            addPendingFile(file);
        }
    }
 
    private function cancelHandler(event:Event):void {
        var file:FileReference = FileReference(event.target);
		do_listCancel();
    }
 
    private function openHandler(event:Event):void {
        var file:FileReference = FileReference(event.target);
		var realname:String = get_realname(file);
//		caller.print_message("upload_info", realname, "openHandler" );
    }
 
    private function progressHandler(event:ProgressEvent):void {
//		var file:FileReference = FileReference(event.target);
//		var realname:String = get_realname(file);
		caller.updateProgress2(event.bytesTotal, event.bytesLoaded);
//		caller.print_message("upload_info", realname, event.bytesLoaded + "/" + event.bytesTotal + " bytes...");
    }
 
    private function completeHandler(event:Event):void {
        var file:FileReference = FileReference(event.target);
		var realname:String = get_realname(file);
		caller.done_size += file.size;
		caller.updateProgress();

		caller.print_message("upload_complete", realname, file.size.toString());
		removePendingFile(file);
	}
	
	private function httpStatusHandler(event:HTTPStatusEvent):void {
        var file:FileReference = FileReference(event.target);
		var realname:String = get_realname(file);
		if((event.status == 200)||(event.status == 0))
			return;
			
		caller.print_message("upload_error", "Die Datei "+realname+" wurde vom Server nicht akzeptiert", 
		"(HTTP/1.1 " + event.status.toString() + ")");

		//caller.print_message("upload_info", realname, "ioErrorHandler: " + event.text);
		removePendingFile(file);
    }
	private function ioErrorHandler(event:IOErrorEvent):void {
        var file:FileReference = FileReference(event.target);
		var realname:String = get_realname(file);
		caller.print_message("upload_error", "ioErrorHandler("+realname+"): " + event.text);
		removePendingFile(file);
    }
 
    private function securityErrorHandler(event:SecurityErrorEvent):void {
        var file:FileReference = FileReference(event.target);
		var realname:String = get_realname(file);
		caller.print_message("upload_error", "securityError("+realname+"): " + event.text);
		removePendingFile(file);
    }
	
    private function addPendingFile(file:FileReference):void {
	        var r0:URLRequest = new URLRequest(this.uploadURL.url);
			var v0:URLVariables = new URLVariables();
            var l0:URLLoader = new URLLoader();
			
			r0.method = URLRequestMethod.POST;
            l0.dataFormat = URLLoaderDataFormat.VARIABLES;
			v0.fname = file.name;
			r0.data = v0;
			
			l0.addEventListener(IOErrorEvent.IO_ERROR, function(e:IOErrorEvent):void {
				var ev:IOErrorEvent = IOErrorEvent(e);
				caller.print_message("upload_error", "IOErrorEvent.IO_ERROR" + ev.text);
			});
			
			l0.addEventListener(HTTPStatusEvent.HTTP_STATUS, function(e:HTTPStatusEvent):void {
				var ev:HTTPStatusEvent = HTTPStatusEvent(e);
				if((ev.status == 200)||(ev.status == 0))
					return;
				caller.print_message("upload_error", "Die Datei wurde vom Server nicht akzeptiert", 
					"(HTTP/1.1 " + ev.status.toString() + ")");
				//do_next();
			});
		
            l0.addEventListener(Event.COMPLETE, function (e:Event):void {
				var l:URLLoader = URLLoader(e.target);
				//caller.print_message("upload_info", "in Event.COMPLETE: " + file.name, '- ' + l.data["newname"]);
				
				var a:Object = new Object();
				a.name = file.name;
				a.realname = l.data["newname"];
				pendingFiles.push(a);
				caller.whole_size += file.size;
				
				file.addEventListener(Event.OPEN, openHandler);
				file.addEventListener(Event.COMPLETE, completeHandler);
				file.addEventListener(HTTPStatusEvent.HTTP_STATUS, httpStatusHandler);
				file.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
				file.addEventListener(ProgressEvent.PROGRESS, progressHandler);
				file.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);

				var _vars:URLVariables = new URLVariables();
				_vars.sid = caller.session_id;
				_vars.realname = l.data["newname"];
				
				uploadURL.data = _vars;
				uploadURL.method = URLRequestMethod.POST;

				if (file.size > caller.maxlen) {
					caller.print_message("upload_error", l.data["newname"], "Datei zu groß ["+
						file.size+" > "+caller.maxlen+"]!");
					removePendingFile(file);
					return;
				}
				caller.print_message("upload_begin", l.data["newname"], file.size.toString());
				caller.updateProgress();
				file.upload(uploadURL);

				//do_realwork(file, l.data["newname"]);
			});
			
            try { 
				l0.load(r0); 
			} catch (error:Error) {
				caller.print_message("upload_error", 'Interner Fehler', error.toString());
            }
	}
}
