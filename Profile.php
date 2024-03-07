<?php
class Profile 
{
	private $SkylabStudio;
	private $SkylabSDKUtil;

	public function __construct($SkylabStudio, $SkylabSDKUtil)
		{
			$this->SkylabStudio = $SkylabStudio;
			$this->SkylabSDKUtil = $SkylabSDKUtil;
		}

	public function getProfile($id)
		{
			$url = $this->SkylabSDKUtil->_buildUrl("profiles", $id);
      $options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("GET", $url, $options);
		}

	public function listProfiles()
		{
			$url = $this->SkylabSDKUtil->_buildUrl("profiles");
      $options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("GET", $url, $options);
		}

	public function createProfile($data)
		{
			$url = $this->SkylabSDKUtil->_buildUrl("profiles");
      $options = $this->SkylabSDKUtil->_buildHeaders();

			$jsonData = json_encode($data);
      $options['body'] = $jsonData;

			return $this->SkylabStudio->makeRequest("POST", $url, $options);
		}

	public function updateProfile($id, $data)
		{
			$url = $this->SkylabSDKUtil->_buildUrl("profiles", $id);
			$options = $this->SkylabSDKUtil->_buildHeaders();

			$jsonData = json_encode($data);
			$options['body'] = $jsonData;

			return $this->SkylabStudio->makeRequest("PATCH", $url, $options);
		}
}
