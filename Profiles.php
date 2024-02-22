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

#!/bin/sh

// COMMIT_FILE=$1
// COMMIT_MSG=$(cat $1)
// CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
// JIRA_ID=$(echo "$CURRENT_BRANCH" | grep -Eo "[A-Z0-9]{1,10}-?[A-Z0-9]+-\d+")

// if [ ! -z "$JIRA_ID" ]; then
//     echo "$JIRA_ID $COMMIT_MSG" > $COMMIT_FILE
//     echo "JIRA ID '$JIRA_ID', matched in current branch name, prepended to commit message. (Use --no-verify to skip)"
// fi
