<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Integration\Controller\Adminhtml\Export;

use Magento\TestFramework\TestCase\AbstractBackendController;

class PreviewTest extends AbstractBackendController
{
    public function testShouldReturnJsonWithMessageWhenSomeErrorOccurred()
    {
        // Expect & Given
        $this->dispatch('backend/factfinder/export/preview/entityId/0');

        // When & Then
        $this->assertEquals(
            '{"message":"Product will not be exported. Reason: Product with ID \"0\" does not exist."}',
            $this->getResponse()->getContent()
        );
    }

    public function testShouldReturnJsonWithItemsInDesiredFormat()
    {
        // Expect & Given
        $this->dispatch('backend/factfinder/export/preview/entityId/1');

        // When & Then
        $this->assertEquals(
            '{"totalRecords":1,"items":[{"ProductNumber":"24-MB01","Master":"24-MB01","Name":"Joust Duffle Bag","Description":"The sporty Joust Duffle Bag can\'t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\'s ideal for athletes with places to go. Dual top handles. Adjustable shoulder strap. Full-length zipper. L 29\" x W 13\" x H 11\".","Short":"","Deeplink":"http:\/\/localhost\/index.php\/joust-duffle-bag.html","Price":"34.00","Manufacturer":"","Availability":"1","MagentoId":"1","ImageUrl":"http:\/\/localhost\/static\/version1657536900\/adminhtml\/Magento\/backend\/en_US\/Magento_Catalog\/images\/product\/placeholder\/thumbnail.jpg","CategoryPath":"Gear|Gear\/Bags","FilterAttributes":"","HasVariants":"0","NumericalAttributes":""}]}',
            $this->getResponse()->getContent()
        );
    }
}
