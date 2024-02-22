<?php

require_once 'Job.php';
require_once 'Profile.php';
require 'vendor/autoload.php';

class SkylabSDKUtil {
	protected $SKYLAB_API_URL;
  protected $PACKAGE_VERSION;
  protected $API_CLIENT;
  protected $DEBUG;
  private $API_KEY;
	
  public function __construct($api_key, $debug) {
		// Load package data// Read the contents of composer.json
		$composerJsonContents = file_get_contents(__DIR__ . '/composer.json');

		// Decode the JSON content
		$composerData = json_decode($composerJsonContents, true);

    $this->SKYLAB_API_URL = getenv('SKYLAB_API_URL') ?: 'https://studio.skylabtech.ai:443';
    $this->PACKAGE_VERSION = $composerData['version'] ?? null;
    $this->API_CLIENT = 'php-' . $this->PACKAGE_VERSION;
    $this->DEBUG = $debug;
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

}

class SkylabStudio {
    protected $API_KEY;
    protected $SkylabSDKUtil;
    private $subclasses = [];

    public function __construct($apiKey, $debug = false) {
        $this->API_KEY = $apiKey;
        $this->SkylabSDKUtil = new SkylabSDKUtil($apiKey, $debug);
        $this->subclasses['jobs'] = new Job($this, $this->SkylabSDKUtil);
        $this->subclasses['profiles'] = new Profile($this, $this->SkylabSDKUtil);

        $this->SkylabSDKUtil->_debug('Debug enabled');
    }

    public function __call($method, $args)
    {
      foreach ($this->subclasses as $subclass) {
          if (is_object($subclass) && method_exists($subclass, $method)) {
              return call_user_func_array([$subclass, $method], $args);
          }
      }

      throw new \BadMethodCallException("Method $method does not exist in any subclass");
    }

		public function makeRequest($method, $url, $options) {
			$client = new GuzzleHttp\Client();
			try {
				$resp = $client->request($method, $url, $options);
				return $this->handleResponse($resp);
			}
			catch (Exception $e) {
				return error_log($e->getMessage());
			}
		}

		public function handleResponse($response) {
			$response_code = $response->getStatusCode();
			$data = $response->getBody()->getContents();
			echo "rsponse code" . $response_code;
			if ($response_code >= 200 && $response_code < 300) {
				return $data;

			} else {
				print_r("call failed INSWIDE HANDLE RESP ELSE", $data);
				$formattedResponse = [
					'message' => isset($data['message']) ? $data['message'] : 'Unknown error',
					'status' => isset($data['status']) ? $data['status'] : 'Unknown status',
				];

				return $formattedResponse;
					// Handle JSON decoding error
					// return ['message' => 'Error fetching response from server.', 'status' => $response_code];
			}
		}
  }

// Include the required files
// require_once 'path/to/profiles'; // Make sure to replace 'path/to/profiles' with the actual path
// require_once 'path/to/photos'; // Make sure to replace 'path/to/photos' with the actual path
// require_once 'path/to/util'; // Make sure to replace 'path/to/util' with the actual path

// Create a function that returns a new SkylabStudio instance
function client($apiKey, $debug) {
    return new SkylabStudio($apiKey, $debug);
}
