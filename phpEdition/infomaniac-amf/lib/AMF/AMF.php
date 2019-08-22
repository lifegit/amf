<?php
namespace Infomaniac\AMF;

use Infomaniac\AMF\Deserializer;
use Infomaniac\Exception\DeserializationException;
use Infomaniac\Exception\SerializationException;
use ErrorException;
use Exception;
use Infomaniac\AMF\Serializer;
use Infomaniac\IO\Input;
use Infomaniac\IO\Output;

/**
 * @author Danny Kopping <dannykopping@gmail.com>
 */
class AMF
{
    private static $classmappingCallback;

    private static function init($options = AMF_DEFAULT_OPTIONS)
    {
        if ($options & AMF_PROMOTE_ERRORS) {
            set_error_handler('\\Infomaniac\\AMF\\AMF::errorHandler');
        }
    }

    public static function serialize($data, $options = AMF_DEFAULT_OPTIONS, $type = null)
    {
        try {
            self::init($options);

            $stream     = new Output();

            $serializer = new Serializer($stream, $options);
            $serializer->setClassmappingCallback(self::$classmappingCallback);

            return $serializer->serialize($data, true, $type);
        } catch (Exception $e) {
            $ex = new SerializationException($e->getMessage(), $e->getCode(), $e);
            $ex->setData($data);
            throw $ex;
        }
    }

    public static function deserialize($data, $forceType = null)
    {
        try {
            self::init();

            $stream       = new Input($data);
            $deserializer = new Deserializer($stream);

            return $deserializer->deserialize($forceType);
        } catch (Exception $e) {
            $ex = new DeserializationException($e->getMessage(), $e->getCode(), $e);
            $ex->setData($data);
            throw $ex;
        }
    }

    /**
     * @link  http://www.php.net/manual/en/class.errorexception.php#95415
     *
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     *
     * @return bool
     * @throws \ErrorException
     */
    public static function errorHandler($code, $message, $file, $line)
    {
        // Determine if this error is one of the enabled ones in php config (php.ini, .htaccess, etc)
        $enabled = (bool) ($code & ini_get('error_reporting'));

        // -- FATAL ERROR
        // throw an Error Exception, to be handled by whatever Exception handling logic is available in this context
        if (in_array($code, array(E_USER_ERROR, E_RECOVERABLE_ERROR, E_WARNING)) && $enabled) {
            throw new ErrorException($message, 0, $code, $file, $line);
        }

        // -- NON-FATAL ERROR/WARNING/NOTICE
        // Log the error if it's enabled, otherwise just ignore it
        else {
            if ($enabled) {
                error_log($message, 0);
                return false; // Make sure this ends up in $php_errormsg, if appropriate
            }
        }
    }

    /**
     * Override the library's default mode of determining an object's fully-qualified classname
     *
     * @param $callback
     */
    public static function setClassmappingCallback($callback)
    {
        self::$classmappingCallback = $callback;
    }
}
