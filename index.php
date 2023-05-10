<?php

require 'vendor/autoload.php';

use React\EventLoop\Loop;

function  submit()
{
    $job = getdata();
    $jobId = "job:" . time();
    $job[$jobId] = 0;
    echo  "\n" . $jobId . " \n\n";
    updateJob($jobId, 0, 0);
}

function  checkStatus($jobId)
{
    $job = getdata();
    if ($job[$jobId]) {
        echo  "\n " . sprintf("Jobstatus: %u%%", $job[$jobId]) . " \n\n";
    }
    echo "Unidentified job";
}

function updateJob($jobId, $progress, $time = 0)
{
    $job = getdata();
    $job[$jobId] = $progress;
    updatedata($job);
    if ($progress == 100) return;

    updateJob($jobId, $progress + 10, 1);

    // Loop::addTimer(4, function () use ($jobId, $progress) {
    //     $job = getdata();
    //     $job[$jobId] = $progress + 10;
    //     updatedata($job);
    //     if ($progress == 100) {
    //         return;
    //     }
    //     updateJob($jobId, $progress + 10);
    // });
    // Loop::run();
}



function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function de($data)
{
    throw new Exception("Error Processing Request", 1);
    die();
}

function routes($route_path, $method)
{
    $segment = explode('/', $route_path);
    $path =  $segment[1];
    if ($path ==  "checkstatus" && $method == "GET") {

        if (isset($_GET['jobId'])) {
            $jobId = $_GET['jobId'];
            checkStatus($jobId);
        }
        de("No job_id specified");
    }

    if ($path ==  "submit" && $method == "POST") {
        submit();
    }

    if (!in_array($path, ["submit", "checkstatus"])) {
        echo "Please 😎 kindly getout of here";
    }
}

function getdata()
{
    // $cacheTime = (3600 * 24); // cache for 24 hour  && time() - filemtime($cacheFile) < $cacheTime

    // Check if cached file exists and is not expired
    if (file_exists('cache.json')) {
        // Load cached data from file
        $jsonData = file_get_contents('cache.json');

        // Decode JSON data to PHP array or object
        $data = json_decode($jsonData, true);

        return $data;
    }
}

function updatedata($job)
{
    // Check if cached file exists and is not expired
    if (file_exists('cache.json')) {

        $jsonData = json_encode($job);
        $result = file_put_contents('cache.json', $jsonData);
    }
}

routes($_SERVER['PATH_INFO'], $_SERVER['REQUEST_METHOD']);


// Start php server: php -S localhost:8080 
// make curl post request: curl -X POST http://localhost:8080/submit
// make curl get request: curl http://localhost:8080/checkstatus?jobId=job:3456732