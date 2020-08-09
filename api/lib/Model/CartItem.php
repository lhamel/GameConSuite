<?php

/**
 * CartItem
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
 * CartItem
 *
 * @package OpenAPIServer\Model
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class CartItem implements ModelInterface
{
    private const MODEL_SCHEMA = <<<'SCHEMA'
{
  "required" : [ "conventionId", "id", "memberId", "price", "quantity", "subtype", "type" ],
  "type" : "object",
  "properties" : {
    "id" : {
      "type" : "integer",
      "format" : "int64"
    },
    "conventionId" : {
      "type" : "integer",
      "format" : "int32"
    },
    "memberId" : {
      "type" : "integer",
      "format" : "int64"
    },
    "type" : {
      "type" : "string"
    },
    "subtype" : {
      "type" : "string",
      "description" : "for tickets, this is the id of the event"
    },
    "quantity" : {
      "type" : "integer",
      "format" : "int32"
    },
    "price" : {
      "type" : "number",
      "format" : "float"
    },
    "special" : {
      "type" : "string",
      "description" : "for badges, the name on the badge"
    },
    "event" : {
      "$ref" : "#/components/schemas/Event"
    }
  }
}
SCHEMA;

    /** @var int $id */
    public $id;

    /** @var int $conventionId */
    public $conventionId;

    /** @var int $memberId */
    public $memberId;

    /** @var string $type */
    public $type;

    /** @var string $subtype for tickets, this is the id of the event*/
    public $subtype;

    /** @var int $quantity */
    public $quantity;

    /** @var float $price */
    public $price;

    public $special;
    /** @var string $special for badges, the name on the badge*/

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
