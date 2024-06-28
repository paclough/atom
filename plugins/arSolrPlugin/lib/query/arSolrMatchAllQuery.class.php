<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

class arSolrMatchAllQuery extends arSolrAbstractQuery
{
    /**
     * Query Params.
     *
     * @var mixed
     */
    protected $query;

    /**
     * Default operator.
     *
     * @var string defaults to 'AND'
     */
    protected $operator = 'AND';

    /**
     * Search query.
     *
     * @var string
     */
    protected $searchQuery = '*:*';

    /**
     * Params.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor.
     *
     * @param mixed $searchQuery
     */
    public function __construct()
    {
        $this->generateQueryParams();
    }

    public function setDefaultOperator($operator)
    {
        $this->operator = $operator;
    }

    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;
    }

    public function getQueryParams()
    {
        $this->generateQueryParams();

        return $this->query;
    }

    public function generateQueryParams()
    {
        $this->query = [
            'query' => [
                'lucene' => [
                    'start' => $this->offset,
                    'rows' => $this->size,
                    'q.op' => $this->operator,
                    'stopwords' => 'true',
                    'query' => $this->searchQuery,
                ],
            ],
        ];
    }
}
