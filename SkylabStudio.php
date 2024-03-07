<?php

use GuzzleHttp\Exception\ClientException;

require_once 'Job.php';
require_once 'Profile.php';
require_once 'Photo.php';
require 'vendor/autoload.php';

class SkylabSDKUtil {
	protected $SKYLAB_API_URL;
  protected $PACKAGE_VERSION;
  protected $API_CLIENT;
  protected $SkylabStudio;
  protected $DEBUG;
  private $API_KEY;
	
  public function __construct($api_key, $SkylabStudio, $debug) {
		// Load package data// Read the contents of composer.json
		$composerJsonContents = file_get_contents(__DIR__ . '/composer.json');

		// Decode the JSON content
		$composerData = json_decode($composerJsonContents, true);

    $this->SKYLAB_API_URL = getenv('SKYLAB_API_URL') ?: 'https://studio.skylabtech.ai:443';
    $this->PACKAGE_VERSION = $composerData['version'] ?? null;
    $this->API_CLIENT = 'php-' . $this->PACKAGE_VERSION;
    $this->DEBUG = $debug;
		$this->SkylabStudio = $SkylabStudio;
    $this->API_KEY = $api_key;
  }

  public function _debug($str) {
    if ($this->DEBUG) {
        echo "SKYLABTECH: $str\n";
    }
  }

  public function _buildHeaders() {
    $headers = ['Content-Type' => 'application/json'];
    $headers['X-SLT-API-KEY'] = $this->API_KEY;
    // TODO FIX API_CLIENT BEING NULL
    $headers['X-SLT-API-CLIENT'] = $this->API_CLIENT;

    // TODO DOES THIS DO ANYTHING ATM??
    $this->_debug('Set headers: ' . json_encode($headers));

    return ['headers' => $headers];
  }


  public function _buildUrl($resource = null, $identifier = null, $action = null, $params = []) {
      $url = $this->SKYLAB_API_URL . '/api/public/v1/';

      if ($resource) {
          $url .= $resource;
      }
      if ($identifier) {
          $url .= "/$identifier";
      }
      if ($action) {
          $url .= "/$action";
      }
      if($params && count($params) > 0) {
        $url .= '?' . http_build_query($params);
      }

      $this->_debug('Built url: ' . $url);

      return $url;
    }

	public function getUploadUrl($data)
		{
			$url = $this->_buildUrl('photos');

			$use_cache_upload = $data['use_cache_upload'];
			$photo_id = $data['photo_id'];
			$content_md5 = $data['content_md5'];

			$url .= "/upload_url?use_cache_upload=" . $use_cache_upload . "&photo_id=" . urlencode($photo_id) . "&content_md5=" . urlencode($content_md5);
			$options = $this->_buildHeaders();

			$client = new GuzzleHttp\Client();
			$response = [];
			try {
				$response = $client->request("GET", $url, $options);
			}
			catch(Exception $e) {
				error_log($e->getMessage());
				echo 'WTF......' . $e->getCode();
				return array('status' => $e->getCode(), 'message'=> $e->getMessage());
			}

			return $this->SkylabStudio->handleResponse($response);
		}

}

class SkylabStudio {
    protected $API_KEY;
    protected $SkylabSDKUtil;
    private $subclasses = [];

    public function __construct($apiKey, $debug = false) {
        $this->API_KEY = $apiKey;
        $this->SkylabSDKUtil = new SkylabSDKUtil($apiKey, $this, $debug);
        $this->subclasses['jobs'] = new Job($this, $this->SkylabSDKUtil);
        $this->subclasses['profiles'] = new Profile($this, $this->SkylabSDKUtil);
        $this->subclasses['photos'] = new Photo($this, $this->SkylabSDKUtil);

        $this->SkylabSDKUtil->_debug('Debug enabled');
    }

    public function __call($method, $args)
    {
      foreach ($this->subclasses as $subclass) {
          if (is_object($subclass) && method_exists($subclass, $method)) {
              return call_user_func_array([$subclass, $method], $args);
          }
      }

      throw new \BadFunctionCallException("Method $method does not exist in any subclass");
    }

		public function makeRequest($method, $url, $options) {
			$client = new GuzzleHttp\Client();
			$resp = [];
			try {
			$resp = $client->request($method, $url, $options);
			return $this->handleResponse($resp);
			}
			catch (Exception $e) {
				error_log($e->getMessage());
				echo 'WTF......' . $e->getCode();
				return array('status' => $e->getCode(), 'message'=> $e->getMessage());
			}
		}

		public function handleResponse($response) {
			$response_code = $response->getStatusCode();
			$data = $response->getBody()->getContents();

			if ($response_code >= 200 && $response_code < 300) {
				return json_decode($data);

			} else {
				$formattedResponse = [
					'message' => isset($data['message']) ? $data['message'] : 'Unknown error',
					'status' => isset($data['status']) ? $data['status'] : 'Unknown status',
				];

				return $formattedResponse;
			}
		}
  }
