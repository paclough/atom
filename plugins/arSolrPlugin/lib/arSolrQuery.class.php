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

/**
 * arSolrQuery.
 */
class arSolrQuery
{
    /**
     * Array of fields to be queried.
     *
     * @var array
     */
    protected $fields;

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
     * Query Params.
     *
     * @var mixed
     */
    protected $query;

    /**
     * Number of results to fetch.
     *
     * @var number defaults to 10
     */
    protected $size = 10;

    /**
     * Offset for search results.
     *
     * @var number defaults to 0
     */
    protected $offset = 0;

    /**
     * Constructor.
     *
     * @param mixed $searchQuery
     */
    public function __construct($searchQuery)
    {
        if (!$this->fields) {
            $fields = arSolrPluginUtil::getBoostedSearchFields([
                'identifier' => 10,
                'donors.i18n.%s.authorizedFormOfName' => 10,
                'i18n.%s.title' => 10,
                'i18n.%s.scopeAndContent' => 10,
                'i18n.%s.locationInformation' => 5,
                'i18n.%s.processingNotes' => 5,
                'i18n.%s.sourceOfAcquisition' => 5,
                'i18n.%s.archivalHistory' => 5,
                'i18n.%s.appraisal' => 1,
                'i18n.%s.physicalCharacteristics' => 1,
                'i18n.%s.receivedExtentUnits' => 1,
                'alternativeIdentifiers.i18n.%s.name' => 1,
                'creators.i18n.%s.authorizedFormOfName' => 1,
                'alternativeIdentifiers.i18n.%s.note' => 1,
                'alternativeIdentifiers.type.i18n.%s.name' => 1,
                'accessionEvents.i18n.%s.agent' => 1,
                'accessionEvents.type.i18n.%s.name' => 1,
                'accessionEvents.notes.i18n.%s.content' => 1,
                'donors.contactInformations.contactPerson' => 1,
                'accessionEvents.dateString' => 1,
            ]);
        }
        $this->setSearchQuery($searchQuery);
        $this->generateQueryParams();
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
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
            'params' => [
                'start' => $this->offset,
                'rows' => $this->size,
                'q.op' => $this->operator,
                'defType' => 'dismax',
                'stopwords' => 'true',
                'q' => $this->searchQuery,
                'qf' => implode(' ', $this->fields),
            ],
        ];
    }
}
