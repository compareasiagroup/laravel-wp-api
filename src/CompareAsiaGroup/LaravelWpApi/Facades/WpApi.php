<?php namespace CompareAsiaGroup\LaravelWpApi\Facades;

use Illuminate\Support\Facades\Config;
use CompareAsiaGroup\LaravelWpApi\Models\ArticleCollection;
use CompareAsiaGroup\GuzzleCache\Facades\GuzzleCache;

class WpApi
{

    protected $client;

    public function __construct($endpoint, $options = [])
    {
        $this->options = array_merge([
            'auth' => Config::get('wp-api.auth', false),
            'posts_per_page' => Config::get('wp-api.posts_per_page', 10),
            'debug' => Config::get('wp-api.debug', false)
        ], $options);

        $this->endpoint = $endpoint;
        $this->client   = GuzzleCache::client();
    }

    public function posts($page = null, $options = [])
    {
        $options['page'] = $page;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            $opts
        ));
    }

    public function pages($page = null, $options = [])
    {
        $options['type'] = 'page';
        $options['page'] = $page;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            $opts
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
        $options['page'] = $page;
        $options['filter']['category_name'] = $slug;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            $opts
        ));
    }

    public function author_posts($name, $page = null, $options = [])
    {
        $options['page'] = $page;
        $options['filter']['author_name'] = $name;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            $opts
        ));
    }

    public function tag_posts($tags, $page = null, $options = [])
    {
        $options['page'] = $page;
        $options['filter']['tag'] = $tags;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            $opts
        ));
    }

    public function search($query, $page = null, $options = [])
    {
        $options['page'] = $page;
        $options['filter']['s'] = $query;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            $opts
        ));
    }

    public function archive($year, $month, $page = null, $options = [])
    {
        $options['page'] = $page;
        $options['filter']['year'] = $year;
        $options['filter']['monthnum'] = $month;
        $opts = $this->extendDefaults($options);

        return $this->_resultsCollection($this->_get(
            'posts',
            $opts
        ));
    }

    public function _get($method, array $query = array())
    {
        try {

            $query = ['query' => $query];

            if($this->options['auth']) {
                $query['auth'] = $this->options['auth'];
            }

            $response = $this->client->get($this->endpoint . $method, $query);

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
            'filter' => [
                'posts_per_page' => $this->options['posts_per_page']
            ]
        ], $options);
    }

}