<?php

namespace Tests\Wikibase\Dump;

use Wikibase\Dump\Page;

/**
 * @covers Wikibase\Dump\Page
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageTest extends \PHPUnit_Framework_TestCase {

	public function testDataIsRetained() {
		$id = '42';
		$title = 'Q42';
		$namespace = '0';
		$revision = $this->getMockBuilder( 'Wikibase\Dump\Revision' )
			->disableOriginalConstructor()->getMock();

		$page = new Page( $id, $title, $namespace, $revision );

		$this->assertEquals( $id, $page->getId() );
		$this->assertEquals( $title, $page->getTitle() );
		$this->assertEquals( $namespace, $page->getNamespace() );
		$this->assertEquals( $revision, $page->getRevision() );
	}

}