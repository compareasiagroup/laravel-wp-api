<?php namespace CompareAsiaGroup\LaravelWpApi;

use GuzzleHttp\Client;
use CompareAsiaGroup\LaravelWpApi\Models\ArticleCollection;

class WpApi
{

    protected $client;

    public function __construct($endpoint, $prefix=null, Client $client, $options = [])
    {
        $this->options = array_merge([
            'auto' => null,
            'debug' => false
        ], $options);

        $this->endpoint = $endpoint;
        $this->client   = $client;

        if ($prefix) {
            $this->prefix = $prefix;
        } else {
            $this->prefix = 'wp-json/';
        }
    }

    public function posts($page = null)
    {
        return $this->_resultsCollection($this->_get('posts', ['page' => $page]));
    }

    public function pages($page = null)
    {
        return $this->_resultsCollection($this->_get('posts', ['type' => 'page', 'page' => $page]));
    }

    public function post($slug)
    {
        return $this->_get('posts', ['filter' => ['name' => $slug]]);
    }

    public function page($slug)
    {
        return $this->_get('posts', ['type' => 'page', 'filter' => ['name' => $slug]]);
    }

    public function categories()
    {
        return $this->_resultsCollection($this->_get('taxonomies/category/terms'));
    }

    public function tags()
    {
        return $this->_resultsCollection($this->_get('taxonomies/post_tag/terms'));
    }

    public function category_posts($slug, $page = null)
    {
        return $this->_resultsCollection($this->_get('posts', ['page' => $page, 'filter' => ['category_name' => $slug]]));
    }

    public function author_posts($name, $page = null)
    {
        return $this->_resultsCollection($this->_get('posts', ['page' => $page, 'filter' => ['author_name' => $name]]));
    }

    public function tag_posts($tags, $page = null)
    {
        return $this->_resultsCollection($this->_get('posts', ['page' => $page, 'filter' => ['tag' => $tags]]));
    }

    public function search($query, $page = null)
    {
        return $this->_resultsCollection($this->_get('posts', ['page' => $page, 'filter' => ['s' => $query]]));
    }

    public function archive($year, $month, $page = null)
    {
        return $this->_resultsCollection($this->_get('posts', ['page' => $page, 'filter' => ['year' => $year, 'monthnum' => $month]]));
    }

    public function _get($method, array $query = array())
    {

        try {

            $query = ['query' => $query];

            if($this->options['auth']) {
                $query['auth'] = $this->options['auth'];
            }

            $response = $this->client->get($this->endpoint . $this->prefix . $method, $query);

            $return = [
                'results' => $response->json(),
                'total'   => $response->getHeader('X-WP-Total'),
                'pages'   => $response->getHeader('X-WP-TotalPages')
            ];

        } catch (\GuzzleHttp\Exception\TransferException $e) {

            $error['message'] = $e->getMessage();

            if ($e->getResponse()) {
                $error['code'] = $e->getResponse()->getStatusCode();
            }

            $return = [
                'error'   => $error,
                'results' => [],
                'total'   => 0,
                'pages'   => 0
            ];

        }

        return $return;

    }

    protected function _resultsCollection ($response)
    {
        if(count($response) > 0) {
            $response['results'] = new ArticleCollection($response['results']);
        }
        return $response;
    }

}
