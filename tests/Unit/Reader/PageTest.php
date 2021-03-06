<?php

namespace Tests\Queryr\DumpReader;

use Queryr\DumpReader\Page;

/**
 * @covers Queryr\DumpReader\Page
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageTest extends \PHPUnit_Framework_TestCase {

	public function testDataIsRetained() {
		$id = 42;
		$title = 'Q42';
		$namespace = 0;
		$revision = $this->getMockBuilder( 'Queryr\DumpReader\Revision' )
			->disableOriginalConstructor()->getMock();

		$page = new Page( $id, $title, $namespace, $revision );

		$this->assertSame( $id, $page->getId() );
		$this->assertSame( $title, $page->getTitle() );
		$this->assertSame( $namespace, $page->getNamespace() );
		$this->assertEquals( $revision, $page->getRevision() );
	}

	public function testGivenStringIdAndNs_gettersReturnIntegers() {
		$revision = $this->getMockBuilder( 'Queryr\DumpReader\Revision' )
			->disableOriginalConstructor()->getMock();

		$page = new Page( '42', 'Q42', '1', $revision );

		$this->assertSame( 42, $page->getId() );
		$this->assertSame( 1, $page->getNamespace() );
	}

}