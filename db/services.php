<?php

$services = array(
    'uploadfiletocoursepluginservice' => array(                                                //the name of the web service
        'functions' => array ('custom_upload_file_to_course'), //web service functions of this service
        'requiredcapability' => '',                //if set, the web service user need this capability to access
        //any function of this service. For example: 'some/capability:specified'
        'restrictedusers' =>0,                                             //if enabled, the Moodle administrator must link some user to this service
        //into the administration
        'enabled'=>1,                                                       //if enabled, the service can be reachable on a default installation
    )
);

$functions = array(
    'custom_upload_file_to_course' => array(         //web service function name
        'classname'   => 'custom_upload_file_to_course',  //class containing the external function
        'methodname'  => 'upload_file_to_course',          //external function name
        'classpath'   => 'local/uploadfiletocourseplugin/externallib.php',  //file containing the class/external function
        'description' => 'Uploads a file to a specific course.',    //human readable description of the web service function
        'type'        => 'write',                  //database rights of the web service function (read, write)
        'ajax' => true,        // is the service available to 'internal' ajax calls.
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
    ),
);