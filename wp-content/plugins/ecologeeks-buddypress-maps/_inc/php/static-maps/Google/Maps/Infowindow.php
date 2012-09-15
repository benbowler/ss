<?php

/*
 * Google_Maps_Marker
 *
 * Copyright (c) 2008 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/google_maps
 *
 * Revision: $Id$
 *
 */
 
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Coordinate.php';
 
class Google_Maps_Infowindow extends Google_Maps_Overload {
    
    protected $marker;
    protected $content;
    protected $display = 'none';
    protected $template = '
<!-- Infobubble -->
<div id="%s" class="bubble" style="position: relative; left: %dpx; top: %dpx; display: %s;">  
  <div class="bubble-top-left">
    <img class="bubble-top-left" src="http://maps.google.com/intl/en_ALL/mapfiles/iw2.png" />
  </div>
  <div class="bubble-top-right">
    <img class="bubble-top-right" src="http://maps.google.com/intl/en_ALL/mapfiles/iw2.png" />
  </div>
  <div class="bubble-pointer">
    <img class="bubble-pointer" src="http://maps.google.com/intl/en_ALL/mapfiles/iw2.png" />
  </div>
  <div class="bubble-bottom-left">
    <img class="bubble-bottom-left" src="http://maps.google.com/intl/en_ALL/mapfiles/iw2.png" />
  </div>
  <div class="bubble-bottom-right">
    <img class="bubble-bottom-right" src="http://maps.google.com/intl/en_ALL/mapfiles/iw2.png" />
  </div>
  <div class="bubble-border-top"></div>
  <div class="bubble-border-left-right"></div>
  <div class="bubble-border-bottom"></div>
  <p class="bubble-close">
    <a href="%s"><img class="bubble-close" src="http://maps.google.com/intl/en_ALL/mapfiles/iw_close.gif" /></a>
  </p>
  <div class="bubble-content">
    %s
  </div>
</div>
<!-- /Infobubble -->
';
    
    /**
    * Class constructor.
    *
    * @param    string $content  
    * @param    array $params Optional parameters (unused for now)
    * @return   object
    */
    public function __construct($content, $params = array()) {
        $this->setContent($content);
        $this->setProperties($params);
    }
    
    /**
    * Show infowindow.
    *
    * @return   boolean
    */
    public function show() {
        $this->setDisplay('block');
    }

    /**
    * Hide infowindow.
    *
    * @return   boolean
    */
    public function hide() {
        $this->setDisplay('none');
    }
    
    /**
    * Is infowindow currently visible?
    *
    * @return   boolean
    */
    public function isVisible() {
        return $this->getDisplay() == 'block' ? true : false;
    }
    
    public function getCloseUrl(Google_Maps_Static $map) {
        return preg_replace('/infowindow=.*&/', 'infowindow=&', $map->toUrl('?', false));
    }
    
    public function toHtml(Google_Maps_Static $map) {
        $template = $this->getTemplate();
        $location = $this->getMarker();
        return sprintf($template, $location->getId(),
                                  $location->getContainerX($map) - 160, 
                                  $location->getContainerY($map) - 235, 
                                  $this->getDisplay(),
                                  $this->getCloseUrl($map),
                                  $this->getContent());
    }
        
    public function __toString() {
    }
        
}