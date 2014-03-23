<?php

namespace Tests\Wikibase\DumpReader;

use Wikibase\DumpReader\DumpReader;
use Wikibase\DumpReader\XmlReader\DumpXmlReader;

/**
 * @covers Wikibase\DumpReader\XmlReader\DumpXmlReader
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpXmlReaderTest extends \PHPUnit_Framework_TestCase {

	public function testGivenFileWithNoEntities_nullIsReturned() {
		$reader = $this->newReaderForFile( 'simple/empty.xml' );

		$this->assertNull( $reader->nextEntityJson() );
	}

	private function newReaderForFile( $fileName ) {
		return new DumpXmlReader( $this->getFilePath( $fileName ) );
	}

	private function getFilePath( $fileName ) {
		return __DIR__ . '/../../../data/' . $fileName;
	}

	private function assertFindsAnotherEntity( DumpReader $reader ) {
		$entityJson = $reader->nextEntityJson();
		$this->assertIsEntityJson( $entityJson );
	}

	private function assertIsEntityJson( $entityJson ) {
		$this->assertInternalType( 'string', $entityJson );

		$entityArray = json_decode( $entityJson, true );
		$this->assertInternalType( 'array', $entityArray );

		$this->assertArrayHasKey( 'entity', $entityArray );
	}

	public function testGivenFileWithOneEntity_oneEntityIsFound() {
		$reader = $this->newReaderForFile( 'simple/one-item.xml' );

		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntityJson() );
	}

	public function testRewind() {
		$reader = $this->newReaderForFile( 'simple/one-item.xml' );

		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntityJson() );

		$reader->rewind();

		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntityJson() );
	}

	public function testGivenFileWithTwoEntities_twoEntitiesAreFound() {
		$reader = $this->newReaderForFile( 'simple/two-items.xml' );

		$this->assertFindsAnotherEntity( $reader );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntityJson() );
	}

	public function testGivenEntityAmongstNonEntities_itemIsFound() {
		$reader = $this->newReaderForFile( 'simple/item-amongst-wikitext.xml' );

		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntityJson() );
	}

	public function testFetchedIteratorIterationWorks() {
		$reader = $this->newReaderForFile( 'simple/two-items.xml' )->getIterator();

		$this->assertCount( 2, $reader );
		$this->assertContainsOnly( 'string', $reader );
	}

	public function testCanUseAsTraversable() {
		$reader = $this->newReaderForFile( 'simple/two-items.xml' );

		foreach ( $reader as $json ) {
			$this->assertIsEntityJson( $json );
		}
	}

	public function testGivenManyRevisions_allPropertiesAreFound() {
		$reader = $this->newReaderForFile( 'big/5341-revs-3-props.xml' );

		$propertyCount = 0;

		while ( $json = $reader->nextEntityJson() ) {
			$this->assertIsEntityJson( $json );

			if ( $this->isPropertyJson( $json ) ) {
				$propertyCount++;
			}
		}

		$this->assertEquals( 3, $propertyCount );
	}

	private function isPropertyJson( $json ) {
		$array = json_decode( $json, true );
		return $array['entity'][0] === 'property';
	}

	public function testGivenItemWithoutRevision_exceptionInThrown() {
		$reader = $this->newReaderForFile( 'invalid/item-without-revision.xml' );

		$this->setExpectedException( 'Wikibase\DumpReader\DumpReaderException' );
		$reader->nextEntityJson();
	}

	// 14520188 entities -> 43:26
//	public function testTime() {
//		$reader = new DumpXmlReader( '/home/j/Downloads/wikidatawiki-20140226-pages-articles.xml' );
//
//		$count = 0;
//
//		while ( $json = $reader->nextEntityJson() ) {
//			$this->assertIsEntityJson( $json );
//			$count++;
//		}
//
//		$this->assertEquals( 0, $count );
//	}

}