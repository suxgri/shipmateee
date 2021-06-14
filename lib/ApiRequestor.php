<?php

namespace Shipmate;

class ApiRequestor
{
    private $_apiKey;

    private $_apiBase;

    private static $_httpClient;

    public function __construct()
    {
        $this->_apiKey = Shipmate::$apiKey;

    }



    /**
     * @param string $method
     * @param string $url
     * @param array|null $params
     * @param array|null $headers
     *
     * @return array An array whose first element is an API response and second
     *    element is the API key used to make the request.
     */
    public function request($method, $url, $params = null, $headers = null)
    {
      if (!$params) {
          $params = array();
      }
      if (!$headers) {
          $headers = array();
      }
      list($rbody, $rcode, $myApiKey) =
      $this->_requestRaw($method, $url, $params, $headers);
      
      return array($rbody, $rcode, $myApiKey);
  }

//    /**
//     * @param string $rbody A JSON string.
//     * @param int $rcode
//     * @param array $rheaders
//     * @param array $resp
//     *
//     * @throws Error\InvalidRequest if the error is caused by the user.
//     * @throws Error\Authentication if the error is caused by a lack of
//     *    permissions.
//     * @throws Error\Card if the error is the error code is 402 (payment
//     *    required)
//     * @throws Error\RateLimit if the error is caused by too many requests
//     *    hitting the API.
//     * @throws Error\Api otherwise.
//     */
//    public function handleApiError($rbody, $rcode, $rheaders, $resp)
//    {
//        if (!is_array($resp) || !isset($resp['error'])) {
//            $msg = "Invalid response object from API: $rbody "
//              . "(HTTP response code was $rcode)";
//            throw new Error\Api($msg, $rcode, $rbody, $resp, $rheaders);
//        }
//
//        $error = $resp['error'];
//        $msg = isset($error['message']) ? $error['message'] : null;
//        $param = isset($error['param']) ? $error['param'] : null;
//        $code = isset($error['code']) ? $error['code'] : null;
//
//        switch ($rcode) {
//            case 400:
//                throw new Error\xxxx($msg, $param, $rcode, $rbody, $resp, $rheaders);
//            case 401:
//                throw new Error\xxxx($msg, $rcode, $rbody, $resp, $rheaders);
//            case 403:
//                throw new Error\xxxx($msg, $param, $code, $rcode, $rbody, $resp, $rheaders);
//            case 500:
//                throw new Error\xxxx($msg, $param, $rcode, $rbody, $resp, $rheaders);
//            default:
//                throw new Error\Api($msg, $rcode, $rbody, $resp, $rheaders);
//        }
//    }

    private function _requestRaw($method, $url, $params, $headers)
    {
        $myApiKey = $this->_apiKey;

        if (!$myApiKey) {
            $msg = 'No API key provided.  (HINT: set your API key using '
              . '"Shipmate::setApiKey(<API-KEY>)".  You can generate API keys from '
              . 'the Shipmate web app interface (not true at the moment).  See https://www.shipmate.co.uk/guides/api for '
              . 'details, or email xxxxx@shipmate.com if you have any questions.';
            throw new Error\Authentication($msg);
        }


        $absUrl = $url;
        $params = json_encode($params);
        $defaultHeaders = array(
            'X-SHIPMATE-TOKEN' =>  $myApiKey,
            'Accept'=> 'application/json',
            'Content-Type'=> 'application/json',
        );

        $hasFile = false;
        $combinedHeaders = array_merge($defaultHeaders, $headers);
        $rawHeaders = array();
        foreach ($combinedHeaders as $header => $value) {
            $rawHeaders[] = $header . ': ' . $value;
        }

        list($rbody, $rcode) = $this->httpClient()->request(
            $method,
            $absUrl,
            $rawHeaders,
            $params,
            $hasFile
        );
        return array($rbody, $rcode, $myApiKey);
    }



    private function _processResourceParam($resource, $hasCurlFile)
    {
        if (get_resource_type($resource) !== 'stream') {
            throw new Error\Api(
                'Attempted to upload a resource that is not a stream'
            );
        }

        $metaData = stream_get_meta_data($resource);
        if ($metaData['wrapper_type'] !== 'plainfile') {
            throw new Error\Api(
                'Only plainfile resource streams are supported'
            );
        }

        if ($hasCurlFile) {
            // We don't have the filename or mimetype, but the API doesn't care
            return new \CURLFile($metaData['uri']);
        } else {
            return '@'.$metaData['uri'];
        }
    }

//    private function _interpretResponse($rbody, $rcode, $rheaders)
//    {
//        try {
//            $resp = json_decode($rbody, true);
//        } catch (Exception $e) {
//            $msg = "Invalid response body from API: $rbody "
//              . "(HTTP response code was $rcode)";
//            throw new Error\Api($msg, $rcode, $rbody);
//        }
//           //
//        if ($rcode < 200 || $rcode >= 300) {
//            $this->handleApiError($rbody, $rcode, $rheaders, $resp);
//        }
//        return $resp;
//    }

    public static function setHttpClient($client)
    {
        self::$_httpClient = $client;
    }

    private function httpClient()
    {
        if (!self::$_httpClient) {
            self::$_httpClient = HttpClient\CurlClient::instance();
        }
        return self::$_httpClient;
    }
}
