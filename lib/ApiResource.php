<?php
//not a real change
namespace Shipmate;

abstract class ApiResource
{

    public static function baseUrl()
    {
        return Shipmate::$apiBase;
    }


//not used at the moment but could be useful
    private static function _validateParams($params = null)
    {
        if ($params && !is_array($params)) {
            $message = "You must pass an array as the first argument to Shipmate API ";
            throw new Error\Api($message);
        }
    }


    protected static function _staticRequest($method, $url, $params = null, $options = null)
    {
      $requestor = new ApiRequestor();
      list($rbody, $rcode, $myApiKey) = $requestor->request($method, $url, $params);
      return array($rbody, $rcode, $myApiKey);
    }
}
