<?php
// This client for local_wstemplate is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//

/**
 * XMLRPC client for Moodle 2 - local_wstemplate
 *
 * This script does not depend of any Moodle code,
 * and it can be called from a browser.
 *
 * @authorr Jerome Mouneyrac
 */

/// MOODLE ADMINISTRATION SETUP STEPS
// 1- Install the plugin
// 2- Enable web service advance feature (Admin > Advanced features)
// 3- Enable XMLRPC protocol (Admin > Plugins > Web services > Manage protocols)
// 4- Create a token for a specific user and for the service 'My service' (Admin > Plugins > Web services > Manage tokens)
// 5- Run this script directly from your browser: you should see 'Hello, FIRSTNAME'


$token = '9e4296d873a9a29ab7e2cb928f682e11';
$function = 'custom_upload_file_to_course';
$domainname = 'http://172.28.0.3/moodle/webservice/rest/server.php';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $domainname . '?wstoken=' . $token . '&wsfunction=' . $function . '&moodlewsrestformat=json',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => array('courseid' => '4','filename' => 'Einkommenserklärung.pdf','repo_upload_file'=> new CURLFILE('/media/alex/721CF6BD1CF67C03/Users/Alex/Documents/Einkommenerklärung_Pabinger.pdf')),
    CURLOPT_HTTPHEADER => array(
        "Content-Type: multipart/form-data;"
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;