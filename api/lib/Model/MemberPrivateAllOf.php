<?php

/**
 * MemberPrivateAllOf
 *
 * PHP version 7.1
 *
 * @package OpenAPIServer\Model
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */

/**
 * NOTE: This class is auto generated by the openapi generator program.
 * https://github.com/openapitools/openapi-generator
 */
namespace OpenAPIServer\Model;

use OpenAPIServer\Interfaces\ModelInterface;

/**
 * MemberPrivateAllOf
 *
 * @package OpenAPIServer\Model
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class MemberPrivateAllOf implements ModelInterface
{
    private const MODEL_SCHEMA = <<<'SCHEMA'
{
  "properties" : {
    "addr1" : {
      "type" : "string"
    },
    "addr2" : {
      "type" : "string"
    },
    "addr3" : {
      "type" : "string"
    },
    "city" : {
      "type" : "string"
    },
    "state" : {
      "type" : "string"
    },
    "zip" : {
      "type" : "string"
    },
    "international" : {
      "type" : "string"
    },
    "email" : {
      "type" : "string"
    },
    "phone" : {
      "type" : "string"
    }
  }
}
SCHEMA;

    /** @var string $addr1 */
    private $addr1;

    /** @var string $addr2 */
    private $addr2;

    /** @var string $addr3 */
    private $addr3;

    /** @var string $city */
    private $city;

    /** @var string $state */
    private $state;

    /** @var string $zip */
    private $zip;

    /** @var string $international */
    private $international;

    /** @var string $email */
    private $email;

    /** @var string $phone */
    private $phone;

    /**
     * Returns model schema.
     *
     * @param bool $assoc When TRUE, returned objects will be converted into associative arrays. Default FALSE.
     *
     * @return array
     */
    public static function getOpenApiSchema($assoc = false)
    {
        return json_decode(static::MODEL_SCHEMA, $assoc);
    }
}
