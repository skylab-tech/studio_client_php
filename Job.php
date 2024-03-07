<?php
class Job
{
	private $SkylabStudio;
	private $SkylabSDKUtil;

	public function __construct($SkylabStudio, $SkylabSDKUtil)
    {
        $this->SkylabStudio = $SkylabStudio;
        $this->SkylabSDKUtil = $SkylabSDKUtil;
    }

  public function getJob($id)
    {
      $url = $this->SkylabSDKUtil->_buildUrl("jobs", $id);
      $options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("GET", $url, $options);
    }

	public function getJobByName($name)
    {
			$baseUrl = $this->SkylabSDKUtil->_buildUrl("jobs");
      $url = "{$baseUrl}/find_by_name?name={$name}";
      $options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("GET", $url, $options);
    }

	public function getJobsInFront($id)
    {
			$url = $this->SkylabSDKUtil->_buildUrl("jobs", $id, "jobs_in_front");
      $options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("GET", $url, $options);
    }

	public function listJobs()
    {
			$url = $this->SkylabSDKUtil->_buildUrl("jobs");
      $options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("GET", $url, $options);
    }

	public function createJob($data)
    {
			$url = $this->SkylabSDKUtil->_buildUrl("jobs");
      $options = $this->SkylabSDKUtil->_buildHeaders();

			$jsonData = json_encode($data);
      $options['body'] = $jsonData;

			return $this->SkylabStudio->makeRequest("POST", $url, $options);
    }

	public function updateJob($id, $data)
    {
      $url = $this->SkylabSDKUtil->_buildUrl("jobs", $id);
      $options = $this->SkylabSDKUtil->_buildHeaders();

			$jsonData = json_encode($data);
      $options['body'] = $jsonData;

			return $this->SkylabStudio->makeRequest("PATCH", $url, $options);
    }

	public function deleteJob($id)
    {
			$url = $this->SkylabSDKUtil->_buildUrl("jobs", $id);
      $options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("DELETE", $url, $options);
    }

	public function queueJob($id, $data)
		{
			$url = $this->SkylabStudio->_buildUrl("jobs", $id, "queue");
			$options = $this->SkylabStudio->_buildHeaders();

			$jsonData = json_encode($data);
			$options['body'] = $jsonData;

			return $this->SkylabStudio->makeRequest("POST", $url, $options);
		}

	public function cancelJob($id)
    {
			$url = $this->SkylabStudio->_buildUrl("jobs", $id, "cancel");
			$options = $this->SkylabStudio->_buildHeaders();

			return $this->SkylabStudio->makeRequest("POST", $url, $options);
    }
}
