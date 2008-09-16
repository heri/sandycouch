<?php
/**
 * edit and create form template controller
 *
 * defined vars:
 * $actionUrl           - the url to be used in the action form.
 * $callbackId          - the callback id to be written into the form.
 * $submitValue         - value attribute of submit button.
 * $submitName          - name attribute of submit button.
 *
 * @package blog
 * @subpackage template
 * @author The myTravelbook Team <http://www.sourceforge.net/projects/mytravelbook>
 * @copyright Copyright (c) 2005-2006, myTravelbook Team
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 * @version $Id$
 */
if (!$User) {
    echo '<p class="error">'.$words->get('BlogErrors_not_logged_in').'</p>';
    return false;
}
$words = new MOD_words();
?>

<script type="text/javascript">//<!--
tinyMCE.srcMode = '';
tinyMCE.baseURL = http_baseuri+'script/tiny_mce';
tinyMCE.init({
    mode: "exact",
    elements: "create-txt",
    plugins : "advimage",
    theme: "advanced",
    relative_urls:false,
    convert_urls:false,
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,link,bullist,separator,justifyleft,justifycenter,justifyfull,bullist,numlist,forecolor,backcolor,image, charmap",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location: 'top',
    theme_advanced_statusbar_location: 'bottom',
    theme_advanced_resizing: true

});
//-->
</script>

<form method="post" action="<?=$actionUrl?>" class="def-form" id="blog-create-form">

<?php
if (in_array('inserror', $vars['errors'])) {
    echo '<p class="error">'.$words->get('BlogErrors_inserror').'</p>';
}
if (in_array('upderror', $vars['errors'])) {
    echo '<p class="error">'.$words->get('BlogErrors_upderror').'</p>';
}
?>




<fieldset id="blog-text">
<legend><?=$words->get('BlogCreateLabelText')?></legend>
    <div class="row">
    <label for="create-title"><?=$words->get('BlogCreateLabelTitle')?>:</label><br/>
        <input type="text" id="create-title" name="t" class="long" <?php
        // the title may be set
        echo isset($vars['t']) ? 'value="'.htmlentities($vars['t'], ENT_COMPAT, 'utf-8').'" ' : '';
        ?>/>
        <div id="bcreate-title" class="statbtn"></div>
        <?php
        if (in_array('title', $vars['errors'])) {
            echo '<span class="error">'.$words->get('BlogErrors_title').'</span>';
        }
        ?>
        <p class="desc"></p>
    </div>
    <div class="row">
        <label for="create-txt"><?=$words->get('BlogCreateLabelText')?>:</label><br/>
        <textarea id="create-txt" name="txt" rows="10" cols="50"><?php
        // the content may be set
        echo isset($vars['txt']) ? htmlentities($vars['txt'], ENT_COMPAT, 'utf-8') : '';
        ?></textarea>
        <div id="bcreate-c" class="statbtn"></div>
        <?php
        if (in_array('text', $vars['errors'])) {
            echo '<span class="error">'.$words->get('BlogErrors_text').'</span>';
        }
        ?>
        <p class="desc"></p>
    </div>
    <div class="row">
        <label for="create-cat"><?=$words->get('BlogCreateLabelCategories')?>:</label><br />
        <select id="create-cat" name="cat">
            <option value="">-- <?=$words->get('BlogCreateNoCategories')?> --</option>
        <?php
            foreach ($catIt as $c) {
                echo "<option value=\"".$c->blog_category_id."\" ";
                if (isset($vars['cat']) && $c->blog_category_id == $vars['cat']) echo ' selected';
                echo ">".htmlentities($c->name, ENT_COMPAT, 'utf-8')."</option>\n";
            }
        ?>
        </select>
        <?php
        if (in_array('category', $vars['errors'])) {
            echo '<span class="error">'.$words->get('BlogErrors_category').'</span>';
        }
        ?>
        <p class="desc"></p>
    </div>
    <div class="row">
        <label for="create-tags"><?=$words->get('BlogCreateLabelCreateTags')?>:</label><br />
        <textarea id="create-tags" name="tags" cols="40" rows="1"><?php
        // the tags may be set
            echo isset($vars['tags']) ? htmlentities($vars['tags'], ENT_COMPAT, 'utf-8') : '';
        ?></textarea>
        <div id="suggestion"></div>
        <p class="desc"><?=$words->get('BlogCreateLabelSublineTags')?></p>
    </div>
    <p>
        <input type="submit" value="<?=$submitValue?>" class="submit"<?php
        echo ((isset($submitName) && !empty($submitName))?' name="'.$submitName.'"':'');
        ?> />
        <input type="hidden" name="<?php
        // IMPORTANT: callback ID for post data
        echo $callbackId; ?>" value="1"/>
<?php
if (isset($vars['id']) && $vars['id']) {
?>
        <input type="hidden" name="id" value="<?=(int)$vars['id']?>"/>
<?php
}
?>
    </p>
</fieldset>

<fieldset id="blog-trip"><legend><?=$words->get('BlogCreate_LabelTrips')?></legend>
    <div class="row">
        <label for="create-sty"><?=$words->get('BlogCreateTrips_LabelStartdate')?>:</label><br />
        <input type="text" id="create-sty" name="sty" style="width:3em" <?php
        echo isset($vars['sty']) ? 'value="'.htmlentities($vars['sty'], ENT_COMPAT, 'utf-8').'" ' : '';
        ?> onblur="Cal.setDateSE('create-sty', 'create-stm', 'create-std', false, 'create-eny', 'create-enm', 'create-end', false);" onfocus="Cal.setDateSE('create-sty', 'create-stm', 'create-std', false, 'create-eny', 'create-enm', 'create-end', false);"/>
        <select id="create-stm" name="stm" onblur="Cal.setDateSE('create-sty', 'create-stm', 'create-std', false, 'create-eny', 'create-enm', 'create-end', false);" onfocus="Cal.setDateSE('create-sty', 'create-stm', 'create-std', false, 'create-eny', 'create-enm', 'create-end', false);">
            <option value="">-</option>
            <?php
                foreach ($monthNames as $m=>$name) {
                    echo '<option value="'.$m.'"';
                    if (isset($vars['stm']) && (int)$vars['stm'] == $m) {
                        echo ' selected="selected"';
                    }
                    echo '>'.$name.'</option>';
                }
            ?>
        </select>
        <input type="text" id="create-std" name="std" style="width:2em" <?php
        echo isset($vars['std']) ? 'value="'.htmlentities($vars['std'], ENT_COMPAT, 'utf-8').'" ' : '';
        ?> onblur="Cal.setDateSE('create-sty', 'create-stm', 'create-std', false, 'create-eny', 'create-enm', 'create-end', false);" onfocus="Cal.setDateSE('create-sty', 'create-stm', 'create-std', false, 'create-eny', 'create-enm', 'create-end', false);"/>
        <a href="#" id="create-stsel" onclick="Cal.aCalTarget('create-sty', 'create-stm', 'create-std');Cal.aCal('create-stsel');return false;">cal</a>
        <?php
        if (in_array('startdate', $vars['errors'])) {
            echo '<span class="error">'.$words->get('BlogErrors_startdate').'</span>';
        } elseif (in_array('duration', $vars['errors'])) {
            echo '<span class="error">'.$words->get('BlogErrors_duration').'</span>';
        }
        ?>
        <p class="desc"><?=$words->get('BlogCreateTrips_SublineStartdate')?></p>
    </div>

    <div class="row">
        <label for="create-trip"><?=$words->get('BlogCreateTrips_LabelTrip')?></')?>:</label><br />
        <select id="create-trip" name="tr">
            <option value="">-- <?=$words->get('BlogCreateTrips_NoTrip')?> --</option>
        <?php
            foreach ($tripIt as $t)
                echo "<option value=\"".$t->trip_id."\"".($t->trip_id == $vars['trip_id_foreign'] ? ' selected="selected"' : '').">".htmlentities($t->trip_name, ENT_COMPAT, 'utf-8')."</option>\n";
        ?>
        </select>
        <?php
        if (in_array('trip', $vars['errors'])) {
            echo '<span class="error">'.$words->get('BlogErrors_trip').'</span>';
        }
        ?>
        <p class="desc"></p>
    </div>
<?php
if ($google_conf && $google_conf->maps_api_key) {
?>
    <div class="row">
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php
        echo $google_conf->maps_api_key;

    ?>" type="text/javascript"></script>
         <script type="text/javascript">
         var map = null;

    function createMarker(point, descr) {
         var marker = new GMarker(point);
         GEvent.addListener(marker, "click", function() {
            marker.openInfoWindowHtml(descr);
         });
         return marker;
    }

    var loaded = false;
    function SPAF_Maps_load() {
         if (!loaded && GBrowserIsCompatible()) {

            map = new GMap2(document.getElementById("spaf_map"));
<?php
    if (isset($vars['latitude']) && isset($vars['longitude']) && $vars['latitude'] && $vars['longitude']) {
        echo 'map.setCenter(new GLatLng('.htmlentities($vars['latitude'], ENT_COMPAT, 'utf-8').', '.htmlentities($vars['longitude'], ENT_COMPAT, 'utf-8').'), 8);';
        if (isset($vars['geonamename']) && isset($vars['geonamecountry'])) {
            $desc = "'".$vars['geonamename'].", ".$vars['geonamecountry']."'";
            echo 'var marker = new GMarker(new GLatLng('.$vars['latitude'].', '.$vars['longitude'].'), '.$desc.');
                map.addOverlay(marker);
                GEvent.addListener(marker, "click", function() {
                    marker.openInfoWindowHtml('.$desc.');
                });
                marker.openInfoWindowHtml('.$desc.');';
        }
    } else {
        echo 'map.setCenter(new GLatLng(47.3666667, 8.55), 8);';
    } ?>
            map.addControl(new GSmallMapControl());
            map.addControl(new GMapTypeControl());
        }
        loaded = true;
    }

    function changeMarker(lat, lng, zoom, descr) {
        if (!loaded) {
            SPAF_Maps_load();
            loaded = true;
        }
        map.panTo(new GLatLng(lat, lng));
        map.setZoom(zoom);
        map.addOverlay(createMarker(new GLatLng(lat, lng), descr));
    }

    function setGeonameIdInForm(geonameid, latitude, longitude, geonamename, countrycode, admincode) {
        $('geonameid').value = geonameid;
        $('latitude').value = latitude;
        $('longitude').value = longitude;
        $('geonamename').value = geonamename;
        $('geonamecountrycode').value = countrycode;
        $('admincode').value = admincode;
    }

    function removeHighlight() {
        var lis = $A($('locations').childNodes);
        lis.each(function(li) {
            Element.setStyle(li, {fontWeight:''});
        });
    }

    function setMap(geonameid, latitude, longitude, zoom, geonamename, countryname, countrycode, admincode) {
        setGeonameIdInForm(geonameid, latitude, longitude, geonamename, countrycode, admincode);
        changeMarker(latitude, longitude, zoom, geonamename+', '+countryname);
        removeHighlight();
        Element.setStyle($('li_'+geonameid), {fontWeight:'bold'});
    }

    window.onunload = GUnload;
    </script>
    <input type="hidden" name="geonameid" id="geonameid" value="<?php
            echo isset($vars['geonameid']) ? htmlentities($vars['geonameid'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="latitude" id="latitude" value="<?php
            echo isset($vars['latitude']) ? htmlentities($vars['latitude'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="longitude" id="longitude" value="<?php
            echo isset($vars['longitude']) ? htmlentities($vars['longitude'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="geonamename" id="geonamename" value="<?php
            echo isset($vars['geonamename']) ? htmlentities($vars['geonamename'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="geonamecountrycode" id="geonamecountrycode" value="<?php
            echo isset($vars['geonamecountrycode']) ? htmlentities($vars['geonamecountrycode'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
    <input type="hidden" name="admincode" id="admincode" value="<?php
            echo isset($vars['admincode']) ? htmlentities($vars['admincode'], ENT_COMPAT, 'utf-8') : '';
        ?>" />
</div>
<?php
}
?>
    <label for="create-location"><?=$words->get('BlogCreateTrips_LabelLocation')?>:</label>
    <input type="text" name="create-location" id="create-location" value="" /> <input type="button" id="btn-create-location" class="button" value="<?=$words->get('label_search_location')?>" />
    <p class="desc"><?=$words->get('BlogCreateTrips_SublineLocation')?></p>
    <div class="subcolumns">
      <div class="c50l">
        <div class="subcl">
          <div id="location-suggestion" class></div>
        </div>
      </div>
      <div class="c50r">
        <div class="subcr">
          <div id="spaf_map" style="width:300px; height:200px;"></div>
        </div>
      </div>
    </div>
    <p>
        <input type="submit" value="<?=$submitValue?>" class="submit"<?php
        echo ((isset($submitName) && !empty($submitName))?' name="'.$submitName.'"':'');
        ?> />
    </p>
</fieldset>






<fieldset id="blog-settings">
    <legend><?=$words->get('BlogCreate_LabelSettings')?></legend>
    <?php
    if ($User->hasRight('write_sticky@blog')) {
    ?>
        <div class="row">
            <input type="checkbox" id="create-flag-sticky" name="flag-sticky"<?php
            if (isset($vars['flag-sticky']) && (int)$vars['flag-sticky']) {
                echo ' checked="checked"';
            }
            ?>/>
            <label for="create-flag-sticky"> <?=$words->get('BlogCreateSettings_LabelSticky')?></label>
        </div>
    <?php
    }
    ?>
    <label><?=$words->get('label_vis')?></label>
    <div class="row">
        <input type="radio" name="vis" value="pub" id="create-vis-pub"<?php
        if (
            (isset($vars['vis']) && $vars['vis'] == 'pub')
            || (!isset($vars['vis']) && (!$defaultVis || ($defaultVis && $defaultVis->valueint == 2)))
        ) {
            echo ' checked="checked"';
        }
        ?>/> <label for="create-vis-pub"><?=$words->get('BlogCreateSettings_LabelVispublic')?></label>
        <p class="desc"><?=$words->get('BlogCreateSettings_DescriptionVispublic')?></p>
    </div>
    <div class="row">
        <input type="radio" name="vis" value="prt" id="create-vis-prt"<?php
        if (
            (isset($vars['vis']) && $vars['vis'] == 'prt')
            || (!isset($vars['vis']) && $defaultVis && $defaultVis->valueint == 1)
        ) {
            echo ' checked="checked"';
        }
        ?>/> <label for="create-vis-prt"><?=$words->get('BlogCreateSettings_LabelVisprotected')?></label>
        <p class="desc"><?=$words->get('BlogCreateSettings_DescriptionVispublic')?></p>
    </div>
    <div class="row">
        <input type="radio" name="vis" value="pri" id="create-vis-pri"<?php
        if (
            (isset($vars['vis']) && $vars['vis'] != 'prt' && $vars['vis'] != 'pub')
            || (!isset($vars['vis']) && $defaultVis && $defaultVis->valueint == 0)
        ) {
            echo ' checked="checked"';
        }
        ?>/> <label for="create-vis-pri"><?=$words->get('BlogCreateSettings_LabelVisprivate')?></label>
        <p class="desc"><?=$words->get('BlogCreateSettings_DescriptionVisprivate')?></p>
    </div>
<p>
        <input type="submit" value="<?=$submitValue?>" class="submit"<?php
        echo ((isset($submitName) && !empty($submitName))?' name="'.$submitName.'"':'');
        ?> />
    </p>
</fieldset>





</form>
<script type="text/javascript">//<!--
new FieldsetMenu('blog-create-form', {<?php
if (in_array('startdate', $vars['errors']) || in_array('duration', $vars['errors'])) {
    echo 'active: "blog-trip"';
} else {
    echo 'active: "blog-text"';
}
?>});
BlogSuggest.initialize('blog-create-form');

function eventHandlerFunction(e) {
    SPAF_Maps_load();
    Event.stop(e);
}
Event.observe('liblog-trip', "click", eventHandlerFunction, false);

//-->
</script>