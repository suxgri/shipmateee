<?php

namespace Shipmate;
//sotto occhio non vero che return sempre consignment
class Consignment extends ApiResource
{
  /**
   * @param array|null $params
   *
   * @return Consignment object created.
   */
  public static function create($params)
  {
      $url = Shipmate::$apiBase . '/consignments';
      list($response, $rcode, $apiKey) = self::_staticRequest('post', $url , $params);
      return array($response, $rcode, $apiKey);
  }


  /**
   * @param array|null $params
   *
   * @return Consignment delete.
   */
  public static function delete($reference)
  {
      //should check reference not null and return error if null, or create reference validation
      $url = Shipmate::$apiBase . '/consignments/' . $reference;
      list($response, $rcode, $apiKey) = self::_staticRequest('delete', $url);
      return array($response, $rcode, $apiKey);
  }


  /**
   * @param array|null $params
   *
   * @return Consignment events
   */
  public static function events($reference)
  {
      //should check reference not null and return error if null, or create reference validation
      $url = Shipmate::$apiBase . '/consignments/' . $reference .'/events';
      list($response, $rcode, $apiKey) = self::_staticRequest('get', $url);
      return array($response, $rcode, $apiKey);
  }
}
