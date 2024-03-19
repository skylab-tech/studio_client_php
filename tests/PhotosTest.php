<?php
use PHPUnit\Framework\TestCase;
require_once 'SkylabStudio.php';
include 'Helpers.php';


final class PhotosTest extends TestCase {
	private $api;
	private static $photoId;
	private static $jobIds = [];
	private static $profileIds = [];

	public static function tearDownAfterClass(): void {
		foreach (self::$profileIds as $profileId) {
			echo $profileId . "\n";
		}

		foreach (self::$jobIds as $jobId) {
			echo $jobId . "\n";
		}
	}

	protected function setUp(): void {
		$this->api = new SkylabStudio(getenv('SKYLAB_API_KEY'));
	}

	public function testUploadJobPhoto() {
		$profilePayload = [
			'name' => randomName('profile'),
			'enable_crop' => false
		];

		$profile = $this->api->createProfile($profilePayload);

		array_push(self::$profileIds, $profile->id);

		$jobPayload = [
			'name' => randomName('job'),
			'profile_id' => $profile->id
		];

		$job = $this->api->createJob($jobPayload);

		array_push(self::$jobIds, $job->id);

		$res = $this->api->uploadJobPhoto(__DIR__ . '/portrait-1.JPG', $job->id);

		$this->assertEquals($res['upload_response'], 200);
	}

	public function testGetPhoto() {
		$profilePayload = [
			'name' => randomName('profile'),
			'enable_crop' => false
		];

		$profile = $this->api->createProfile($profilePayload);

		array_push(self::$profileIds, $profile->id);

		$jobPayload = [
			'name' => randomName('job'),
			'profile_id' => $profile->id
		];

		$job = $this->api->createJob($jobPayload);

		array_push(self::$jobIds, $job->id);

		$photoPayload = [
			'name' => randomName('photo'),
			'job_id' => $job->id
		];

		$photo = $this->api->createPhoto($photoPayload);
		self::$photoId = $photo->id;

		$this->assertNotEmpty($photo->id);
	}

	public function testDeletePhoto() {
		$photoId = self::$photoId;

		$res = $this->api->deletePhoto($photoId);

		$this->assertEquals($res->id, $photoId);
	}
}
