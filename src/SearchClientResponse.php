<?php

namespace Devpoint\SearchClient\Elasticsearch;

use Devpoint\SearchClient\Contracts\SearchClientResponse as SearchClientResponseContract;

class SearchClientResponse implements SearchClientResponseContract {

    /**
     * @var array
     */
    protected $hits;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var string
     */
    protected $status;

    /**
     * Constructor
     *
     * @param  array   $hits
     * @param  string  $limit
     * @param  string  $status
     */
    public function __construct($hits, $limit, $status)
    {
        $this->hits = $hits;
        $this->limit = $limit;
        $this->status = $status;
    }

    /**
     * Get the take limit.
     *
     * @return int
     */
    public function limit()
    {
        $this->limit;
    }
    
    /**
     * Get the status.
     *
     * @return string
     */
    public function status()
    {
        return $this->status;
    }
    
    /**
     * Get the values count.
     *
     * @return int
     */
    public function count()
    {
        return count($this->hits['hits']);
    }
    
    /**
     * Get the total count.
     *
     * @return int
     */
    public function totalCount()
    {
        return $this->hits['total'];
    }
    
    /**
     * Get the maximal score.
     *
     * @return float
     */
    public function maxScore()
    {
        return $this->hits['max_score'];
    }

    /**
     * Retrieve values together with meta information.
     * 
     * @return array
     */
    public function valuesWithMeta()
    {
        $values = [];
        if ($this->count() > 0) 
        {
            $values = array_map(
                function($hit) { 
                    return [
                        'id' => $hit['_id'],
                        'index' => $hit['_type'],
                        'score' => $hit['_score'],
                        'data' => $hit['_source']];
                }, 
                $this->hits['hits']);
        }
        return $values;
    }
    
    /**
     * Retrieve values.
     * 
     * @return array
     */
    public function values()
    {
        $values = [];
        if ($this->count() > 0) 
        {
            $values = array_map(
                function($hit) { return $hit['_source']; }, 
                $this->hits['hits']);
        }
        return $values;
    }

    /**
     * Retrieve values ids.
     *
     * @return array
     */
    public function ids()
    {
        $ids = [];
        if ($this->count() > 0) 
        {
            $ids = array_map(
                function($hit) { return $hit['_id']; }, 
                $this->hits['hits']);
        }
        return $ids;
    }
}