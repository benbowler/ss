<?php

/*
 * Google_Maps
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
 
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Overload.php';
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Coordinate.php';
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Point.php';
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Marker.php';
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Marker/Cluster.php';
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Clusterer.php';
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Path.php';
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Control.php';
require_once BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Google/Maps/Infowindow.php';

class Google_Maps extends Google_Maps_Overload {
        
    public function create($type, $params = array()) {
        $class_name = 'Google_Maps_' . ucfirst($type);
        $file_name  = str_replace('_', DIRECTORY_SEPARATOR, $class_name).'.php';
        require_once $file_name;
        return new $class_name($params);
    }
    
}