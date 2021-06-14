<?php

namespace Shipmate\HttpClient;//ok quindi mettila in suo folder

use Shipmate\Shipmate;
use Shipmate\Error;

class CurlClient
{
    private static $instance;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected $defaultOptions;

    /**
     * CurlClient constructor.
     *
     * Pass in a callable to $defaultOptions that returns an array of CURLOPT_* values to start
     * off a request with, or an flat array with the same format used by curl_setopt_array() to
     * provide a static set of options. Note that many options are overridden later in the request
     * call, including timeouts, which can be set via setTimeout() and setConnectTimeout().
     *
     * Note that request() will silently ignore a non-callable, non-array $defaultOptions, and will
     * throw an exception if $defaultOptions returns a non-array value.
     *
     * @param array|callable|null $defaultOptions
     */
    public function __construct($defaultOptions = null)
    {
        $this->defaultOptions = $defaultOptions;
    }

    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    // USER DEFINED TIMEOUTS

    const DEFAULT_TIMEOUT = 80;
    const DEFAULT_CONNECT_TIMEOUT = 30;

    private $timeout = self::DEFAULT_TIMEOUT;
    private $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;

    public function setTimeout($seconds)
    {
        $this->timeout = (int) max($seconds, 0);
        return $this;
    }

    public function setConnectTimeout($seconds)
    {
        $this->connectTimeout = (int) max($seconds, 0);
        return $this;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    // END OF USER DEFINED TIMEOUTS

    public function request($method, $absUrl, $headers, $params, $hasFile)
    {
        $curl = curl_init();
        $method = strtolower($method);

        $opts = array();
        $params=array();

        if ($method == 'get') {//qui chiarire
            $opts[CURLOPT_HTTPGET] = 1;
            if (count($params) > 0) {
               $encoded = self::encode($params);
               $absUrl = "$absUrl?$encoded";
               //$absUrl=utf8_encode($absUrl);
            }
        } elseif ($method == 'post') {
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
        } elseif ($method == 'delete') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }elseif ($method == 'put') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
        } else {
            throw new Error\Api("Unrecognized method $method");
        }

        //$absUrl = json_encode($absUrl);
        $opts[CURLOPT_URL] = $absUrl;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
        $opts[CURLOPT_TIMEOUT] = $this->timeout;
        //$opts[CURLOPT_HEADERFUNCTION] = $headerCallback;
        $opts[CURLOPT_HTTPHEADER] = $headers;
        // @codingStandardsIgnoreStart
        // PSR2 requires all constants be upper case. Sadly, the CURL_SSLVERSION
        // constants to not abide by those rules.
        //
        // Opt into TLS 1.x support on older versions of curl. This causes some
        // curl versions, notably on RedHat, to upgrade the connection to TLS
        // 1.2, from the default TLS 1.0.
        if (!defined('CURL_SSLVERSION_TLSv1')) {
            define('CURL_SSLVERSION_TLSv1', 1); // constant not defined in PHP < 5.5
        }
        $opts[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1;
        // @codingStandardsIgnoreEnd

        curl_setopt_array($curl, $opts);
        $rbody = curl_exec($curl);

        if ($rbody === false) {
            $errno = curl_errno($curl);

            $message = curl_error($curl);
            curl_close($curl);
            $this->handleCurlError($absUrl, $errno, $message);
        }

        $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);//questo dovrebbe essere 20X..
        curl_close($curl);
        return array($rbody, $rcode);//($rbody, $rcode, $rheaders);//rheaders non li ho piu perche tolta la callback che li catturava
    }

    /**
     * @param number $errno
     * @param string $message
     * @throws Error\ApiConnection
     */
    private function handleCurlError($url, $errno, $message)
    {
        switch ($errno) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $msg = "Could not connect to Shipmate ($url).  Please check your "
                 . "internet connection and try again.  If this problem persists, "
                 . "you should get in contact with Shipmate support service  ";

                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify Shipmate's SSL certificate.  Please make sure "
                 . "that your network is not intercepting certificates.  "
                 . "(Try going to $url in your browser.)  "
                 . "If this problem persists,";
                break;
            default:
                $msg = "Unexpected error communicating with Shipmate.  "
                 . "If this problem persists,";
        }
        $msg .= " let us know at xxxxxxxxxxx@shipmate.com.";

        $msg .= "\n\n(Network error [errno $errno]: $message)";
        throw new Error\ApiConnection($msg);
    }


    /**
     * @param array $arr An map of param keys to values.
     * @param string|null $prefix
     *
     * Only public for testability, should not be called outside of CurlClient
     *
     * @return string A querystring, essentially.
     */
    public static function encode($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
         //
        $r = array();
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }
             //
            if (is_array($v)) {
                $enc = self::encode($v, $k);
                if ($enc) {
                    $r[] = $enc;
                }
            } else {
                $r[] = urlencode($k)."=".urlencode($v);
            }
        }
        //
        return implode("&", $r);
    }
}
