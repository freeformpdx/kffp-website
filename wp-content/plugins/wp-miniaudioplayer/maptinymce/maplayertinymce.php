<?php
require('../../../../wp-blog-header.php');

$plugin_version = get_option('mbYTPlayer_version');
$includes_url = includes_url();
$plugins_url = plugins_url();
$charset = get_option('blog_charset');
$donate = get_option('miniAudioPlayer_donate');

$exclude_class = get_option('miniAudioPlayer_excluded');
$showVolumeLevel = get_option('miniAudioPlayer_showVolumeLevel');
$allowMute = get_option('miniAudioPlayer_allowMute');
$showTime = get_option('miniAudioPlayer_showTime');
$showRew = get_option('miniAudioPlayer_showRew');
$width = get_option('miniAudioPlayer_width');
$skin = get_option('miniAudioPlayer_skin');
$miniAudioPlayer_animate = get_option('miniAudioPlayer_animate');
$miniAudioPlayer_add_gradient = get_option('miniAudioPlayer_add_gradient');
$volume = get_option('miniAudioPlayer_volume');
$downloadable = get_option('miniAudioPlayer_download');
$custom_skin_name = get_option('miniAudioPlayer_custom_skin_name');
$downloadable_security = get_option('miniAudioPlayer_download_security');


if (!headers_sent()) {
    header('Content-Type: text/html; charset='.$charset);
}

if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>" />
    <title>mb.miniAudioPlayer</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $plugins_url.'/wp-miniaudioplayer/maptinymce/bootstrap-1.4.0.min.css?v='.$plugin_version; ?>"/>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $plugins_url.'/wp-miniaudioplayer/js/jquery.metadata.js?v='.$plugin_version; ?>"></script>
    <script type="text/javascript" src="<?php echo $plugins_url.'/wp-miniaudioplayer/js/id3.min.js?v='.$plugin_version; ?>"></script>
    <script type="text/javascript" src="<?php echo $includes_url.'js/tinymce/tiny_mce_popup.js?v='.$plugin_version; ?>"></script>

    <style>
        fieldset span.label{
            display: inline-block;
            width: 150px;
        }

        fieldset label {
            margin: 0;
            padding: 3px!important;
            border-top: 1px solid #dcdcdc;
            border-bottom: 1px solid #f9f9f9;
            display: block;
        }

        .actions{
            text-align: right;
        }

        .span6{
            width: 100px!important;
        }
        h3{
            color:#404040!important;
        }
    </style>

</head>
<body>

<form class="form-stacked" action="#">
    <fieldset>
        <legend><?php _e('mb.miniAudioPlayer parameters', 'wp-miniaudioplayer'); ?>:</legend>

        <label>
            <span class="label"><?php _e('Don’t render', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="exclude" value="true"/>
            <span class="help-inline"><?php _e('check to exclude this link', 'wp-miniaudioplayer'); ?> (<?php echo $exclude_class ?>)</span>
        </label>

        <label>
            <span class="label"><?php _e('Audio url', 'wp-miniaudioplayer'); ?> <span style="color:red">*</span> : </span>
            <input type="text" name="url" class="span5"/>
            <span class="help-inline"><?php _e('A valid .mp3 url', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Audio title', 'wp-miniaudioplayer'); ?>: </span>
            <input type="text" name="audiotitle" class="span5"/>
            <span class="help-inline"><?php _e('The audio title', 'wp-miniaudioplayer'); ?></span><br>
            <span class="label"> </span>
            <button id="metadata" onclick="getFromMetatags();$(this).hide(); return false" style="color: gray" ><?php _e('Get the title from meta-data', 'wp-miniaudioplayer'); ?></button>
        </label>

        <label>
            <span class="label"><?php _e('Skin', 'wp-miniaudioplayer'); ?>:</span>
            <select name="skin">
                <option value="black"><?php _e('black', 'wp-miniaudioplayer'); ?></option>
                <option value="blue"><?php _e('blue', 'wp-miniaudioplayer'); ?></option>
                <option value="orange"><?php _e('orange', 'wp-miniaudioplayer'); ?></option>
                <option value="red"><?php _e('red', 'wp-miniaudioplayer'); ?></option>
                <option value="gray"><?php _e('gray', 'wp-miniaudioplayer'); ?></option>
                <option value="green"><?php _e('green', 'wp-miniaudioplayer'); ?></option>
                <option value="<?php echo $custom_skin_name ?>"><?php echo $custom_skin_name ?></option>
            </select>
            <span class="help-inline"><?php _e('Set the skin color for the player', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Gradient', 'wp-miniaudioplayer'); ?>:</span>
            <input type="checkbox" name="addGradient" value="true"/>
            <span class="help-inline"><?php  _e('Check to add a gradient to the player skin', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Animate', 'wp-miniaudioplayer'); ?>:</span>
            <input type="checkbox" name="animate" value="true"/>
            <span class="help-inline"><?php _e('Check to activate the opening / closing animation', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Width', 'wp-miniaudioplayer'); ?>: </span>
            <input type="text" name="width" class="span6"/>
            <span class="help-inline"><?php _e('Set the player width', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Volume', 'wp-miniaudioplayer'); ?>: </span>
            <input type="text" name="volume" class="span6"/>
            <span class="help-inline"><?php _e('(from 1 to 10) Set the player initial volume', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Autoplay', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="autoplay" value="true"/>
            <span class="help-inline"><?php _e('Check to start playing on page load', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Loop', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="loop" value="false"/>
            <span class="help-inline"><?php _e('Check to loop the sound', 'wp-miniaudioplayer'); ?></span>
        </label>

        <h3><?php _e('Show/Hide', 'wp-miniaudioplayer'); ?></h3>

        <label>
            <span class="label"><?php _e('Volume control', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="showVolumeLevel" value="true"/>
            <span class="help-inline"><?php _e('Check to show the volume control', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Time control', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="showTime" value="true"/>
            <span class="help-inline"><?php _e('Check to show the time control', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Mute control', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="allowMute" value="true"/>
            <span class="help-inline"><?php _e('Check to activate the mute button', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Rewind control', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="showRew" value="true"/>
            <span class="help-inline"><?php _e('Check to show the rewind control', 'wp-miniaudioplayer'); ?></span>
        </label>

        <label>
            <span class="label"><?php _e('Downloadable', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="downloadable" value="false" onclick="manageSecurity(this)"/>
            <span class="help-inline"><?php _e('Check to show the download button', 'wp-miniaudioplayer'); ?></span><br>
        </label>

        <label>
            <span class="label" style="font-weight: normal; color: gray"><?php _e('Only registered', 'wp-miniaudioplayer'); ?>: </span>
            <input type="checkbox" name="downloadable_security" value="true"/>
            <span class="help-inline"><?php _e('Check to limit downloads to registered users', 'wp-miniaudioplayer'); ?></span>
        </label>

        <script>
            function manageSecurity(el){

                var security = jQuery('[name=downloadablesecurity]');
                if(jQuery(el).is(":checked")){
                    security.removeAttr('disabled');
                }else{
                    security.attr('disabled','disabled');
                    security.removeAttr('checked');
                }
            }
        </script>

    </fieldset>

    <div class="actions">
        <input type="submit" value="Insert the code" class="btn primary"/>
        or
        <input class="btn" type="reset" value="Reset settings"/>
    </div>
</form>

<!--DONATE POPUP-->
<style>
    #donate{ position: fixed; top: 0; left: 0; width: 100%; height: 100%; padding: 30px; text-align: center; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; z-index: 10000; display: none}
    #donateContent{ position: relative; margin: 30px auto; background: rgba(77, 71, 61, 0.88); color:white; padding: 30px; text-align: center; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; width: 450px; border-radius: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.5) }
    #donate h2{ font-size: 30px; line-height: 33px; color: #ffffff; }
    #donate p{ margin: 30px; font-size: 16px; line-height: 22px; display: block; float: none; }
    #donate p#follow{ margin: 30px; font-size: 16px; line-height: 33px; }
    #donate p#timer{ padding: 5px; font-size: 20px; line-height: 33px; background: #231d0c; border-radius: 30px; color: #ffffff; width: 30px; margin: auto; }
</style>

<div id="donate">
    <div id="donateContent">
        <h2>mb.miniAudioPlayer</h2>
        <p ><?php _e('If you like it and you are using it then you should consider a donation <br> (€15,00 or more) :-)', 'wp-miniaudioplayer'); ?></p>
        <p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=V6ZS8JPMZC446&lc=GB&item_name=mb%2eideas&item_number=MBIDEAS&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG_global%2egif%3aNonHosted" target="_blank" onclick="donate();">
                <img border="0" alt="PayPal" src="https://www.paypalobjects.com/en_US/IT/i/btn/btn_donateCC_LG.gif">
            </a></p>
        <p id="timer">&nbsp;</p>
        <br>
        <br>
        <button onclick="donate()"><?php _e('I already donate', 'wp-miniaudioplayer'); ?></button>
    </div>
</div>
<script type="text/javascript">

    $.mbCookie = {
        set:function (name, value, days, domain) {
            if (!days) days = 7;
            domain = domain ? "; domain=" + domain : "";
            var date = new Date(), expires;
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
            document.cookie = name + "=" + value + expires + "; path=/" + domain;
        },
        get:function (name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ')
                    c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0)
                    return unescape(c.substring(nameEQ.length, c.length));
            }
            return null;
        },
        remove:function (name) {
            $.mbCookie.set(name, "", -1);
        }
    };

    function donate() {
        jQuery.mbCookie.set("mapdonate", true);
        self.location.reload();
    }

    jQuery(function () {
        var hasDonate = <?php echo $donate ?> ;
        if (hasDonate || $.mbCookie.get("mapdonate") === "true" ) {
            jQuery("#donate").remove();
            jQuery("#inlineDonate").remove();
        } else {
            jQuery("#donate").show();
            var timer = 5;
            var closeDonate = setInterval(function () {
                timer--;
                jQuery("#timer").html(timer);
                if (timer == 0) {
                    clearInterval(closeDonate);
                    jQuery("#donate").fadeOut(600, jQuery(this).remove)
                }
            }, 1000)
        }
    });
</script>

<!--END DONATE POPUP-->

<script type="text/javascript">

    var tmpInfo = {};

    function getFromMetatags(title){
        if (typeof ID3 == "object") {
            ID3.loadTags(document.audioURL, function () {
                var info = {};
                info.title = ID3.getTag(document.audioURL, "title");
                info.artist = ID3.getTag(document.audioURL, "artist");
                info.album = ID3.getTag(document.audioURL, "album");
                info.track = ID3.getTag(document.audioURL, "track");
                info.size = ID3.getTag(document.audioURL, "size");
                if(info.title && info.title!=undefined){
                    jQuery("[name='audiotitle']").val(info.title + " - " +info.artist);

                    tmpInfo = info;
                }else{
                    $("button#metadata").after("no meta-data available for this file");
                }
            })
        }
    }

    tinyMCEPopup.onInit.add(function(ed) {
        //var ed = top.tinymce.activeEditor;

        var selection = ed.selection.getNode();
        ed.selection.select(selection,true);
        var $selection = jQuery(selection);

        var map_element = $selection.find("a[href *= '.mp3']");
        if (map_element.length){
            selection = ed.selection.select(map_element.get(0),true);
        }else if($selection.prev().is("a[href *= '.mp3']")){
            selection = ed.selection.select($selection.prev().get(0),true);
        }
        $selection = jQuery(selection);

        var url = document.audioURL = $selection.attr("href");
        var title = $selection.html();
        var isExcluded = $selection.hasClass("<?php echo $exclude_class ?>");

        var $desc = $selection.next(".map_params");
        var metadata = $selection.metadata();

        if(metadata.volume)
            metadata.volume =  parseFloat(metadata.volume)*10;

        if(jQuery.isEmptyObject(metadata)){
            var defaultmeta = {
                showVolumeLevel:<?php echo empty($showVolumeLevel) ? false : $showVolumeLevel ?>,
                allowMute:<?php echo $allowMute ? "true" : "false"?>,
                showTime:<?php echo $showTime ? "true" : "false"?>,
                showRew:<?php echo $showRew ? "true" : "false"?>,
                width:"<?php echo $width ?>",
                skin:"<?php echo $skin ?>",
                animate:<?php echo $miniAudioPlayer_animate ? "true" : "false" ?>,
                loop:false,
                addGradientOverlay: <?php echo $miniAudioPlayer_add_gradient ? "true" : "false" ?>,
                downloadable:<?php echo $downloadable ? "true" : "false" ?>,
                downloadable_security:<?php echo $downloadable_security ? "true" : "false" ?>,
                volume:parseFloat(<?php echo $volume ?>)*10
            };
            jQuery.extend(metadata,defaultmeta);
        }

        jQuery.extend(metadata, {exclude:isExcluded});

        jQuery("[name='url']").val(url);

        jQuery("[name='audiotitle']").val(title);

        for (var i in metadata){
            if(typeof metadata[i] == "boolean"){
                if(eval(metadata[i]) == true)
                    jQuery("[name="+i+"]").attr("checked",  "checked");
            }else
                jQuery("[name="+i+"]").val(metadata[i]);

        }

        var form = document.forms[0];
        var isEmpty = function(value) {
                return (/^\s*$/.test(value));
            },

            encodeStr = function(value) {
                return value.replace(/\s/g, "%20")
                    .replace(/"/g, "%22")
                    .replace(/'/g, "%27")
                    .replace(/=/g, "%3D")
                    .replace(/\[/g, "%5B")
                    .replace(/\]/g, "%5D")
                    .replace(/\//g, "%2F");
            },

            insertCode = function(e){

                var map_params = "{";
                if(jQuery("[name='skin']").val().length>0)
                    map_params+="skin:'"+jQuery("[name='skin']").val()+"', ";
                map_params+="animate:"+(jQuery("[name='animate']").is(":checked") ? "true" : "false")+", ";
                if(jQuery("[name='width']").val().length>0)
                    map_params+="width:'"+jQuery("[name='width']").val()+"', ";
                if(jQuery("[name='volume']").val().length>0)
                    map_params+="volume:"+ jQuery("[name='volume']").val()/10 +", ";
                map_params+="autoplay:"+(jQuery("[name='autoplay']").is(":checked") ? "true" : "false")+", ";
                map_params+="loop:"+(jQuery("[name='loop']").is(":checked") ? "true" : "false")+", ";
                map_params+="showVolumeLevel:"+(jQuery("[name='showVolumeLevel']").is(":checked") ? "true" : "false")+", ";
                map_params+="showTime:"+(jQuery("[name='showTime']").is(":checked") ? "true" : "false")+", ";
                map_params+="allowMute:"+(jQuery("[name='allowMute']").is(":checked") ? "true" : "false")+", ";
                map_params+="showRew:"+(jQuery("[name='showRew']").is(":checked") ? "true" : "false")+", ";
                map_params+="addGradientOverlay:"+(jQuery("[name='addGradient']").is(":checked") ? "true" : "false")+", ";
                map_params+="downloadable:"+(jQuery("[name='downloadable']").is(":checked") ? "true" : "false")+", ";
                map_params+="downloadablesecurity:"+(jQuery("[name='downloadablesecurity']").is(":checked") ? "true" : "false")+", ";
                map_params+="id3: false";
                map_params+="}";
                map_params = map_params.replace(", }", "}");

                var isExcluded = jQuery("[name='exclude']").is(":checked") ? "<?php echo $exclude_class ?> " : "";

                var map_a = "<a id='mbmaplayer_"+new Date().getTime()+"' class=";
                map_a += "\"mb_map " + isExcluded + map_params + "\" ";

                for (var x in tmpInfo){
                    map_a += "meta-"+ x +"=\""+tmpInfo[x]+"\" ";
                }
                map_a += "href=\""+jQuery("[name='url']").val()+"\">";
                map_a+=jQuery("[name='audiotitle']").val();
                map_a+="</a>";
                ed.execCommand('mceInsertContent', 0, map_a);

                if($desc.length)
                    $desc.remove();

                tinyMCEPopup.close();

                return false;
            };

        form.onsubmit = insertCode;
        tinyMCEPopup.resizeToInnerSize();
    });
</script>
</body>
<?php } ?>
</html>
