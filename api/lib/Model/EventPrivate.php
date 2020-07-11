<?php

/**
 * EventPrivate
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
 * EventPrivate
 *
 * @package OpenAPIServer\Model
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class EventPrivate extends Event implements ModelInterface
{
    private const MODEL_SCHEMA = <<<'SCHEMA'
{
  "allOf" : [ {
    "$ref" : "#/components/schemas/Event"
  }, {
    "$ref" : "#/components/schemas/EventPrivate_allOf"
  } ]
}
SCHEMA;

    /** @var string $vttLink A link to VTT information, available only to GMs and ticketed players*/
    public $vttLink;

    /** @var string $vttInfo Additional VTT information, available only to GMs and ticketed players*/
    public $vttInfo;

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
