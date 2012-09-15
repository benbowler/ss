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
 
require_once 'Google/Maps/Coordinate.php';
 
class Google_Maps_Marker extends Google_Maps_Location {
    
    protected $coordinate;
    protected $color;
    protected $size;
    protected $character;
    protected $infowindow;
    
    protected $format = '%01.8f,%01.8f,%s%s%s';
    
    protected $visible = false;
    protected $id;
    static private $counter = 1;
    
    /**
    * Class constructor.
    *
    * @param    object $location Google_Maps_Coordinate|Point 
    * @param    array $params Optional parameters for color, size and character.
    * @return   object
    */
    public function __construct($location, $params = array()) {
        $this->setCoordinate($location);
        $this->setProperties($params);
        $this->setId('marker_' . self::$counter++);
    }
    
    /**
    * Return imagemap area. Used for clickable markers in static map.
    *
    * @return   string
    */
    public function toArea(Google_Maps_Static $map) {        
        $marker_x  = $this->getContainerX($map);
        $marker_y  = $this->getContainerY($map) - 20;
        $marker_id = $this->getId();
        
        $string = 'infowindow=' . $marker_id . '&';
        $url = preg_replace('/infowindow=.*&/', $string, $map->toQueryString());
        
        return sprintf('<area shape="circle" coords="%d,%d,12" href="?%s" name="%s">',
                        $marker_x, $marker_y, $url, $marker_id);
    }

    /**
    * Return marker as coordinate in Google Maps.
    *
    * @return   object Google_Maps_Coordinate
    */
    public function toCoordinate() {
        return $this->getCoordinate();
    }
    
    /**
    * Return marker as pixel point in Google Maps.
    *
    * @return   object Google_Maps_Point
    */
    public function toPoint() {
        return $this->getCoordinate()->toPoint();
    }
    
    /**
    * Return latitude of marker coordinate.
    *
    * @return   float Latitude
    */
    public function getLat() {
        return $this->getCoordinate()->getLat();
    }

    /**
    * Return longitude of path coordinate.
    *
    * @return   float Longitude
    */
    public function getLon() {
        return $this->getCoordinate()->getLon();
    }
    
    /**
    * Return color of marker.
    *
    * @return   string color
    */
    public function getColor() {
        $retval = $this->color;
        if ($this->getCharacter() && !trim($retval)) {
            $retval = 'red';
        }
        return $retval;
    }
            
    public function __toString() {
        $retval = sprintf($this->getFormat(), $this->getLat(), $this->getLon(), 
                          $this->getSize(), $this->getColor(), $this->getCharacter());
        return preg_replace('/,$/', '', $retval);
    }
        
}