<?php

namespace CompareAsiaGroup\LaravelWpApi\Models;

use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

abstract class BaseArticleModel implements Arrayable, Jsonable, JsonSerializable
{

    protected $data = [];

    protected $properties = [
        'id',
        'date',
        'guid',
        'modified',
        'modified_gmt',
        'slug',
        'type',
        'link',
        'title',
        'content',
        'excerpt',
        'featured_image_full_url',
        'featured_image_thumbnail_url',
        'terms',
        'sticky'
    ];

    /**
     * @param null $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
        return $this->format();
    }

    public function format ($data=null)
    {
        $data = $data || $this->data;
        return $data;
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}