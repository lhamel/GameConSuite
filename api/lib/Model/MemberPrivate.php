<?php

/**
 * MemberPrivate
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
 * MemberPrivate
 *
 * @package OpenAPIServer\Model
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class MemberPrivate extends Member implements ModelInterface
{
    private const MODEL_SCHEMA = <<<'SCHEMA'
{
  "allOf" : [ {
    "$ref" : "#/components/schemas/Member"
  }, {
    "$ref" : "#/components/schemas/MemberPrivate_allOf"
  } ]
}
SCHEMA;

    /** @var string $addr1 */
    public $addr1;

    /** @var string $addr2 */
    public $addr2;

    /** @var string $addr3 */
    private $addr3;

    /** @var string $city */
    public $city;

    /** @var string $state */
    public $state;

    /** @var string $zip */
    public $zip;

    /** @var string $international */
    public $international;

    /** @var string $email */
    public $email;

    /** @var string $phone */
    public $phone;

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
