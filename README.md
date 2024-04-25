# Skylab Studio PHP Client

[studio.skylabtech.ai](https://studio.skylabtech.ai)

## Installation

```
composer require skylab/studio
```

## Example usage

```php
require_once 'SkylabStudio.php';

$api = new SkylabStudio('your-api-key');

// CREATE PROFILE
$profilePayload = [
  'name' => 'profile name',
  'enable_crop' => false,
  'enable_retouch' => true
];

$profile = $api->createProfile($profilePayload);

// CREATE JOB
$jobPayload = [
  'name' => 'job name',
  'profile_id' => $profile['id']
];

$job = $api->createJob($jobPayload);

// UPLOAD JOB PHOTO(S)
$filePath = '/path/to/photo';
$api->uploadJobPhoto($filePath, $job['id']);

// QUEUE JOB
$payload = [ 'callback_url' => 'YOUR_CALLBACK_ENDPOINT' ];
$api->queueJob($job['id'], $payload);

// NOTE: Once the job is queued, it will transition to processed and then completed
// We will send a response to the specified callback_url with the output photo download urls
```

## Jobs

### List all Jobs

List the last 30 jobs.

```php
$api->listJobs();
```

### Create a Job

```php

$payload = [
  'name' => 'your unique job name',
  'profile_id' => 123
]

$api->createJob($payload);
```

For all payload options, consult the [API documentation](https://studio-docs.skylabtech.ai/#tag/job/operation/createJob).

### Get a Job

```php
$api->getJob($jobId);
```

### Get Job by Name

```php
$api->getJobByName($name);
```

### Update a Job

```php
$payload = [
  'name' => 'your updated job name',
  'profile_id' => 123
]

$api->updateJob($jobId, $payload);
```

For all payload options, consult the [API documentation](https://studio-docs.skylabtech.ai/#tag/job/operation/updateJobById).

### Queue Job

```php
$payload = [ 'callback_url' => 'YOUR_CALLBACK_ENDPOINT' ]

$api->queueJob($jobId, $payload);
```

### Jobs in Front

```php
$api->getJobsInFront($jobId);
```

### Delete a Job

```php
$api->deleteJob($jobId);
```

### Cancel a Job

```php
$api->cancelJob($jobId);
```

## Profiles

### List all Profiles

```php
$api->listProfiles();
```

### Create a Profile

```php
$api->createProfile([
  'name' => 'My Profile'
]);
```

For all payload options, consult the [API documentation](https://studio-docs.skylabtech.ai/#tag/profile/operation/createProfile).

### Get a Profile

```php
$api->getProfile($profileId);
```

### Update profile

```php
$payload = [
  'name' => 'My updated profile name',
];

$api->updateProfile($profileId, $payload);
```

For all payload options, consult the [API documentation](https://studio-docs.skylabtech.ai/#tag/profile/operation/updateProfileById).

## Photos

#### Upload Job Photo

This function handles validating a photo, creating a photo object and uploading it to your job/profile's s3 bucket. If the bucket upload process fails, it retries 3 times and if failures persist, the photo object is deleted.

```php
$api->uploadJobPhoto($photoPath, $jobId);
```

`Returns: { photo: { photoObject }, uploadResponse: bucketUploadResponseStatus }`

If upload fails, the photo object is deleted for you. If upload succeeds and you later decide you no longer want to include that image, use delete_photo to remove it.

### Get a Photo

```php
$api->getPhoto($photoId);
```

### Delete a Photo

```php
$api->deletePhoto($photoId);
```

## Troubleshooting

### General Troubleshooting

- Enable debug mode
- Capture the response data and check your logs &mdash; often this will have the exact error

### Enable Debug Mode

Debug mode prints out the underlying request information as well as the data payload that gets sent to Skylab.
You will most likely find this information in your logs. To enable it, simply put `true` as a parameter
when instantiating the API object.

```php
$api = new SkylabStudio("your-api-key", true);
```

### Response Ranges

SkylabTech's API typically sends responses back in these ranges:

- 2xx – Successful Request
- 4xx – Failed Request (Client error)
- 5xx – Failed Request (Server error)

If you're receiving an error in the 400 response range follow these steps:

- Double check the data and ID's getting passed to Skylab
- Ensure your API key is correct
- Log and check the body of the response
