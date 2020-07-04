<?php

/**
 * EventPrivateAllOf
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
 * EventPrivateAllOf
 *
 * @package OpenAPIServer\Model
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class EventPrivateAllOf implements ModelInterface
{
    private const MODEL_SCHEMA = <<<'SCHEMA'
{
  "properties" : {
    "vttLink" : {
      "type" : "string",
      "description" : "A link to VTT information, available only to GMs and ticketed players"
    },
    "vttInfo" : {
      "type" : "string",
      "description" : "Additional VTT information, available only to GMs and ticketed players"
    }
  }
}
SCHEMA;

    /** @var string $vttLink A link to VTT information, available only to GMs and ticketed players*/
    private $vttLink;

    /** @var string $vttInfo Additional VTT information, available only to GMs and ticketed players*/
    private $vttInfo;

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
