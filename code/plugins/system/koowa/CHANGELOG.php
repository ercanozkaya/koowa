<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
* @category		Koowa
* @package      Koowa
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link     	http://www.koowa.org
*/
defined('_JEXEC') or die('Restricted access');
?>

Legend:

 * -> Security Fix
 # -> Bug Fix
 $ -> Language fix or change
 + -> Addition
 ^ -> Change
 - -> Removed
 ! -> Note
 
2008-09-17 Mathias Verraes
 + Added KInflector::addWord(), removed the feature from singualrize and pluralize 
 
2008-09-16 Mathias Verraes
 + Added KFilterDigit
 
2008-09-14 Johan Janssens
 ^ Moved KHelperClass to KMixinClass
 
2008-09-13 Mathias Verraes
 + Automatically added tokens in forms  can now be overriden using @token(bool $reuse)
 ^ Tokens can be reused from the previous request
 
2008-09-11 Johan Janssens
 ^ Refactored KViewHelper, added format specifier and moved current helpers into
   html subdirectory.
 ^ Renamed Koowa::getMediaURL to Koowa::getURL
 
2008-09-10 Johan Janssens
 + Added KDocument package
 + Added toString method to JHelperArray
 ^ Improved loader to be able to look for files in directories with the same name
 
2008-09-08 Johan Janssens
 + Added KHelperString class to easily handle multi-byte strings 
  
2008-09-03 Johan Janssens
 + Added KFactory::tmp method to create an object witout storing it in the factory 
   container
 ^ Reworked KRequest::get to also accept filter names as strings.
 ^ Renamed KRequest to KInput

2008-08-27 Johan Janssens
 ^ Completely refactored the factory package, implemented support for factory adapters
 + Added koow, joomla and component specific factory adapters
 - Removed KViewAbstract::getFileName, now handled by the component factory adapter
 ^ Fixed docblocks, added @uses to package blocks and @throws to function blocks
 ^ Renamed KPatternClass to KHelperClass and moved to helper package

2008-08-25 Mathias Verraes
 + Added KFilterAscii, KFilterArray*
 
2008-08-24 Johan Janssens
 - Removed KObject::getError, setError and getErrors
 ^ Replaced all calls to JError::raiseError by throwing KExceptions
 ! Need to have a look at how to deal with JError::raiseNotice and raiseWarning  

2008-08-24 Mathias Verraes
 + Added KFilter and KRequest

2008-08-22 Mathias Verraes
 + Added changelog, license and readme 