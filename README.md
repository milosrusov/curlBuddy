# curlBuddy
Simple and super easy to use PHP cURL handler wrapper.

# General Usage
######// Include curlBuddy in your project
include_once('/path/to/curlBuddy/curlBuddy.php');
######// Create a new instance of curlBuddy
$curl_buddy = new curlBuddy();
######// Start a new curl request
$post_h = $curl_buddy->newCurl()->post('https://api.somedomain.com/v3/somefile.xml');
######// Set POST body data (optional)
######// The data can be a string or an associated array
$post_h->setData('<?xml version="1.0" encoding="UTF-8"?><request>...</request>');
######// Set custom headers (optional)
$post_h->setHeader('Content-Type:', 'application/xml; charset=UTF-8');
######// Send the request
$post_h->send();
######// Retrieve the response (optional)
$response = $post_h->response();

# Supported Verbs (methods)
######// POST
$post_h = $curl_buddy->newCurl()->post(string $url);
######// GET
$get_h = $curl_buddy->newCurl()->get(string $url);
######// PUT
$put_h = $curl_buddy->newCurl()->put(string $url);
######// PATCH
$patch_h = $curl_buddy->newCurl()->patch(string $url);
######// DELETE
$delete_h = $curl_buddy->newCurl()->delete(string $url);

# Error Handling
######// Check to see if there was an error
if($post_h->hasError()){ ... }
######// Getting the error message
$error_message = $post_h->errorMessage();
