<?php

/*
 * This file is part of the Pondtunes package.
 *
 * (c) Marcus Stöhr <dafish@soundtrack-board.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pond\Tunes;

use Pond\Tunes\Tunes;
use Pond\Tunes\Search;

class TunesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Search
     */
    protected $itunesSearch =   null;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->itunesSearch = new Search();
    }
    
    public function testSetTermsAsArray()
    {
        $this->itunesSearch->setTerms(array('christopher', 'gordon'));
        $this->assertEquals(array('christopher', 'gordon'), $this->itunesSearch->getTerms());
    }

    public function testSetTermAsString()
    {
        $this->itunesSearch->setTerms('star trek');
        $this->assertEquals(array('star', 'trek'), $this->itunesSearch->getTerms());
    }
    
    public function testTermsNotArray()
    {
        $this->itunesSearch->setTerms('star');
        $this->assertEquals(array('star'), $this->itunesSearch->getTerms());
    }
    
    public function testSetGetLimit()
    {
        $this->itunesSearch->setLimit(42);
        $this->assertEquals(42, $this->itunesSearch->limit);
    }
    
    public function testSetGetExplicity()
    {
        $this->itunesSearch->setExplicit('yes');
        $this->assertEquals('yes', $this->itunesSearch->explicit);
    }
    
    public function testSetGetCallback()
    {
        $this->itunesSearch->setResultFormat(Tunes::RESULT_JSON);
        $this->itunesSearch->setCallback('wbFoobar');
        $this->assertEquals('wbFoobar', $this->itunesSearch->callback);
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testSetGetCallbackException()
    {
        $this->itunesSearch->setResultFormat(Tunes::RESULT_ARRAY);
        $this->itunesSearch->setCallback('wbFoobar');
        
        $this->fail('An expected exception has not been raised!');
    }
    
    public function testSetGetCountry()
    {
        $this->itunesSearch->setCountry('gb');
        $this->assertEquals('gb', $this->itunesSearch->country);
    }
    
    public function testCountryDefaultValue()
    {
        $this->assertEquals('us', $this->itunesSearch->country);
    }
    
    public function testSetGetCountryOutOfRange()
    {
        $this->itunesSearch->setCountry('dd');
        $this->assertEquals('us', $this->itunesSearch->country);
    }
    
    public function testSetGetLanguage()
    {
        $this->itunesSearch->setLanguage('ja_jp');
        $this->assertEquals('ja_jp', $this->itunesSearch->language);
    }
    
    public function testSetGetMediaType()
    {
        $this->itunesSearch->setMediaType(Tunes::MEDIATYPE_TVSHOW);
        $this->assertEquals(Tunes::MEDIATYPE_TVSHOW, $this->itunesSearch->mediaType);
    }
    
    public function testSetGetResultFormat()
    {
        $this->itunesSearch->setResultFormat(Tunes::RESULT_ARRAY);
        $this->assertEquals(Tunes::RESULT_ARRAY, $this->itunesSearch->getResultFormat());
    }
    
    public function testSetGetEntity()
    {
        $temp = array();
        $temp['music'] = 'musicVideo';
        
        $this->itunesSearch->setEntity($temp);
        $this->assertEquals($temp, $this->itunesSearch->entity);
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testSetEntityExceptionCountToLow()
    {
        $this->itunesSearch->setEntity();
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testSetEntityExceptionCountToHigh()
    {
        $temp = array('foo' => 'bar', 'lorem' => 'ipsum');
        $this->itunesSearch->setEntity($temp);

        $this->fail('An expected exception has not been raised!');
    }
    
    public function testSetGetVersion()
    {
        $this->itunesSearch->setVersion(1);
        $this->assertEquals(1, $this->itunesSearch->version);
        
        $this->itunesSearch->setVersion(3);
        $this->assertEquals(1, $this->itunesSearch->version);
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testEmptyQueryException()
    {
        $this->itunesSearch->request();
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testQueryWithCallbackException()
    {
        $this->itunesSearch->setCallback('wsCallback');
        
        $this->itunesSearch->request();
    }
    
    public function testOptionNotSet()
    {
        $this->assertEquals(null, $this->itunesSearch->foobar);
    }
    
    public function testGetRawRequestUrl()
    {
        $this->itunesSearch->setTerms(array('star', 'trek'))
                            ->setCountry('de')
                            ->setCallback('wsCallback');
        
        $this->assertEquals('http://ax.phobos.apple.com.edgesuite.net/WebObjects/MZStoreServices.woa/wa/wsSearch?entity=album&country=de&callback=wsCallback&term=star+trek', $this->itunesSearch->getRawRequestUrl());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetIllegalAttribute()
    {
        $this->itunesSearch->setMediaType(Tunes::MEDIATYPE_MUSIC);
        $this->itunesSearch->setAttribute('iPadSoftware');
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testAttributeWithEmptyMediaType()
    {
        $this->itunesSearch->setAttribute('albumTerm');
    }
    
    /**
     * @expectedException \LogicException
     */
    public function testAttributeWrongAttributeToMediaType()
    {
        $this->itunesSearch->setMediaType(Tunes::MEDIATYPE_MUSIC);
        $this->itunesSearch->setAttribute('actorTerm');
    }
    
    public function testGetListOfCountries()
    {
        $this->assertInternalType('array', $this->itunesSearch->getCountries());
    }
    
    public function testQueryWithCustomSettings()
    {
        $this->itunesSearch->setMediaType('podcast');
        $this->itunesSearch->setTerms('star');
        $this->itunesSearch->setAttribute('authorTerm');
        $this->itunesSearch->setLanguage('ja_jp');
        $this->itunesSearch->setLimit(1);
        $this->itunesSearch->setVersion(1);
        $this->itunesSearch->setExplicit('no');
        
        $this->assertEquals(
            'http://ax.phobos.apple.com.edgesuite.net/WebObjects/MZStoreServices.woa/wa/wsSearch?entity=album&media=podcast&attribute=authorTerm&lang=ja_jp&limit=1&version=1&explicit=no&term=star',
            $this->itunesSearch->getRawRequestUrl()
        );
    }
}