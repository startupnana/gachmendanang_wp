<?php

if(!defined('MagicZoomPlusModuleCoreClassLoaded')) {

    define('MagicZoomPlusModuleCoreClassLoaded', true);

    require_once(dirname(__FILE__).'/magictoolbox.params.class.php');

    /**
     * MagicZoomPlusModuleCoreClass
     *
     */
    class MagicZoomPlusModuleCoreClass {

        /**
         * MagicToolboxParamsClass class
         *
         * @var   MagicToolboxParamsClass
         *
         */
        var $params;

        /**
         * Tool type
         *
         * @var   string
         *
         */
        var $type = 'standard';

        /**
         * Constructor
         *
         * @return void
         */
        function MagicZoomPlusModuleCoreClass() {
            $this->params = new MagicToolboxParamsClass();
            $this->loadDefaults();
            $this->params->setMapping(array(
                'zoomWidth' => array('0' => 'auto'),
                'zoomHeight' => array('0' => 'auto'),
                'expandCaption' => array('Yes' => 'true', 'No' => 'false'),
                'upscale' => array('Yes' => 'true', 'No' => 'false'),
                'lazyZoom' => array('Yes' => 'true', 'No' => 'false'),
                'closeOnClickOutside' => array('Yes' => 'true', 'No' => 'false'),
                'rightClick' => array('Yes' => 'true', 'No' => 'false'),
                'transitionEffect' => array('Yes' => 'true', 'No' => 'false'),
                'variableZoom' => array('Yes' => 'true', 'No' => 'false'),
                'autostart' => array('Yes' => 'true', 'No' => 'false'),
                'cssClass' => array('blurred' => null, 'dark' => 'dark-bg', 'white' => 'white-bg'),
                'smoothing' => array('Yes' => 'true', 'No' => 'false'),
            ));
        }

        /**
         * Method to get headers string
         *
         * @param string $jsPath  Path to JS file
         * @param string $cssPath Path to CSS file
         *
         * @return string
         */
        function getHeadersTemplate($jsPath = '', $cssPath = null) {
            //to prevent multiple displaying of headers
            if(!defined('MAGICZOOMPLUS_MODULE_HEADERS')) {
                define('MAGICZOOMPLUS_MODULE_HEADERS', true);
            } else {
                return '';
            }
            if($cssPath == null) {
                $cssPath = $jsPath;
            }
            $headers = array();
            $headers[] = '<!-- Magic Zoom Plus WordPress module version v6.0.17 [v1.5.14:v5.1.1] -->';
            $headers[] = '<link type="text/css" href="'.$cssPath.'/magiczoomplus.css" rel="stylesheet" media="screen" />';
            $headers[] = '<link type="text/css" href="'.$cssPath.'/magiczoomplus.module.css" rel="stylesheet" media="screen" />';
            $headers[] = '<script type="text/javascript" src="'.$jsPath.'/magiczoomplus.js"></script>';
            $headers[] = '<script type="text/javascript" src="'.$jsPath.'/magictoolbox.utils.js"></script>';
            $headers[] = $this->getOptionsTemplate();
            return "\r\n".implode("\r\n", $headers)."\r\n";
        }

        /**
         * Method to get options string
         *
         * @return string
         */
        function getOptionsTemplate() {
            $autostart = $this->params->getValue('autostart');//NOTE: true | false
            if($autostart !== null) {
                $autostart = "\n\t\t'autostart':".$autostart.',';
            } else {
                $autostart = '';
            }
            return "<script type=\"text/javascript\">\n\tvar mzOptions = {{$autostart}\n\t\t".$this->params->serialize(true, ",\n\t\t")."\n\t}\n</script>\n".
                   "<script type=\"text/javascript\">\n\tvar mzMobileOptions = {".
                   "\n\t\t'zoomMode':'".str_replace('\'', '\\\'', $this->params->getValue('zoomModeForMobile'))."',".
                   "\n\t\t'textHoverZoomHint':'".str_replace('\'', '\\\'', $this->params->getValue('textHoverZoomHintForMobile'))."',".
                   "\n\t\t'textClickZoomHint':'".str_replace('\'', '\\\'', $this->params->getValue('textClickZoomHintForMobile'))."',".
                   "\n\t\t'textExpandHint':'".str_replace('\'', '\\\'', $this->params->getValue('textExpandHintForMobile'))."'".
                   "\n\t}\n</script>";
        }

        /**
         * Method to get main image HTML
         *
         * @param array $params Params
         *
         * @return string
         */
        function getMainTemplate($params) {
            $img = '';
            $thumb = '';
            $id = '';
            $alt = '';
            $title = '';
            $width = '';
            $height = '';
            $link = '';
            $group = '';//data-gallery

            extract($params);

            if(empty($img)) {
                return false;
            }
            if(empty($thumb)) {
                $thumb = $img;
            }
            if(empty($id)) {
                $id = md5($img);
            }

            if(!empty($title)) {
                $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
                if(empty($alt)) {
                    $alt = $title;
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }
                $title = " title=\"{$title}\"";
            } else {
                $title = '';
                if(empty($alt)) {
                    $alt = '';
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }
            }

            if(empty($width)) {
                $width = '';
            } else {
                $width = " width=\"{$width}\"";
            }
            if(empty($height)) {
                $height = '';
            } else {
                $height = " height=\"{$height}\"";
            }

            if($this->params->checkValue('show-message', 'Yes')) {
                $message = '<div class="MagicToolboxMessage">'.$this->params->getValue('message').'</div>';
            } else {
                $message = '';
            }

            if(empty($link)) {
                $link = '';
            } else {
                $link = " data-link=\"{$link}\"";
            }

            if(empty($group)) {
                $group = '';
            } else {
                $group = " data-gallery=\"{$group}\"";
            }

            $options = $this->params->serialize();

            if(!empty($options)) {
                $options = " data-options=\"{$options}\"";
            }

            $mobileOptions = array(
                'zoomModeForMobile'          => 'zoomMode',
                'textHoverZoomHintForMobile' => 'textHoverZoomHint',
                'textClickZoomHintForMobile' => 'textClickZoomHint',
                'textExpandHintForMobile'    => 'textExpandHint',
            );
            $profile = $this->params->getProfile();
            foreach($mobileOptions as $mId => $option) {
                if(!$this->params->paramExists($mId, $profile) || $this->params->checkValue($mId, $this->params->getValue($mId, $this->params->generalProfile), $profile)) {
                    $mobileOptions[$mId] = '';
                    continue;
                }
                $mobileOptions[$mId] = "{$option}:".str_replace('"', '&quot;', $this->params->getValue($mId, $profile)).';';
            }
            $mobileOptions = implode('', $mobileOptions);
            if(!empty($mobileOptions)) {
                $options .= " data-mobile-options=\"{$mobileOptions}\"";
            }

            return "<a id=\"MagicZoomPlusImage{$id}\" class=\"MagicZoom\" href=\"{$img}\"{$group}{$link}{$title}{$options}><img itemprop=\"image\" src=\"{$thumb}\" alt=\"{$alt}\"{$width}{$height} /></a>{$message}";
        }

        /**
         * Method to get selectors HTML
         *
         * @param array $params Params
         *
         * @return string
         */
        function getSelectorTemplate($params) {
            $img = '';
            $medium = '';
            $thumb = '';
            $id = '';
            $alt = '';
            $title = '';
            $width = '';
            $height = '';

            extract($params);

            if(empty($img)) {
                return false;
            }
            if(empty($medium)) {
                $medium = $img;
            }
            if(empty($thumb)) {
                $thumb = $img;
            }

            if(empty($id)) {
                $id = md5($img);
            }

            if(!empty($title)) {
                $title = htmlspecialchars(htmlspecialchars_decode($title, ENT_QUOTES));
                if(empty($alt)) {
                    $alt = $title;
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }
                $title = " title=\"{$title}\"";
            } else {
                $title = '';
                if(empty($alt)) {
                    $alt = '';
                } else {
                    $alt = htmlspecialchars(htmlspecialchars_decode($alt, ENT_QUOTES));
                }
            }

            if(empty($width)) {
                $width = '';
            } else {
                $width = " width=\"{$width}\"";
            }
            if(empty($height)) {
                $height = '';
            } else {
                $height = " height=\"{$height}\"";
            }

            return "<a data-zoom-id=\"MagicZoomPlusImage{$id}\" href=\"{$img}\" data-image=\"{$medium}\"{$title}><img src=\"{$thumb}\" alt=\"{$alt}\"{$width}{$height} /></a>";
        }

        /**
         * Method to load defaults options
         *
         * @return void
         */
        function loadDefaults() {
            $params = array(
				"zoomWidth"=>array("id"=>"zoomWidth","group"=>"Positioning and Geometry","order"=>"20","default"=>"auto","label"=>"Width of zoom window","description"=>"pixels or percentage, e.g. 400 or 100%.","type"=>"text","scope"=>"tool"),
				"zoomHeight"=>array("id"=>"zoomHeight","group"=>"Positioning and Geometry","order"=>"30","default"=>"auto","label"=>"Height of zoom window","description"=>"pixels or percentage, e.g. 400 or 100%.","type"=>"text","scope"=>"tool"),
				"zoomPosition"=>array("id"=>"zoomPosition","group"=>"Positioning and Geometry","order"=>"40","default"=>"right","label"=>"Position of zoom window","type"=>"array","subType"=>"radio","values"=>array("top","right","bottom","left","inner"),"scope"=>"tool"),
				"zoomDistance"=>array("id"=>"zoomDistance","group"=>"Positioning and Geometry","order"=>"50","default"=>"15","label"=>"Zoom distance","description"=>"Distance between small image and zoom window (in pixels).","type"=>"num","scope"=>"tool"),
				"selectorTrigger"=>array("id"=>"selectorTrigger","advanced"=>"1","group"=>"Multiple images","order"=>"10","default"=>"click","label"=>"Switch between images on","description"=>"Mouse event used to swtich between multiple images.","type"=>"array","subType"=>"radio","values"=>array("click","hover"),"scope"=>"tool"),
				"transitionEffect"=>array("id"=>"transitionEffect","advanced"=>"1","group"=>"Multiple images","order"=>"20","default"=>"Yes","label"=>"Use transition effect when switching images","description"=>"Whether to enable dissolve effect when switching between images.","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),
				"lazyZoom"=>array("id"=>"lazyZoom","group"=>"Miscellaneous","order"=>"10","default"=>"No","label"=>"Lazy load of zoom image","description"=>"Whether to load large image on demand (on first activation).","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),
				"rightClick"=>array("id"=>"rightClick","group"=>"Miscellaneous","order"=>"20","default"=>"No","label"=>"Right-click menu on image","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),
				"class"=>array("id"=>"class","group"=>"Miscellaneous","order"=>"20","default"=>"MagicZoomPlus","label"=>"Class Name","type"=>"array","subType"=>"select","values"=>array("all","MagicZoomPlus")),
				"show-message"=>array("id"=>"show-message","group"=>"Miscellaneous","order"=>"370","default"=>"No","label"=>"Show message under images","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),
				"message"=>array("id"=>"message","group"=>"Miscellaneous","order"=>"380","default"=>"Move your mouse over image or click to enlarge","label"=>"Enter message to appear under images","type"=>"text"),
				"zoomMode"=>array("id"=>"zoomMode","group"=>"Zoom mode","order"=>"10","default"=>"zoom","label"=>"Zoom mode","description"=>"How to zoom image. off - disable zoom.","type"=>"array","subType"=>"radio","values"=>array("zoom","magnifier","preview","off"),"scope"=>"tool"),
				"zoomOn"=>array("id"=>"zoomOn","group"=>"Zoom mode","order"=>"20","default"=>"hover","label"=>"Zoom on","description"=>"When to activate zoom.","type"=>"array","subType"=>"radio","values"=>array("hover","click"),"scope"=>"tool"),
				"upscale"=>array("id"=>"upscale","advanced"=>"1","group"=>"Zoom mode","order"=>"30","default"=>"Yes","label"=>"Upscale image","description"=>"Whether to scale up the large image if its original size is not enough for a zoom effect.","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),
				"smoothing"=>array("id"=>"smoothing","advanced"=>"1","group"=>"Zoom mode","order"=>"35","default"=>"Yes","label"=>"Smooth zoom movement","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),
				"variableZoom"=>array("id"=>"variableZoom","advanced"=>"1","group"=>"Zoom mode","order"=>"40","default"=>"No","label"=>"Variable zoom","description"=>"Whether to allow changing zoom ratio with mouse wheel.","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),
				"zoomCaption"=>array("id"=>"zoomCaption","group"=>"Zoom mode","order"=>"50","default"=>"off","label"=>"Caption in zoom window","description"=>"Position of caption on zoomed image. off - disable caption on zoom window.","type"=>"array","subType"=>"radio","values"=>array("top","bottom","off"),"scope"=>"tool"),
				"expand"=>array("id"=>"expand","group"=>"Expand mode","order"=>"10","default"=>"window","label"=>"Expand mode","description"=>"How to show expanded view. off - disable expanded view.","type"=>"array","subType"=>"radio","values"=>array("window","fullscreen","off"),"scope"=>"tool"),
				"expandZoomMode"=>array("id"=>"expandZoomMode","group"=>"Expand mode","order"=>"20","default"=>"zoom","label"=>"Expand zoom mode","description"=>"How to zoom image in expanded view. off - disable zoom in expanded view.","type"=>"array","subType"=>"radio","values"=>array("zoom","magnifier","off"),"scope"=>"tool"),
				"expandZoomOn"=>array("id"=>"expandZoomOn","group"=>"Expand mode","order"=>"21","default"=>"click","label"=>"Expand zoom on","description"=>"When and how activate zoom in expanded view. ‘always’ - zoom automatically activates upon entering the expanded view and remains active.","type"=>"array","subType"=>"radio","values"=>array("click","always"),"scope"=>"tool"),
				"expandCaption"=>array("id"=>"expandCaption","group"=>"Expand mode","order"=>"30","default"=>"Yes","label"=>"Show caption in expand window","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),
				"closeOnClickOutside"=>array("id"=>"closeOnClickOutside","group"=>"Expand mode","order"=>"40","default"=>"Yes","label"=>"Close expanded image on click outside","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),
				"cssClass"=>array("id"=>"cssClass","group"=>"Expand mode","order"=>"50","default"=>"blurred","label"=>"Background behind the enlarged image","type"=>"array","subType"=>"radio","values"=>array("blurred","dark","white"),"scope"=>"tool"),
				"hint"=>array("id"=>"hint","group"=>"Hint","order"=>"10","default"=>"once","label"=>"Display hint to suggest image is zoomable","description"=>"How to show hint. off - disable hint.","type"=>"array","subType"=>"radio","values"=>array("once","always","off"),"scope"=>"tool"),
				"textHoverZoomHint"=>array("id"=>"textHoverZoomHint","advanced"=>"1","group"=>"Hint","order"=>"20","default"=>"Hover to zoom","label"=>"Hint to suggest image is zoomable (on hover)","description"=>"Hint that shows when zoom mode is enabled, but inactive, and zoom activates on hover (Zoom on: hover).","type"=>"text","scope"=>"tool"),
				"textClickZoomHint"=>array("id"=>"textClickZoomHint","advanced"=>"1","group"=>"Hint","order"=>"21","default"=>"Click to zoom","label"=>"Hint to suggest image is zoomable (on click)","description"=>"Hint that shows when zoom mode is enabled, but inactive, and zoom activates on click (Zoom on: click).","type"=>"text","scope"=>"tool"),
				"textExpandHint"=>array("id"=>"textExpandHint","advanced"=>"1","group"=>"Hint","order"=>"30","default"=>"Click to expand","label"=>"Hint to suggest image is expandable","description"=>"Hint that shows when zoom mode activated, or in inactive state if zoom mode is disabled.","type"=>"text","scope"=>"tool"),
				"textBtnClose"=>array("id"=>"textBtnClose","group"=>"Hint","order"=>"40","default"=>"Close","label"=>"Hint for “close” button","description"=>"Text label that appears on mouse over the “close” button in expanded view.","type"=>"text","scope"=>"tool"),
				"textBtnNext"=>array("id"=>"textBtnNext","group"=>"Hint","order"=>"50","default"=>"Next","label"=>"Hint for “next” button","description"=>"Text label that appears on mouse over the “next” button arrow in expanded view.","type"=>"text","scope"=>"tool"),
				"textBtnPrev"=>array("id"=>"textBtnPrev","group"=>"Hint","order"=>"60","default"=>"Previous","label"=>"Hint for “previous” button","description"=>"Text label that appears on mouse over the “previous” button arrow in expanded view.","type"=>"text","scope"=>"tool"),
				"zoomModeForMobile"=>array("id"=>"zoomModeForMobile","group"=>"Mobile","order"=>"10","default"=>"zoom","label"=>"Zoom mode","description"=>"How to zoom image. off - disable zoom.","type"=>"array","subType"=>"radio","values"=>array("zoom","magnifier","off"),"scope"=>"profile"),
				"textHoverZoomHintForMobile"=>array("id"=>"textHoverZoomHintForMobile","advanced"=>"1","group"=>"Mobile","order"=>"20","default"=>"Touch to zoom","label"=>"Hint to suggest image is zoomable (on hover)","description"=>"Hint that shows when zoom mode is enabled, but inactive, and zoom activates on hover (Zoom on: hover).","type"=>"text","scope"=>"profile"),
				"textClickZoomHintForMobile"=>array("id"=>"textClickZoomHintForMobile","advanced"=>"1","group"=>"Mobile","order"=>"21","default"=>"Double tap to zoom","label"=>"Hint to suggest image is zoomable (on click)","description"=>"Hint that shows when zoom mode is enabled, but inactive, and zoom activates on click (Zoom on: click).","type"=>"text","scope"=>"profile"),
				"textExpandHintForMobile"=>array("id"=>"textExpandHintForMobile","advanced"=>"1","group"=>"Mobile","order"=>"30","default"=>"Tap to expand","label"=>"Hint to suggest image is expandable","description"=>"Hint that shows when zoom mode activated, or in inactive state if zoom mode is disabled.","type"=>"text","scope"=>"profile")
			);
            $this->params->appendParams($params);
        }

    }

}

?>
