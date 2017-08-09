<?php

namespace Devpoint\SearchClient\Elasticsearch;

use Elasticsearch\Client as Elastic;
use Devpoint\SearchClient\Contracts\SearchClientBuilder as SearchClientBuilderContract;

class SearchClientBuilder implements SearchClientBuilderContract {

    /**
     * @var Elastic
     */
    private $elastic;

    /**
     * @var string
     */
    private $elasticIndex;

    /**
     * @var string
     */
    private $elasticType;

    /**
     * @var string
     */
    protected $limit;

    /**
     * @var array
     */
    protected $filters;

    /**
     * Constructor
     *
     * @param  Elastic  $elastic
     * @param  string   $elasticIndex
     * @param  string   $elasticType
     * @param  string   $query
     */
    public function __construct(Elastic $elastic, $elasticIndex, $elasticType, $query)
    {
        $this->elastic = $elastic;
        $this->elasticIndex = $elasticIndex;
        $this->elasticType = $elasticType;
        $this->query = $query;
        $this->filters = [];
        $this->limit = 10;
    }

    /**
     * Build the match phrases for the query.
     *
     * @return array
     */
    protected function buildMatchPhrases()
    {
        $matchPhrases = [];
        foreach ($this->filters as $key => $value) 
        {
            $matchPhrases[] = [
                'match_phrase' => [$key => $value]
            ];
        }
        return $matchPhrases;
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  array  $options
     * @return mixed
     */
    protected function performSearch(array $options = [])
    {
        $params = [
            'index' => $this->elasticIndex,
            'type' => $this->elasticType,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [['query_string' => [ 'query' => "*{$this->query}*"]]]
                    ]
                ]
            ]
        ];

        if (isset($options['from'])) 
        {
            $params['body']['from'] = $options['from'];
        }

        if (isset($options['size'])) 
        {
            $params['body']['size'] = $options['size'];
        }

        if (isset($options['numericFilters']) && count($options['numericFilters'])) 
        {
            $params['body']['query']['bool']['must'] = array_merge($params['body']['query']['bool']['must'],
                $options['numericFilters']);
        }

        return $this->elastic->search($params);
    }

    /**
     * Add search filter for a field.
     *
     * @param  string  key
     * @param  mixed   value
     * @return self
     */
    public function filterExp($key, $value)
    {
        $this->filters[$key] = $value;
    }
    
    /**
     * Override the default index.
     *
     * @param  string  $index
     * @return self
     */
    public function index($index)
    {
        $this->elasticType = $index;
    }
    
    /**
     * Perform paginated search.
     *
     * @param  int    $page
     * @param  int    $pageSize
     * @return SearchClientResponse
     */
    public function paginate($page, $pageSize)
    {
        $statusCode = static::STATUS_OK;
        $searchResponse = ['hits' => []];
        try 
        {
            $searchResponse = $this->performSearch([
                'numericFilters' => $this->buildMatchPhrases(),
                'from' => (($page * $pageSize) - $pageSize),
                'size' => $pageSize]);
        }
        catch (\Exception $exception)
        {
            $statusCode = static::STATUS_ERROR;
        }
        return new SearchClientResponse($searchResponse['hits'], $pageSize, $statusCode);
    }

    /**
     * Perform search.
     *
     * @return SearchClientResponse
     */
    public function get()
    {
        $statusCode = static::STATUS_OK;
        $searchResponse = ['hits' => []];
        try 
        {
            $searchResponse = $this->performSearch([
                'numericFilters' => $this->buildMatchPhrases(),
                'size' => $this->limit]);
        }
        catch (\Exception $exception)
        {
            $statusCode = static::STATUS_ERROR;
        }
        return new SearchClientResponse($searchResponse['hits'], $this->limit, $statusCode);
    }
}
