<?php
require_once 'SkylabStudio.php';

$api = new SkylabStudio("16V7LPczUNXb6cdY7V15G5s5");

// $res = $api->getJob(8233);

// $res = $api->getJobByName("b7280bc1-8cb9-4a15-84b8-cee35ebf2a8b");
// $res = $api->getJobsInFront(8233);
// $res = $api->listJobs();

function randomName($model) 
{
  $min = 1;
  $max = 1000000000;
  $random_number = rand($min, $max);
  $name = 'php-sdk-' . $model . '-' . $random_number;

  return $name;
};

// echo(randomName('job'));

// $data = [
//   'name' => randomName('job'),
//   'profile_id' => 24
// ];

// $res = $api->createJob($data);

// $res = $api->updateJob(8233, $data);

// $res = $api->deleteJob(8233);


// PHOTOS
// $res = $api->getPhoto(214738);

$res = $api->uploadJobPhoto('/Users/brandonliu/Desktop/small sample photos/IMG_0107.JPG', 8235);

print_r($res);
