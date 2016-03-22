(function() {
	tinymce.create('tinymce.plugins.maplayer', {

		init : function(ed, url) {

			var popUpURL = url + '/maplayertinymce.php';

			ed.addCommand('maplayerpopup', function() {

				function openEditor(){
					ed.windowManager.open({
						url : popUpURL,
						width : 900,
						height :  jQuery(window).height(),
						inline : 1
					});
				}

				if(! ed.isHref){
					alert("Select a link to an mp3 file to customize the player.");
					return;
				}

				if(!ed.isValidURL){
					var d = confirm("the selected Link doesn't seams a valid MP3 path; do you want to continue anyway?");
					if (d == true) {
						openEditor();
					}
				} else{
					openEditor();
				}

			});

			ed.addButton('maplayerbutton', {
				title : 'Modify a miniAudioPlayer',
				image : url + '/maplayerbutton.svg',
				cmd : 'maplayerpopup'
			});

			ed.onNodeChange.add(function(ed) {
				var selection = ed.selection.getNode();

				var btnId = typeof ed.controlManager.buttons != "undefined" ? ed.controlManager.buttons.maplayerbutton._id : ed.controlManager.get("maplayerbutton").id;

				var disable = false;
				ed.isValidURL = false;
				ed.isHref = false;

				jQuery("#"+btnId).css({opacity:.5, border:"1px solid transparent"});

				if (jQuery(selection).is("a[href *= '.mp3']") || jQuery(selection).find("a[href *= '.mp3']").lenght>0 || jQuery(selection).prev().is("a[href *= '.mp3']")) {
					ed.isHref = true;
					ed.isValidURL = true;
					disable = false;
					jQuery("#"+btnId).css({opacity:1});
				} else if(jQuery(selection).is("a") || jQuery(selection).find("a").lenght>0 || jQuery(selection).prev().is("a" )) {
					ed.isHref = true;
				}

				ed.controlManager.setDisabled("maplayerbutton", disable);

			});
		},

		createControl : function() {
			return null;
		},

		getInfo : function() {
			return {};
		}
	});
	tinymce.PluginManager.add('maplayer', tinymce.plugins.maplayer);
}());
