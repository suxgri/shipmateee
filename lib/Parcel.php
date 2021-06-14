<?php

namespace Shipmate;

class Parcel extends ApiResource
{
    /**
     * @param array|null $params
     *
     * @return ParcelAttributes.
     */
    public static function allAttributes()
    {
        $url = Shipmate::$apiBase . '/attributes';
        list($response, $rcode, $apiKey) = self::_staticRequest('get', $url);
        return array($response, $rcode, $apiKey);
    }


    /**
     * @param array|null $params
     *
     * @return Parcel packaging options.
     */
    public static function allPackagingOptions()
    {
        $url = Shipmate::$apiBase . '/packaging_options';
        list($response, $rcode, $apiKey) = self::_staticRequest('get', $url);
        return array($response, $rcode, $apiKey);
    }


    /**
     * @param array|null $params
     *
     * @return Parcel events list.
     */
    public function events($reference)
    {
        $urlbase = Shipmate::$apiBase;
        //should check reference not null and return error if null, or create reference validation
        $url2 = '/parcels/'.$reference.'/events';
        $url = $urlbase.$url2;
        list($response, $rcode, $apiKey) = self::_staticRequest('get', $url);
        return array($response, $rcode, $apiKey);
    }


    /**
     * @param array|null $params
     *
     * @return Label.
     */
    public function label($reference)
    {
        $urlbase = Shipmate::$apiBase;
        //should check reference not null and return error if null, or create reference validation
        $url2 = '/parcels/'.$reference.'/label';
        $url = $urlbase.$url2;
        list($response, $rcode, $apiKey) = self::_staticRequest('get', $url);
        return array($response, $rcode, $apiKey);
    }


    /**
     * @param array|null $params
     *
     * @return Parcel packaging options.
     */
    public function print($reference)
    {
        $urlbase = Shipmate::$apiBase;
        //should check reference not null and return error if null, or create reference validation
        $url2 = '/parcels/'.$reference.'/print';
        $url = $urlbase.$url2;
        list($response, $rcode, $apiKey) = self::_staticRequest('put', $url);
        return array($response, $rcode, $apiKey);
    }
}
