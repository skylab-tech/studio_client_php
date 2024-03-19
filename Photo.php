<?php
error_reporting(E_ALL & ~E_DEPRECATED);

use Jcupitt\Vips;
include 'ImageUtil.php';
class Photo
{
	private $SkylabStudio;
	private $SkylabSDKUtil;

	const validExtensions = ["jpg", "jpeg", "png", "webp"];

	public function __construct($SkylabStudio, $SkylabSDKUtil)
		{
			$this->SkylabStudio = $SkylabStudio;
			$this->SkylabSDKUtil = $SkylabSDKUtil;
		}

	public function getPhoto($id)
		{
			$url = $this->SkylabSDKUtil->_buildUrl("photos", $id);
			$options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("GET", $url, $options);
		}

	public function createPhoto($data)
		{
			$url = $this->SkylabSDKUtil->_buildUrl("photos");
      $options = $this->SkylabSDKUtil->_buildHeaders();

			$jsonData = json_encode($data);
      $options['body'] = $jsonData;

			return $this->SkylabStudio->makeRequest("POST", $url, $options);
		}

	private function _uploadPhoto($photoPath, $id, $model = 'job')
		{
			$client = new GuzzleHttp\Client();
			echo 'uploading photo: ~~~~~ ' . $photoPath . ', ' . $id;


			$ext = pathinfo($photoPath, PATHINFO_EXTENSION);

			if (!in_array(strtolower($ext), self::validExtensions)) {
					throw new Exception("Invalid file type: must be of type jpg/jpeg/png/webp");
			}

			// TODO
			// $valid = $this->validateFile($photoPath);
			$valid = true;
			if (!$valid) {
					throw new Exception("Invalid file size: must be within 6400x6400, and no larger than 27MB");
			}

			$response = [];

			$photoName = basename($photoPath);

			$uploadOptions = [];
			$file = Vips\Image::newFromFile($photoPath);

			if ($ext === "png") {
					$file = attemptImageConversion($file, "webp");
			}

			// Returns the digest in raw binary format with a length of 16
			$md5 = base64_encode(md5_file($photoPath, true));

			$photoData = [
					"{$model}_id" => $id,
					"name" => $photoName,
					"use_cache_upload" => false
			];

			if ($model == "job") {
					$job = $this->SkylabStudio->getJob($id);
					if ($job->type == "regular") {
							$uploadOptions['headers']["X-Amz-Tagging"] = "job=photo&api=true";
					}
			}

			// Create Studio photo record
			$photoResp = $this->createPhoto($photoData);

			// Response status was not within acceptable range - see formattedResponse in handleResponse
			if (is_array($photoResp) && $photoResp["status"]) {
					throw new Exception("Unable to create the photo object. if creating profile photo, ensure enable_extract and replace_background is set to: True. Ensure the photo name is unique.");
			}

			$response["photo"] = $photoResp;
			$photoId = $photoResp->id;

			$uploadUrlPayload = [
					"use_cache_upload" => 'false',
					"photo_id" => $photoId,
					"content_md5" => $md5
			];

			$uploadUrlResp = $this->SkylabSDKUtil->getUploadUrl($uploadUrlPayload);

			if(is_array($uploadUrlResp) && $uploadUrlResp["status"]) {
				// $this->deletePhoto($photoId);
				throw new Exception("Unable to obtain upload url.");
			}

			$uploadUrl = $uploadUrlResp->url;
			$uploadOptions['headers']["Content-MD5"] = $md5;
			$uploadOptions['body'] = file_get_contents($photoPath, true);

			$uploadPhotoResp = [];
			try {
					$uploadPhotoResp = $client->request('PUT', $uploadUrl, $uploadOptions);
					echo "attempting to upload photo...";

					if (!$uploadPhotoResp) {
							echo "First upload attempt failed, retrying...";

							$retry = 0;

							while ($retry < 3) {
									$uploadPhotoResp = $client->request('PUT', $uploadUrl, $uploadOptions);
									if ($uploadPhotoResp) {
											break; // Upload was successful, exit the loop
									} else if ($retry === 2) {
											throw new Exception("Unable to upload to the bucket after retrying.");
									} else {
											sleep(1); // Wait for a moment before retrying (1 second)
											$retry += 1;
									}
							}
						}

				$response["upload_response"] = $uploadPhotoResp->getStatusCode();

			} catch (Exception $error) {
					$code = $error->getCode();
					$reason = $error->getMessage();
					echo "An exception of type {$code} occurred: {$reason}";

					$this->deletePhoto($photoId);

					$response["photo"] = null;
					$response["status"] = $code;
					$response["reason"] = $reason;
			}


			return $response;
		}

	public function uploadJobPhoto($photoPath, $id)
		{
			return $this->_uploadPhoto($photoPath, $id);
		}

	public function deletePhoto($id)
		{
			$url = $this->SkylabSDKUtil->_buildUrl("photos", $id);
      $options = $this->SkylabSDKUtil->_buildHeaders();

			return $this->SkylabStudio->makeRequest("DELETE", $url, $options);
		}

}
