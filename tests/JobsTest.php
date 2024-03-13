<?php
use PHPUnit\Framework\TestCase;
require_once 'SkylabStudio.php';
include 'Helpers.php';

final class JobsTest extends TestCase {
	private $api;
	protected function setUp(): void {
		$this->api = new SkylabStudio("16V7LPczUNXb6cdY7V15G5s5");
	}

	protected function &getJobId() {
		static $jobId = null;
		return $jobId;
	}

	protected function &getJobName() {
		static $jobName = null;
		return $jobName;
	}

	public function testCreateJob() {

		$data = [
			'name' => randomName('job'),
			// PSPA global profile
			'profile_id' => 24
		];

		$response = $this->api->createJob($data);

		// setup
		$jobId = &$this->getJobId();
		$jobId = $response->id;

		$this->assertNotNull($response->id);
	}

	public function testGetJob() {
		$jobId = &$this->getJobId();
		$response = $this->api->getJob($jobId);
		$responseJobId = $response->id;

		$this->assertEquals($jobId, $responseJobId);
	}

	public function testUpdateJob() {
		$updatedName = randomName('job');
		$jobId = &$this->getJobId();

		$data = [
			'name' => $updatedName
		];

		$response = $this->api->updateJob($jobId, $data);

		// setup
		$jobName = &$this->getJobName();
		$jobName = $response->name;

		$this->assertEquals($response->name, $updatedName);
	}

	public function testGetJobByName() {
		$jobName = &$this->getJobName();
		
		$response = $this->api->getJobByName($jobName);
		
		$this->assertEquals($response->name, $jobName);
	}

	public function testListJobs() {
		$response = $this->api->listJobs();

		$this->assertNotEmpty($response);
	}
	
	public function testDeleteJob() {
		$jobId = &$this->getJobId();

		$response = $this->api->deleteJob($jobId);

		$this->assertEquals($response->id, $jobId);
	}
}
