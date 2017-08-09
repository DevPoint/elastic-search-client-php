<?php

namespace App\Search\Elasticsearch;

use Elasticsearch\Client as Elastic;
use App\Search\Contracts\SearchClient as SearchClientContract;
use App\Search\Contracts\SearchClientBuilder as SearchClientBuilderContract;

class SearchClient implements SearchClientContract {
    
    /**
     * @var Elastic
     */
    private $elastic;

    /**
     * @var string
     */
    private $elasticIndex;

    /**
     * Constructor
     *
     * @param  Elastic  $elastic
     * @param  string   $elasticIndex
     */
    public function __construct(Elastic $elastic, $elasticIndex)
    {
        $this->elastic = $elastic;
        $this->elasticIndex = $elasticIndex;
    }

    /**
     * @param  string  $index
     * @param  string  $q
     * @return SearchBuilderContract
     */
    public function query($index, $q)
    {
    	return new SearchClientBuilder(
    		$this->elastic,
    		$this->elasticIndex,
    		$index, $q);
    }
}
