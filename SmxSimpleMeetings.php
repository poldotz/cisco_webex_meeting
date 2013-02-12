<?php
/*
 * This file handles all includes needed to use the SmxSimpleMeetings library.
 * If you are already using an autoloader you can configure to it to find 
 * classes in the vendor/ folder.
 */


/*
 * Base classes are required for all service providers
 */
require_once 'Interfaces.php';
require_once 'adapters/BaseClasses/Site.php';
require_once 'adapters/BaseClasses/Meeting.php';
if(!class_exists('\\Zend\\Http\\Client')){
    require_once 'vendor/zendframework/zendframework/library/Zend/Http/Client.php';
}

/*
 * Factory class is required
 */
require_once 'adapters/Factory.php';

/*
 * WebEx classes.
 * This section can be commented out if you do not plan to use WebEx.
 */
require_once 'adapters/WebEx/Attendee.php';
require_once 'adapters/WebEx/AttendeeList.php';
require_once 'adapters/WebEx/Meeting.php';
require_once 'adapters/WebEx/MeetingList.php';
require_once 'adapters/WebEx/User.php';
require_once 'adapters/WebEx/UserList.php';