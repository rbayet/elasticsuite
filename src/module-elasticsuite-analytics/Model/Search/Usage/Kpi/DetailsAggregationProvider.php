<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile ElasticSuite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteAnalytics
 * @author    Richard BAYET <richard.bayet@smile.fr>
 * @copyright 2025 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ElasticsuiteAnalytics\Model\Search\Usage\Kpi;

use Smile\ElasticsuiteAnalytics\Model\Report\AggregationProviderInterface;
use Smile\ElasticsuiteCore\Search\Request\Aggregation\AggregationFactory;
use Smile\ElasticsuiteCore\Search\Request\Aggregation\MetricFactory;
use Smile\ElasticsuiteCore\Search\Request\BucketInterface;
use Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory;
use Smile\ElasticsuiteCore\Search\Request\QueryInterface;

/**
 * Page type details aggregation provider for the Search usage KPI report.
 *
 * @category Smile
 * @package  Smile\ElasticsuiteAnalytics
 */
class DetailsAggregationProvider implements AggregationProviderInterface
{
    /**
     * @var AggregationFactory
     */
    private $aggregationFactory;

    /**
     * @var MetricFactory
     */
    private $metricFactory;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * DetailsAggregationProvider constructor.
     *
     * @param AggregationFactory $aggregationFactory Aggregation factory.
     * @param MetricFactory      $metricFactory      Metric factory.
     * @param QueryFactory       $queryFactory       Query factory.
     */
    public function __construct(
        AggregationFactory $aggregationFactory,
        MetricFactory $metricFactory,
        QueryFactory $queryFactory
    ) {
        $this->aggregationFactory   = $aggregationFactory;
        $this->metricFactory        = $metricFactory;
        $this->queryFactory         = $queryFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getAggregation()
    {
        $aggParams = [
            'name'    => 'details',
            'queries' => $this->getQueries(),
            'metrics' => [],
        ];

        return $this->aggregationFactory->create(BucketInterface::TYPE_QUERY_GROUP, $aggParams);
    }

    /**
     * Return the queries of the query group aggregation.
     *
     * @return array
     */
    private function getQueries()
    {
        return [
            'product_views' => $this->queryFactory->create(
                QueryInterface::TYPE_TERM,
                [
                    'field' => 'page.type.identifier',
                    'value' => 'catalog_product_view',
                ]
            ),
            'category_views' => $this->queryFactory->create(
                QueryInterface::TYPE_TERM,
                [
                    'field' => 'page.type.identifier',
                    'value' => 'catalog_category_view',
                ]
            ),
            'add_to_cart' => $this->queryFactory->create(
                QueryInterface::TYPE_EXISTS,
                [
                    'field' => 'page.cart.product_id',
                ]
            ),
            'sales' => $this->queryFactory->create(
                QueryInterface::TYPE_TERM,
                [
                    'field' => 'page.type.identifier',
                    'value' => 'checkout_onepage_success',
                ]
            ),
        ];
    }
}
