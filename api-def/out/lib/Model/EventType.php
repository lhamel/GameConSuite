<?php

/**
 * EventType
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
 * EventType
 *
 * @package OpenAPIServer\Model
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
class EventType implements ModelInterface
{
    private const MODEL_SCHEMA = <<<'SCHEMA'
{
  "type" : "object",
  "properties" : {
    "id" : {
      "type" : "integer",
      "format" : "int64"
    },
    "abbr" : {
      "type" : "string"
    },
    "label" : {
      "type" : "string"
    },
    "order" : {
      "type" : "integer",
      "format" : "int64"
    }
  },
  "xml" : {
    "name" : "EventType"
  }
}
SCHEMA;

    /** @var int $id */
    public $id;

    /** @var string $abbr */
    public $abbr;

    /** @var string $label */
    public $label;

    /** @var int $order */
    public $order;

    public function __construct(int $id, string $abbr, string $label, int $order)
    {
      $this->id = $id;
      $this->abbr = $abbr;
      $this->label = $label;
      $this->order = $order;
    }

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
