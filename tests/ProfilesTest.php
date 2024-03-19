<?php
use PHPUnit\Framework\TestCase;

final class ProfilesTest extends TestCase {
	private $api;

	protected function setUp(): void {
		$this->api = new SkylabStudio(getenv('SKYLAB_API_KEY'));
	}

	protected function &getProfileId() {
		static $profileId = null;
		return $profileId;
	}

	public function testCreateProfile() {

		$data = [
			'name' => randomName('profile'),
			'enable_crop' => false
		];

		$response = $this->api->createProfile($data);

		// setup
		$profileId = &$this->getProfileId();
		$profileId = $response->id;

		$this->assertNotNull($response->id);
	}

	public function testListProfiles() {
		$resp = $this->api->listProfiles();

		$this->assertNotEmpty($resp);
	}

	public function testGetProfile() {
		$profileId = &$this->getProfileId();

		$response = $this->api->getProfile($profileId);

		$this->assertEquals($profileId, $response->id);
	}

	public function testUpdateProfile() {	
		$profileId = &$this->getProfileId();

		$data = [
			// updated name
			'name' => randomName('profile'),
			'enable_crop' => false,
			// enabling color
			'enable_color' => true
		];

		$response = $this->api->updateProfile($profileId, $data);

		$this->assertEquals($profileId, $response->id);
	}
}
