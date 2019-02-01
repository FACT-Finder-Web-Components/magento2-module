<?php

namespace Omikron\Factfinder\Test\Unit\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\ResourceModel\Config;
use Omikron\Factfinder\Helper\Communication;
use Omikron\Factfinder\Helper\Data;
use Omikron\Factfinder\Model\Client;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ClientTest extends TestCase
{
    /** @var Config | \PHPUnit_Framework_MockObject_MockObject */
    protected $configResourceMock;

    /** @var  ClientInterface | \PHPUnit_Framework_MockObject_MockObject*/
    protected $httpClientMock;

    /** @var  SerializerInterface | \PHPUnit_Framework_MockObject_MockObject*/
    protected $serializerMock;

    /** @var  Data | \PHPUnit_Framework_MockObject_MockObject*/
    protected $configHelperMock;

    /** @var  Communication | \PHPUnit_Framework_MockObject_MockObject*/
    protected $communicationHelperMock;

    /** @var  LoggerInterface | \PHPUnit_Framework_MockObject_MockObject*/
    protected $loggerMock;

    /** @var  Client*/
    protected $client;

    public function setUp()
    {
        $this->configResourceMock = $this->createMock(Config::class);
        $this->httpClientMock     = $this->createMock(ClientInterface::class);
        $this->serializerMock      = $this->createMock(SerializerInterface::class);
        $this->configHelperMock   = $this->createMock(Data::class);
        $this->communicationHelperMock = $this->createMock(Communication::class);
        $this->loggerMock         = $this->createMock(LoggerInterface::class);

        $this->configResourceMock->method('saveConfig')->willReturn($this->configResourceMock);
        $this->communicationHelperMock->method('getPassword')->willReturn('password');
        $this->communicationHelperMock->method('getPassword')->willReturn('http://fact-finder-test/FACT-Finder-7.3');
        $this->communicationHelperMock->method('getAuthenticationPrefix')->willReturn('authPrefix');
        $this->communicationHelperMock->method('getAuthenticationPostfix')->willReturn('authPostfix');
        $this->communicationHelperMock->method('getChannel')->willReturn('channel');
        $this->communicationHelperMock->method('getUsername')->willReturn('username');
        $this->loggerMock->method('error');

        $this->client = (new ObjectManager($this))
            ->getObject(
                Client::class,
                [
                    'configResource'      => $this->configResourceMock,
                    'httpClient'          => $this->httpClientMock,
                    'serializer'          => $this->serializerMock,
                    'configHelper'        => $this->configHelperMock,
                    'communicationHelper' => $this->communicationHelperMock,
                    'logger'              => $this->loggerMock
                ]
            );
    }

    public function testCurlPreparation()
    {
        $params              = 'sid=er952u6mnfdncfqdsmbvs5jqqa&id=24-MB01&masterId=24-MB01&count=1&price=34.00';
        $apiName             = 'Tracking.ff';
        $successfulResponse = 'Event was successfully tracked';

        $this->httpClientMock->expects($this->exactly(2))->method('setOption')->withConsecutive([CURLOPT_ENCODING, 'Accept-encoding: gzip, deflate'],[CURLOPT_RETURNTRANSFER, 1]);
        $this->httpClientMock->expects($this->once())->method('get');
        $this->httpClientMock->expects($this->once())->method('getBody')->willReturn($successfulResponse);

        $response = $this->client->sendToFF($apiName, $params);

        //check if actual result is always result of getBody httpClient
        $this->assertEquals($successfulResponse, $response, 'Response message is not equal to expected');
    }

    public function testPushImportShouldReturnFalseIfNoDataTypeConfigured()
    {
        $this->communicationHelperMock->method('getPushImportTypes')->willReturn([]);
        $this->serializerMock->expects($this->never())->method('unserialize');
        $this->loggerMock->expects($this->never())->method('error');
        $this->loggerMock->expects($this->never())->method('info');

        $this->client->pushImport(1);
    }

    public function testPushImportShouldReturnTrueIfNoErrors()
    {
        $response = '{"result" : "success"}';
        $responseUnserialized = ['result' => 'success'];
        $this->communicationHelperMock->method('getPushImportTypes')->willReturn(['data','suggest']);
        $this->httpClientMock->expects($this->exactly(2))->method('getBody')->willReturn($response);
        $this->serializerMock->method('unserialize')->with($response)->willReturn($responseUnserialized);
        $this->serializerMock->method('serialize')->willReturn($response);

        $result = $this->client->pushImport(1);

        $this->assertEquals(true, $result, 'Result should be true in this test case');
    }

    public function testPushImportExceptionShouldBeLogged()
    {
        $wrongFormatResponse = '<?xml version="1.0"?><root><error>something wrong happened</error></root>';
        $this->communicationHelperMock->method('getPushImportTypes')->willReturn(['data','suggest']);
        $this->httpClientMock->method('getBody')->willReturn($wrongFormatResponse);
        $this->serializerMock->method('unserialize')->with($wrongFormatResponse)->willThrowException(new \InvalidArgumentException());
        $this->loggerMock->expects($this->once())->method('error');
        $this->configHelperMock->method('isLoggingEnabled')->willReturn(true);

        $this->client->pushImport(1);
    }

    public function testResponseShouldNotBeLoggedIfLoggingIsDisabled()
    {
        $response = '{"result" : "success"}';
        $this->communicationHelperMock->method('getPushImportTypes')->willReturn(['data']);
        $this->configHelperMock->method('isLoggingEnabled')->willReturn(false);
        $this->httpClientMock->method('getBody')->willReturn($response);
        $this->serializerMock->method('unserialize')->with($response)->willReturn(['result' => 'success']);
        $this->serializerMock->method('serialize')->willReturn($response);
        $this->loggerMock->expects($this->never())->method('info');

        $this->client->pushImport(1);
    }

    public function testUpdateFieldRolesHasCorrectResponseFormat()
    {
        $response = '{"searchResult":{"breadCrumbTrailItems":[],"campaigns":[],"channel":"channel","fieldRoles":{"brand":"Manufacturer","campaignProductNumber":"ProductNumber","deeplink":"ProductUrl","description":"Description","displayProductNumber":"ProductNumber","ean":"EAN","imageUrl":"ImageUrl","masterArticleNumber":"MasterProductNumber","price":"Price","productName":"Name","trackingProductNumber":"ProductNumber"},"filters":[],"groups":[],"paging":{"currentPage":1,"firstLink":{"caption":"1","currentPage":true,"number":1,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026verbose=true\u0026format=JSON"},"lastLink":{"caption":"723","currentPage":false,"number":723,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=723\u0026verbose=true\u0026format=JSON"},"nextLink":{"caption":"\u003e\u003e","currentPage":false,"number":2,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=2\u0026verbose=true\u0026format=JSON"},"pageCount":723,"pageLinks":[{"caption":"1","currentPage":true,"number":1,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026verbose=true\u0026format=JSON"},{"caption":"2","currentPage":false,"number":2,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=2\u0026verbose=true\u0026format=JSON"},{"caption":"3","currentPage":false,"number":3,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=3\u0026verbose=true\u0026format=JSON"},{"caption":"4","currentPage":false,"number":4,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=4\u0026verbose=true\u0026format=JSON"},{"caption":"5","currentPage":false,"number":5,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=5\u0026verbose=true\u0026format=JSON"},{"caption":"6","currentPage":false,"number":6,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=6\u0026verbose=true\u0026format=JSON"},{"caption":"7","currentPage":false,"number":7,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=7\u0026verbose=true\u0026format=JSON"},{"caption":"8","currentPage":false,"number":8,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=8\u0026verbose=true\u0026format=JSON"},{"caption":"9","currentPage":false,"number":9,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026page=9\u0026verbose=true\u0026format=JSON"}],"previousLink":null,"resultsPerPage":10},"records":[{"foundWords":[],"id":"24-MB01","keywords":[],"position":1,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":2,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":3,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":4,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":5,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":6,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":7,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":8,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":9,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0},{"foundWords":[],"id":"24-MB01","keywords":[],"position":10,"record":{"Email":"","Description":"The sporty Joust Duffle Bag can\u0027t be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it\u0027s ideal for athletes with places to go.Dual top handles.Adjustable shoulder strap.Full-length zipper.L 29\u0022 x W 13\u0022 x H 11\u0022.","MagentoEntityId":"1","MetaKeywords":"","FeaturesBags":"","PageImage":"","Name":"Joust Duffle Bag","CategoryPath":"|CategoryPathROOT=Gear|CategoryPathROOT/Gear=Bags|","Identifier":"","PageUrl":"","FirstFailure":"","Dob":"","ErinRecommends":"","Manufacturer":"..Black..","MetaDescription":"","Short":"","Availability":"1","PageId":"","MasterProductNumber":"24-MB01","ImageUrl":"http://magento2.local/media/catalog/product/cache/6dd18fb85a59916e944c7f1f42e58a4c/m/b/mb01-blue-0.jpg","Title":"","ProductNumber":"24-MB01","ProductUrl":"http://magento2.local/joust-duffle-bag.html","Firstname":"","ContentHeading":"","EAN":"24-MB01","Price":"34.00","FilterPriceRange":"","Content":""},"searchSimilarity":100.0,"simiMalusAdd":0}],"resultArticleNumberStatus":"noArticleNumberSearch","resultCount":7227,"resultStatus":"resultsFound","resultsPerPageList":[{"default":false,"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026verbose=true\u0026format=JSON","selected":true,"value":10}],"searchControlParams":{"disableCache":false,"generateAdvisorTree":true,"idsOnly":false,"useAsn":true,"useAso":true,"useCampaigns":true,"useFoundWords":false,"useKeywords":false,"usePersonalization":true,"useSemanticEnhancer":true},"searchParams":"/FACT-Finder-7.3/Search.ff?query=FACT-Finder+version\u0026channel=channel\u0026verbose=true\u0026format=JSON","searchTime":13,"simiFirstRecord":10000,"simiLastRecord":10000,"singleWordResults":null,"sortsList":[],"timedOut":false}}';
        $fieldRoles = '{"brand":"Manufacturer","campaignProductNumber":"ProductNumber","deeplink":"ProductUrl","description":"Description","displayProductNumber":"ProductNumber","ean":"EAN","imageUrl":"ImageUrl","masterArticleNumber":"MasterProductNumber","price":"Price","productName":"Name","trackingProductNumber":"ProductNumber"}';
        $responseUnserialized = json_decode($response, true);
        $this->serializerMock->method('unserialize')->with($response)->willReturn($responseUnserialized);
        $this->httpClientMock->method('getBody')->willReturn($response);

        $result = $this->client->updateFieldRoles(1);

        $this->assertArrayHasKey('ff_response_decoded', $result, 'Correct response should contain ff_response_decoded element');
        $this->assertArrayNotHasKey('error', $result['ff_response_decoded'], 'Correct response should not contain error element');
        $this->assertEquals(
            json_decode($fieldRoles, true), $result['ff_response_decoded']['searchResult']['fieldRoles'],
            'Correct update field roles should contains them as part of response'
        );
        $this->assertTrue($result['success'], 'Correct update field roles should return true value');
    }

    public function testUpdateFieldRolesExceptionShouldBelogged()
    {
        $wrongFormatResponse = '<?xml version="1.0"?><root><error>something wrong happened</error></root>';
        $this->httpClientMock->method('getBody')->willReturn($wrongFormatResponse);
        $this->serializerMock->method('unserialize')->with($wrongFormatResponse)->willThrowException(new \InvalidArgumentException());
        $this->configHelperMock->method('isLoggingEnabled')->willReturn(true);
        $this->loggerMock->expects($this->once())->method('error');

        $this->client->updateFieldRoles(1);
    }
}
