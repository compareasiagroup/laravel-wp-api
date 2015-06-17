<?php namespace CompareAsiaGroup\LaravelWpApi;

use CompareAsiaGroup\LaravelWpApi\Models\ArticleCollection;

class WpApi
{

    protected $client;

    public function __construct($endpoint, $prefix=null, $client, $options = [])
    {
        $this->options = array_merge([
            'auto' => null,
            'posts_per_page' => 10,
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

    public function posts($page = null, $options = [])
    {
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            [
                'page' => $page,
                'filter' => $opts
            ]
        ));
    }

    public function pages($page = null, $options = [])
    {
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            [
                'type' => 'page',
                'page' => $page,
                'filter' => $opts
            ]
        ));
    }

    public function post($slug)
    {
        return $this->_get('posts', ['filter' => ['name' => $slug]]);
    }

    public function page($slug)
    {
        return $this->_get('posts', ['type' => 'page', 'filter' => ['name' => $slug]]);
    }

    public function categories($options=[])
    {
        $segment = (isset($options['segment'])) ? $options['segment'] : 'category';
        return $this->_resultsCollection($this->_get('taxonomies/' . $segment . '/terms'));
    }

    public function tags($options=[])
    {
        $segment = (isset($options['segment'])) ? $options['segment'] : 'post_tag';
        return $this->_resultsCollection($this->_get('taxonomies/' . $segment . '/terms'));
    }

    public function category_posts($slug, $page = null, $options = [])
    {
        $options['category_name'] = $slug;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            [
                'page' => $page,
                'filter' => $opts
            ]
        ));
    }

    public function author_posts($name, $page = null, $options = [])
    {
        $options['author_name'] = $name;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            [
                'page' => $page,
                'filter' => $opts
            ]
        ));
    }

    public function tag_posts($tags, $page = null, $options = [])
    {
        $options['tag'] = $tags;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            [
                'page' => $page,
                'filter' => $opts
            ]
        ));
    }

    public function search($query, $page = null, $options = [])
    {
        $options['s'] = $query;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            [
                'page' => $page,
                'filter' => $opts
            ]
        ));
    }

    public function archive($year, $month, $page = null, $options = [])
    {
        $options['year'] = $year;
        $options['monthnum'] = $month;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            [
                'page' => $page,
                'filter' => $opts
            ]
        ));
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

    /**
     * @param $options
     */
    protected function extendDefaults($options = [])
    {
        return array_merge([
            'posts_per_page' => $this->options['posts_per_page']
        ], $options);
    }

}
