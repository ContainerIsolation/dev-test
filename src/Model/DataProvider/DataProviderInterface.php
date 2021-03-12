<?php
namespace Totallywicked\DevTest\Model\DataProvider;

/**
 * A simple flexible interface that accepts data in array format and returns data in array format.
 */
interface DataProviderInterface
{
    /**
     * Does some processing and returns data
     * @param array $data
     * @return array
     */
    function getData(array $data): array;
}
