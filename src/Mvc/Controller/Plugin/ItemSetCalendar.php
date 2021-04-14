<?php
namespace ItemSetCalendar\Mvc\Controller\Plugin;

use Doctrine\ORM\EntityManager;
use Omeka\Api\Adapter\Manager as ApiAdapterManager;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class ItemSetCalendar extends AbstractPlugin
{
    const DATE_REGEXP = '^[[:space:]]*[0-9]{4}(/[0-9]{4}|-[0-9]{2}|-[0-9]{2}-[0-9]{2})?[[:space:]]*$';

    protected $entityManager;
    protected $apiAdapterManager;

    public function __construct(EntityManager $entityManager, ApiAdapterManager $apiAdapterManager)
    {
        $this->entityManager = $entityManager;
        $this->apiAdapterManager = $apiAdapterManager;
    }

    public function getYears($itemSetId)
    {
        $dql = '
            SELECT DISTINCT SUBSTRING(TRIM(v.value), 1, 4) AS year
            FROM Omeka\Entity\Item i
              JOIN i.values v
              JOIN v.property p
              JOIN p.vocabulary vocab
            WHERE :itemSetId MEMBER OF i.itemSets
              AND vocab.prefix = :prefix
              AND p.localName = :localName
              AND REGEXP(v.value, :regexp) = true
        ';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('itemSetId', $itemSetId);
        $query->setParameter('prefix', 'dcterms');
        $query->setParameter('localName', 'date');
        $query->setParameter('regexp', self::DATE_REGEXP);
        $results = $query->getResult();

        $years = array_column($results, 'year');

        return $years;
    }

    public function getItems($itemSetId, $year)
    {
        $dql = '
            SELECT i
            FROM Omeka\Entity\Item i
              JOIN i.values v
              JOIN v.property p
              JOIN p.vocabulary vocab
            WHERE :itemSetId MEMBER OF i.itemSets
              AND vocab.prefix = :prefix
              AND p.localName = :localName
              AND REGEXP(v.value, :regexp) = true
              AND SUBSTRING(TRIM(v.value), 1, 4) = :year
        ';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('itemSetId', $itemSetId);
        $query->setParameter('prefix', 'dcterms');
        $query->setParameter('localName', 'date');
        $query->setParameter('regexp', self::DATE_REGEXP);
        $query->setParameter('year', $year);
        $results = $query->getResult();

        $itemAdapter = $this->apiAdapterManager->get('items');
        $items = array_map(function ($result) use ($itemAdapter) {
            return $itemAdapter->getRepresentation($result);
        }, $results);

        return $items;
    }

    public function getItemsByDate($itemSetId, $date)
    {
        $dql = '
            SELECT i
            FROM Omeka\Entity\Item i
              JOIN i.values v
              JOIN v.property p
              JOIN p.vocabulary vocab
            WHERE :itemSetId MEMBER OF i.itemSets
              AND vocab.prefix = :prefix
              AND p.localName = :localName
              AND TRIM(v.value) = :date
        ';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('itemSetId', $itemSetId);
        $query->setParameter('prefix', 'dcterms');
        $query->setParameter('localName', 'date');
        $query->setParameter('date', $date);
        $results = $query->getResult();

        $itemAdapter = $this->apiAdapterManager->get('items');
        $items = array_map(function ($result) use ($itemAdapter) {
            return $itemAdapter->getRepresentation($result);
        }, $results);

        return $items;
    }
}
