<?php

namespace CompareAsiaGroup\LaravelWpApi\Models;

use Illuminate\Support\Collection;

class ArticleCollection {

    protected $collection = [];

    public function __construct($items=[], $options=[])
    {
        $this->collection = new Collection($items);
        return $this->collection;
    }

    public function __call($method, $args)
    {
        $data = call_user_func_array(array($this->collection, $method), $args);

        if(is_a($data, 'Collection')) {
            return $data->map(function($row) {
                $data = new ArticleModel($row);
                return $data;
            });
        }

        return $data;
    }

    public function toString()
    {
        return json_encode($this->collection->toArray());
    }
}