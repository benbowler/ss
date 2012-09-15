<?php

/*
 * Google_Maps_Clusterer_Distance
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

class Google_Maps_Clusterer_Distance extends Google_Maps_Overload {
    
    protected $distance     = 20;
    protected $unit         = 'pixel';
    protected $color        = 'blue';
    protected $marker_sizes = array('mid','mid','mid','mid','mid','','','','');
    
    public function process(array $markers, Google_Maps_Static $map) {
        $clustered = array();
        $distance  = $this->getDistance();
        $unit      = $this->getUnit();
        while (count($markers)) {
            $marker = array_pop($markers);
            $cluster = new Google_Maps_Marker_Cluster();
            foreach ($markers as $key => $target) {
                if (($distance > $marker->distanceTo($target, $unit, $map))) {
                    unset($markers[$key]);
                    $amount = $cluster->addMarker($target);
                    /* Marker can display max number 9, so break out of  */
                    /* the loop when we reach it. One is added outside of the loop. */
                    if (8 == $amount) {
                        break 1;
                    }
                    
                }
            }

            if (count($cluster->getMarkers()) > 0) {
                $sizes = $this->getMarkerSizes();
                $count = $cluster->addMarker($marker);
                $cluster->setCharacter($count);
                $cluster->setColor('blue');
                $cluster->setSize($sizes[$count - 1]);
                $clustered[] = $cluster;
            } else {
                $clustered[] = $marker;
            };
        }
        return $clustered;
    }
     
}