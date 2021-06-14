<?php

namespace Shipmate;

class Service extends ApiResource
{
  /**
   * @param array|null $params
   *
   * @return Service all.
   */
  public static function all()
  {
      $url = Shipmate::$apiBase . '/services';
      list($response, $rcode, $apiKey) = self::_staticRequest('get', $url);
      return array($response, $rcode, $apiKey);
  }


  /**
   * @param array|null $params
   *
   * @return Service packaging options.
   */
  public static function retrieve($params)
  {
    //should check params mandatory in array form, or make params validation
      $url = Shipmate::$apiBase . '/services';
      list($response, $rcode, $apiKey) = self::_staticRequest('get', $url , $params);
      return array($response, $rcode, $apiKey);
  }

}
