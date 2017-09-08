<?php
/*

Copyright 2015 MagicToolbox (email : support@magictoolbox.com)

*/

$error_message = false;

function WordPress_MagicZoomPlus_activate () {

    if(!function_exists('file_put_contents')) {
        function file_put_contents($filename, $data) {
            $fp = fopen($filename, 'w+');
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }


    //fix url's in css files
    $fileContents = file_get_contents(dirname(__FILE__) . '/core/magiczoomplus.css');
    $cssPath = preg_replace('/https?:\/\/[^\/]*/is', '', get_option("siteurl"));

    $cssPath .= '/wp-content/'.preg_replace('/^.*?\/(plugins\/.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__))).'/core';

    $pattern = '/url\(\s*(?:\'|")?(?!'.preg_quote($cssPath, '/').')\/?([^\)\s]+?)(?:\'|")?\s*\)/is';
    $replace = 'url(' . $cssPath . '/$1)';
    $fixedFileContents = preg_replace($pattern, $replace, $fileContents);
    if($fixedFileContents != $fileContents) {
        file_put_contents(dirname(__FILE__) . '/core/magiczoomplus.css', $fixedFileContents);
    }
    
    magictoolbox_WordPress_MagicZoomPlus_init() ;

    WordPress_MagicZoomPlus_send_stat('install');

}

function WordPress_MagicZoomPlus_deactivate () {

    //delete_option("WordPressMagicZoomPlusCoreSettings");
    WordPress_MagicZoomPlus_send_stat('uninstall');
}

function WordPress_MagicZoomPlus_send_stat($action = '') {

    //NOTE: don't send from working copy
    if('working' == 'v6.0.17' || 'working' == 'v5.1.1') {
        return;
    }

    $hostname = 'www.magictoolbox.com';

    $url = preg_replace('/^https?:\/\//is', '', get_option("siteurl"));
    $url = urlencode(urldecode($url));

    global $wp_version;
    $platformVersion = isset($wp_version) ? $wp_version : '';
    
    

    $path = "api/stat/?action={$action}&tool_name=magiczoomplus&license=trial&tool_version=v5.1.1&module_version=v6.0.17&platform_name=wordpress&platform_version={$platformVersion}&url={$url}";
    $handle = @fsockopen($hostname, 80, $errno, $errstr, 30);
    if($handle) {
        $headers  = "GET /{$path} HTTP/1.1\r\n";
        $headers .= "Host: {$hostname}\r\n";
        $headers .= "Connection: Close\r\n\r\n";
        fwrite($handle, $headers);
        fclose($handle);
    }

}

function showMessage_WordPress_MagicZoomPlus($message, $errormsg = false) {
    if ($errormsg) {
        echo '<div id="message" class="error">';
    } else {
        echo '<div id="message" class="updated fade">';
    }
    echo "<p><strong>$message</strong></p></div>";
}    


function showAdminMessages_WordPress_MagicZoomPlus(){
    global $error_message;
    if (current_user_can('manage_options')) {
       showMessage_WordPress_MagicZoomPlus($error_message,true);
    }
}


function plugin_get_version_WordPress_MagicZoomPlus() {
    $plugin_data = get_plugin_data(str_replace('/plugin.php','.php',__FILE__));
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
}

function update_plugin_message_WordPress_MagicZoomPlus() {
    $ver = json_decode(@file_get_contents('http://www.magictoolbox.com/api/platform/wordpress/version/'));
    if (empty($ver)) return false;
    $ver = str_replace('v','',$ver->version);
    $oldVer = plugin_get_version_WordPress_MagicZoomPlus();
    if (version_compare($oldVer, $ver, '<')) {
        echo '<div id="message" class="updated fade">
                  <p>New version available! We recommend that you download the latest version of the plugin <a href="http://magictoolbox.com/magiczoomplus/modules/wordpress/">here</a>. </p>
              </div>';
    }
}

function MagicZoomPlus_remove_update_nag($value) {
    if (isset($value->response)) {
        unset($value->response[ str_replace('/plugin','',plugin_basename(__FILE__)) ]);
    }
    return $value;
}

function  magictoolbox_WordPress_MagicZoomPlus_init() {

    global $error_message;
    
    $tool_lower = 'magiczoomplus';
    switch ($tool_lower) {
	case 'magiczoom': 	$priority = '90'; break;
	case 'magiczoomplus': 	$priority = '100'; break;
	case 'magicthumb': 	$priority = '110'; break;
	case 'magicscroll': 	$priority = '120'; break;
	case 'magicslideshow':	$priority = '130'; break;
	case 'magic360': 	$priority = '140'; break;
	case 'magictouch': 	$priority = '150'; break;
	default :		$priority = '90'; break;
    }
    
    add_action("admin_menu", "magictoolbox_WordPress_MagicZoomPlus_config_page_menu");
    add_action( 'admin_enqueue_scripts', 'WordPress_MagicZoomPlusload_admin_scripts' );
    add_action("wp_head", "magictoolbox_WordPress_MagicZoomPlus_styles",$priority); //load scripts and styles
    add_filter("the_content", "magictoolbox_WordPress_MagicZoomPlus_create", 13); //filter content

    
    
    
    add_filter('site_transient_update_plugins', 'MagicZoomPlus_remove_update_nag');
    add_filter( 'plugin_action_links', 'magictoolbox_WordPress_MagicZoomPlus_links', 10, 2 );

    if (!file_exists(dirname(__FILE__) . '/core/magiczoomplus.js')) {
        $jsContents = file_get_contents('http://www.magictoolbox.com/static/magiczoomplus/trial/magiczoomplus.js');
        if (!empty($jsContents) && preg_match('/\/\*.*?\\\*/is',$jsContents)){
            if ( !is_writable(dirname(__FILE__) . '/core/')) {
                $error_message = 'The '.substr(dirname(__FILE__),strpos(dirname(__FILE__),'wp-content')).'/core/magiczoomplus.js file is missing. Please re-uplaod it.';
            }
            file_put_contents(dirname(__FILE__) . '/core/magiczoomplus.js', $jsContents);
            chmod(dirname(__FILE__) . '/core/magiczoomplus.js', 0777);
        } else {
            $error_message = 'The '.substr(dirname(__FILE__),strpos(dirname(__FILE__),'wp-content')).'/core/magiczoomplus.js file is missing. Please re-uplaod it.';
        }
    }
    if ($error_message) add_action('admin_notices', 'showAdminMessages_WordPress_MagicZoomPlus');

    //add_filter("shopp_catalog", "magictoolbox_create", 1); //filter content for SHOPP plugin

    if(!isset($GLOBALS['magictoolbox']['WordPressMagicZoomPlus'])) {
        require_once(dirname(__FILE__) . '/core/magiczoomplus.module.core.class.php');
        $coreClassName = "MagicZoomPlusModuleCoreClass";
        $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'] = new $coreClassName;
        $coreClass = &$GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];
    }
    $coreClass = &$GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];
    /* get current settings from db */
    $settings = get_option("WordPressMagicZoomPlusCoreSettings");
    if($settings !== false && is_array($settings) && isset($settings['default']) && !isset($_GET['reset_settings'])) {
        foreach (WordPressMagicZoomPlus_getParamsProfiles() as $profile => $name) {
	    if (isset($settings[$profile])) {
		$coreClass->params->appendParams($settings[$profile],$profile);
	    }
	}
    } else { //set defaults
        $allParams = array();
	foreach (WordPressMagicZoomPlus_getParamsProfiles() as $profile => $name) {
	    $coreClass->params->setParams($coreClass->params->getParams('default'),$profile);
	    $allParams[$profile] = $coreClass->params->getParams('default');
	}
	delete_option("WordPressMagicZoomPlusCoreSettings");
        add_option("WordPressMagicZoomPlusCoreSettings", $allParams);
    }
   
    
}

function WordPress_MagicZoomPlusload_admin_scripts () {
 
    wp_enqueue_script( 'jquery' ,includes_url('/js/jquery/jquery.js'));
    wp_enqueue_script( 'jquery-ui-core', includes_url('/js/jquery/ui/core.js') );
    wp_enqueue_script( 'jquery-ui-tabs', includes_url('/js/jquery/ui/tabs.js') );
 
}



function WordPressMagicZoomPlus_config_page() {
     magictoolbox_WordPress_MagicZoomPlus_config_page('WordPressMagicZoomPlus');
}

function magictoolbox_WordPress_MagicZoomPlus_links( $links, $file ) {
    if ( $file == plugin_basename( dirname(__FILE__).'.php' ) ) {
        $settings_link = '<a href="admin.php?page=WordPressMagicZoomPlus-config-page">'.__('Settings').'</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
}

function magictoolbox_WordPress_MagicZoomPlus_config_page_menu() {
    if(function_exists("add_menu_page")) {
        //$page = add_submenu_page("admin.php", __("Magic Zoom Plus Plugin Configuration"), __("Magic Zoom Plus Configuration"), "manage_options", "WordPressMagicZoomPlus-config-page", "WordPressMagicZoomPlus_config_page");
        $page = add_menu_page( __("Magic Zoom Plus"), __("Magic Zoom Plus"), "manage_options", "WordPressMagicZoomPlus-config-page", "WordPressMagicZoomPlus_config_page", plugin_dir_url( __FILE__ )."/core/admin_graphics/icon.png");
    }
}

function  magictoolbox_WordPress_MagicZoomPlus_config_page($id) {

    update_plugin_message_WordPress_MagicZoomPlus();
    
    $settings = get_option("WordPressMagicZoomPlusCoreSettings");
    $map = WordPressMagicZoomPlus_getParamsMap();
    

    if(isset($_POST["submit"])) {
	$allSettings = array();
        /* save settings */
        foreach (WordPressMagicZoomPlus_getParamsProfiles() as $profile => $name) {
	    $GLOBALS['magictoolbox'][$id]->params->setProfile($profile);
	    foreach($_POST as $name => $value) {
		if(preg_match('/magiczoomplussettings_'.ucwords($profile).'_(.*)/is',$name,$matches)) {
		    $GLOBALS['magictoolbox'][$id]->params->setValue($matches[1],$value);
	      }
	    }
	    $allSettings[$profile] = $GLOBALS['magictoolbox'][$id]->params->getParams($profile);
	}
	update_option($id . "CoreSettings", $allSettings);
	$settings = $allSettings;
    }

    
    
    
    
    ?>
	<style>
        <?php /*.<?php echo $toolAbr; ?>params { margin:20px 0; width:90%; border:1px solid #dfdfdf; }*/ ?>
        #magiczoomplus-config-form  .params { margin:0; width:100%;}
        #magiczoomplus-config-form .params th { border-bottom:1px solid #dfdfdf; font-weight:bold; background:#fff; text-align:left; padding:15px 20px; vertical-align: top; }
        #magiczoomplus-config-form .params td { vertical-align:top; border-bottom:1px solid #dfdfdf; padding:10px 5px; background:#fff; width:100%; }
        #magiczoomplus-config-form .params tr.back th, #magiczoomplus-config-form .params tr.back td { background:#f9f9f9; }
        #magiczoomplus-config-form .params tr.last th, #magiczoomplus-config-form .params tr.last td { border:none; }
        .afterText {font-size:10px;font-style:normal;font-weight:normal;}
        .help-block {font-size: 11px; display:block}
        .settingsTitle {font-size: 1.5em;font-weight: normal;margin: 1.7em 0 1em 0;}
        .ui-tabs-nav {margin-bottom: -6px;}
        .ui-tabs-nav li {display: inline-block;}
        .ui-tabs-nav a:focus {box-shadow:none !important;}

        input[type="checkbox"],input[type="radio"] {margin:5px;vertical-align:middle !important;}
        td img {vertical-align:middle !important; margin-right:10px;}
        td span {vertical-align:middle !important; margin-right:10px;}
		#footer , #wpfooter {position:relative;}
    </style>
    
    <script type="text/javascript">
	 jQuery( document ).ready(function() {
	      jQuery(function() {
		  jQuery( "#tabs" ).tabs({
			activate: function(event, ui){
			    jQuery('.nav-tab').removeClass('nav-tab-active');
			    jQuery(ui.newTab).children().addClass('nav-tab-active');
			}
		  });
	      });
	  });
    </script>

    
    <div class="icon32" id="icon-options-general"><br></div>
    <h2>Magic Zoom Plus Settings</h2><br/>
    <p style="font-size:15px;">Learn about all the <a href="http://www.magictoolbox.com/magiczoomplus/integration/" target="_blank">Magic Zoom Plus&trade; settings and examples too!</a>&nbsp;|&nbsp;<a href="http://www.magictoolbox.com/contact/">Get support</a></p>
    <form action="" method="post" id="magiczoomplus-config-form">
    
    
    
    
    
    
    
     <div id="tabs">
    
	      <h2 class="nav-tab-wrapper">
		<ul>
		<?php /*<li><a data-toggle="tab" class="nav-tab nav-tab-active" href="#tab-general">General</a></li>*/ ?>
		<?php foreach (WordPressMagicZoomPlus_getParamsProfiles() as $block_id => $block_name) { 
		    if (!isset($tactive)) {
			$tactive = 'nav-tab-active';
		    } else {
			$tactive = '';
		    }
		?>
		    <li><a data-toggle="tab" class="nav-tab <?php echo $tactive; ?>" href="#tab-<?php echo $block_id; ?>"><?php echo $block_name; ?></a></li>
		<?php } ?>
		</ul>
	      </h2>
	      
	      
	      <?php foreach (WordPressMagicZoomPlus_getParamsProfiles() as $block_id => $block_name) { 
	      ?>
		  <div id="tab-<?php echo $block_id; ?>">
		  
			<?php echo WordPressMagicZoomPlus_get_options_groups($settings, $block_id, $map, $id); ?>
		  </div>
	      <?php } ?>
	  </div>
	  
            <p><input type="submit" name="submit" class="button-primary" value="Save settings" />&nbsp;<a href="admin.php?page=WordPressMagicZoomPlus-config-page&reset_settings=true">Reset to defaults</a></p>
        </form>

   
    </div>

    <div style="font-size:12px;margin:5px auto;text-align:center;">Learn more about the <a href="http://www.magictoolbox.com/magiczoomplus_integration/" target="_blank">customisation options</a></div>
    
    
    
    <?php
    
}
    
    function WordPressMagicZoomPlus_get_options_groups ($settings, $profile = 'default', $map, $id) {
    
	
	$html = '';
	$toolAbr = '';
	$abr = explode(" ", strtolower("Magic Zoom Plus"));
	
	foreach ($abr as $word) $toolAbr .= $word{0};
    
	$corePath = preg_replace('/https?:\/\/[^\/]*/is', '', get_option("siteurl"));
	$corePath .= '/wp-content/'.preg_replace('/^.*?\/(plugins\/.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__))).'/core';
	
	if (!isset($settings[$profile])) return false;
	$settings = $settings[$profile]; 

                $groups = array();
                $imgArray = array('zoom & expand','zoom&expand','yes','zoom','expand','swap images only','original','expanded','no','left','top left','top','top right', 'right', 'bottom right', 'bottom', 'bottom left'); //array for the images ordering

                $result = '';
                
                foreach($settings as $name => $s) { 
                
		    if (!isset($map[$profile][$s['group']]) || !in_array($s['id'], $map[$profile][$s['group']])) continue; 
                
		    if ($profile == 'product') {
			if ($s['id'] == 'page-status' && !isset($s['value'])) {
			    $s['default'] = 'Yes';
			}
		    }
		    
		    if (!isset($s['value'])) $s['value'] = $s['default'];

		    if ($profile == 'product') {
			if ($s['id'] == 'page-status' && !isset($s['value'])) {
			    $s['default'] = 'Yes';
			}
		    }
                    if (strtolower($s['id']) == 'direction') continue;
		    if (strtolower($s['id']) == 'enabled-effect' || strtolower($s['id']) == 'class' || strtolower($s['id']) == 'nextgen-gallery'  ) {
			$s['group'] = 'top';
                    }
                    
                    
                    if (!isset($groups[$s['group']])) {
                        $groups[$s['group']] = array();
                    }

                    //$s['value'] = $GLOBALS['magictoolbox'][$id]->params->getValue($name);

                    if (strpos($s["label"],'(')) {
                        $before = substr($s["label"],0,strpos($s["label"],'('));
                        $after = ' '.str_replace(')','',substr($s["label"],strpos($s["label"],'(')+1));
                    } else {
                        $before = $s["label"];
                        $after = '';
                    }
                    if (strpos($after,'%')) $after = ' %';
                    if (strpos($after,'in pixels')) $after = ' pixels';
                    if (strpos($after,'milliseconds')) $after = ' milliseconds';
                    
                    if (isset($s["description"]) && trim($s["description"]) != '') {
			$description = $s["description"];
		    } else {
			$description = '';
		    }

                    $html  .= '<tr>';
                    $html  .= '<th width="30%">';
                    $html  .= '<label for="magiczoomplussettings'.'_'.ucwords($profile).'_'. $name.'">'.$before.'</label>';

                    if(($s['type'] != 'array') && isset($s['values'])) $html .= '<br/> <span class="afterText">' . implode(', ',$s['values']).'</span>';

                    $html .= '</th>';
                    $html .= '<td width="70%">';

                    switch($s["type"]) {
                        case "array": 
                                $rButtons = array();
                                foreach($s["values"] as $p) {
                                    $rButtons[strtolower($p)] = '<label><input type="radio" value="'.$p.'"'. ($s["value"]==$p?"checked=\"checked\"":"").' name="magiczoomplussettings'.'_'.ucwords($profile).'_'.$name.'" id="magiczoomplussettings'.'_'.ucwords($profile).'_'. $name.$p.'">';
                                    $pName = ucwords($p);
                                    if(strtolower($p) == "yes")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/yes.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "no")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/no.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom-left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom-right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top-left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top-right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    else {
                                        if (strtolower($p) == 'load,hover') $p = 'Load & hover';
                                        if (strtolower($p) == 'load,click') $p = 'Load & click';
                                        $rButtons[strtolower($p)] .= '<span>'.ucwords($p).'</span></label>';
                                    }
                                }
                                foreach ($imgArray as $img){
                                    if (isset($rButtons[$img])) {
                                        $html .= $rButtons[$img];
                                        unset($rButtons[$img]);
                                    }
                                }
                                $html .= implode('',$rButtons);
                            break;
                        case "num": 
                        case "text": 
                        default:
                            if (strtolower($name) == 'message') { $width = 'style="width:95%;"';} else {$width = '';}
                            $html .= '<input '.$width.' type="text" name="magiczoomplussettings'.'_'.ucwords($profile).'_'.$name.'" id="magiczoomplussettings'.'_'.ucwords($profile).'_'. $name.'" value="'.$s["value"].'" />';
                            break;
                    }
                    $html .= '<span class="afterText">'.$after.'</span>';
                    if (!empty($description)) $html .= '<span class="help-block">'.$description.'</span>';
                    $html .= '</td>';
                    $html .= '</tr>';
                    $groups[$s['group']][] = $html;
                    $html = '';
                }
            
            if (isset($groups['top'])) { //move 'top' group to the top
		$top = $groups['top'];
		unset($groups['top']);
		array_unshift($groups, $top);
            }

            if (isset($groups['Miscellaneous'])) {
		$misc = $groups['Miscellaneous'];
		unset($groups['Miscellaneous']);
		$groups['Miscellaneous'] = $misc; //move Miscellaneous to bottom
	    }

            foreach ($groups as $name => $group) {
                $i = 0;
		if ($name == '0') {
		    $name = '';
		    $group = preg_replace('/(^.*)(Class\sName)(.*?<span>)(All)(<\/span>.*?<span>)(MagicZoomPlus)(<\/span>.*)/is','$1Apply effect to all image links$3Yes$5No$7',$group);
		    
		}
                $group[count($group)-1] = str_replace('<tr','<tr class="last"',$group[count($group)-1]); //set "last" class
                $result .= '<h3 class="settingsTitle">'.$name.'</h3>
                            <div class="'.$toolAbr.'params">
                            <table class="params" cellspacing="0">';
                if (is_array($group)) {
		    foreach ($group as $g) {
			if (++$i%2==0) { //set stripes
			    if (strpos($g,'class="last"')) {
				$g = str_replace('class="last"','class="back last"',$g);
			    } else {
				$g = str_replace('<tr','<tr class="back"',$g);
			    }
			}
			$result .= $g;
		    }
                }
                $result .= '</table> </div>';
            }
            
      return $result;
}



function  magictoolbox_WordPress_MagicZoomPlus_styles() {
    if(!defined('MAGICTOOLBOX_MAGICZOOMPLUS_HEADERS_LOADED')) {
        $plugin = $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];
		if (function_exists('plugins_url')) {
			$core_url = plugins_url();
		} else {
			$core_url = get_option("siteurl").'/wp-content/plugins';
		}


        $path = preg_replace('/^.*?\/plugins\/(.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__)));
        
        $headers = $plugin->getHeadersTemplate($core_url."/{$path}/core");

        echo $headers;
        define('MAGICTOOLBOX_MAGICZOOMPLUS_HEADERS_LOADED', true);
    }
}



function  magictoolbox_WordPress_MagicZoomPlus_create($content) {


    $plugin = $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];
    
    
    /*set watermark options for all profiles START */
    $defaultParams = $plugin->params->getParams('default');
    $wm = array();
    $profiles = $plugin->params->getProfiles();
    foreach ($defaultParams as $id => $values) {
	if (($values['group']) == 'Watermark') {
	    $wm[$id] = $values;
	}
    }
    foreach ($profiles as $profile) {
	$plugin->params->appendParams($wm,$profile);
    }
    /*set watermark options for all profiles END */
    
    
    /*$pattern = "<img([^>]*)(?:>)(?:[^<]*<\/img>)?";
    $pattern = "(?:<a([^>]*)>.*?){$pattern}(.*?)(?:<\/a>)";*/
    $pattern = "(?:<a([^>]*)>)[^<]*<img([^>]*)(?:>)(?:[^<]*<\/img>)?(.*?)[^<]*?<\/a>";


    $oldContent = $content;
    
        $content = preg_replace_callback("/{$pattern}/is","magictoolbox_WordPress_MagicZoomPlus_callback",$content);
        if ($content == $oldContent) return $content;
        
        

      

    /*$content = str_replace('{MAGICTOOLBOX_'.strtoupper('magiczoomplus').'_MAIN_IMAGE_SELECTOR}',$GLOBALS['MAGICTOOLBOX_'.strtoupper('magiczoomplus').'_MAIN_IMAGE_SELECTOR'],$content);  //add main image selector to other
    $content = str_replace('{MAGICTOOLBOX_'.strtoupper('magiczoomplus').'_SELECTORS}','',$content); //if no selectors - remove constant
     onlyForModend  */


    if (!$plugin->params->checkValue('template','original') && $plugin->type == 'standard' && isset($GLOBALS['magictoolbox']['MagicZoomPlus']['main'])) {
        // template helper class
        require_once(dirname(__FILE__) . '/core/magictoolbox.templatehelper.class.php');
        MagicToolboxTemplateHelperClass::setPath(dirname(__FILE__).'/core/templates');
        MagicToolboxTemplateHelperClass::setOptions($plugin->params);
        if (!WordPress_MagicZoomPlus_page_check('WordPress')) { //do not render thumbs on category pages
            $thumbs = WordPress_MagicZoomPlus_get_prepared_selectors();
        } else {
            $thumbs = array();
        }
        
        if (isset($GLOBALS['MAGICTOOLBOX_'.strtoupper('MagicZoomPlus').'_SELECTORS']) && is_array($GLOBALS['MAGICTOOLBOX_'.strtoupper('MagicZoomPlus').'_SELECTORS'])) {
	    $thumbs = array_merge($thumbs,$GLOBALS['MAGICTOOLBOX_'.strtoupper('MagicZoomPlus').'_SELECTORS']);
        }
        
        if (!isset($GLOBALS['magictoolbox']['prods_info']['product_id']) && isset($post_id)) {
	    $GLOBALS['magictoolbox']['prods_info']['product_id'] = $post_id;
	} else if (!isset($GLOBALS['magictoolbox']['prods_info']['product_id']) && !isset($post_id)) {
	    $GLOBALS['magictoolbox']['prods_info']['product_id'] = '';
	}
        $html = MagicToolboxTemplateHelperClass::render(array(
            'main' => $GLOBALS['magictoolbox']['MagicZoomPlus']['main'],
            'thumbs' => (count($thumbs) >= 1) ? $thumbs : array(),
            'pid' => $GLOBALS['magictoolbox']['prods_info']['product_id'],
        ));

        $content = str_replace('MAGICTOOLBOX_PLACEHOLDER', $html, $content);
    } else if ($plugin->params->checkValue('template','original') || $plugin->type != 'standard') {
        $html = $GLOBALS['magictoolbox']['MagicZoomPlus']['main'];
        $content = str_replace('MAGICTOOLBOX_PLACEHOLDER', $html, $content);
    }


    return $content;
}
function  magictoolbox_WordPress_MagicZoomPlus_callback($matches) {
    $plugin = $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];

    
    if (!preg_match('/(jpg|png|jpeg|gif)/is',$matches[1])) return $matches[0];
    
    if($plugin->params->checkValue('class', 'all')) { //apply to all images
    
	$tool_class = strtolower($plugin->params->getValue('class'));
	
     
        if(preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*" . preg_quote('MagicZoom', '/') . "(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0])) { //already with class.. wrap it !
            $result =  $matches[0];
        } else { //need to add tool class
	    if (!preg_match('/<a[^<]*?class=/is',$matches[0])) { //a tag have no class
		$result = str_replace('<a','<a class="MagicZoom"',$matches[0]);
	    } else {
		$result = preg_replace('/(.*?)class=[\'\"](.*?)[\'\"](.*)/is','$1class="MagicZoom $2"$3',$matches[0]); //add tool class
	    }
        }
    } else {
	if (preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*Magic[A-Za-z]+?(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0])) {
	    $result = $matches[0];
	} else {
	    return $matches[0];
	}
    }
    $result = "<div class=\"MagicToolboxContainer\">{$result}</div>";





    return $result;

}

    
    
    

function WordPress_MagicZoomPlus_get_post_attachments()  {
    $args = array(
            'post_type' => 'attachment',
            'numberposts' => '-1',
            'post_status' => null,
            'post_parent' => $post_id
        );

    $attachments = get_posts($args);
    return $attachments;
}


    
    
    



function WordPressMagicZoomPlus_params_map_check ($profile = 'default', $group, $parameter) {
    $map = WordPressMagicZoomPlus_getParamsMap();
    if (isset($map[$profile][$group][$parameter])) return true;
    return false;
}
function WordPressMagicZoomPlus_getParamsMap () {
    $map = array(
		'default' => array(
			'Positioning and Geometry' => array(
				'zoomWidth',
				'zoomHeight',
				'zoomPosition',
				'zoomDistance',
			),
			'Multiple images' => array(
				'selectorTrigger',
				'transitionEffect',
			),
			'Miscellaneous' => array(
				'lazyZoom',
				'rightClick',
				'class',
				'show-message',
				'message',
			),
			'Zoom mode' => array(
				'zoomMode',
				'zoomOn',
				'upscale',
				'smoothing',
				'variableZoom',
				'zoomCaption',
			),
			'Expand mode' => array(
				'expand',
				'expandZoomMode',
				'expandZoomOn',
				'expandCaption',
				'closeOnClickOutside',
				'cssClass',
			),
			'Hint' => array(
				'hint',
				'textHoverZoomHint',
				'textClickZoomHint',
				'textExpandHint',
				'textBtnClose',
				'textBtnNext',
				'textBtnPrev',
			),
			'Mobile' => array(
				'zoomModeForMobile',
				'textHoverZoomHintForMobile',
				'textClickZoomHintForMobile',
				'textExpandHintForMobile',
			),
		),
	);
    return $map;
}

function WordPressMagicZoomPlus_getParamsProfiles () {

    $blocks = array(
		'default' => 'General',
	);
    
    return $blocks;
}
?>
