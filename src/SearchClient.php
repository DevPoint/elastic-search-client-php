<?php

namespace Devpoint\SearchClient\Elasticsearch;

use Elasticsearch\Client as Elastic;
use Devpoint\SearchClient\Contracts\SearchClient as SearchClientContract;
use Devpoint\SearchClient\Contracts\SearchClientBuilder as SearchClientBuilderContract;

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
     * Update values on the index.
     * 
     * @param  string  $index
     * @param  array   $valuesWithMeta
     * @return void
     */
    public function update($index, $valuesWithMeta)
    {
        // TODO: ..
    }

    /**
     * Delete values from the index.
     *
     * @param  string  $index
     * @param  array   $ids
     * @return void
     */
    public function delete($index, $ids)
    {
        // TODO: ..
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
