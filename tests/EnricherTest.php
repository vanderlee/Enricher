<?php

class EnricherTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Enricher
	 */
	protected $object;

	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new Enricher();
	}

	/**
	 * @covers Enricher::addAttribute
	 */
	public function testAddAttributeWithoutPrevious() {
		$this->object->addSelector('p');
        $this->object->addAttribute('title', 'yes');

		$this->assertEquals('<p title="yes"></p>', $this->object->enrich('<p></p>'));
	}

	/**
	 * @covers Enricher::addAttribute
	 */
	public function testAddAttributeWithPrevious() {
		$this->object->addSelector('p');
        $this->object->addAttribute('title', 'yes');

		$this->assertEquals('<p title="no"></p>', $this->object->enrich('<p title="no"></p>'));
	}

	/**
	 * @covers Enricher::addAttributes
	 */
	public function testAddAttributes() {
		$this->object->addSelector('p');
        $this->object->addAttributes(array(
            'lang'      => 'en',
            'tabindex'  => 3,
            'title'     => 'yes',
        ));

		$this->assertEquals('<p title="no" lang="en" tabindex="3"></p>', $this->object->enrich('<p title="no"></p>'));
	}

	/**
	 * @covers Enricher::addAttributes
	 */
	public function testAddAttributesDifferentOrder() {
		$this->object->addSelector('p');
        $this->object->addAttributes(array(
            'tabindex'  => 3,
            'lang'      => 'en',
            'title'     => 'yes',
        ));

		$this->assertEquals('<p title="no" tabindex="3" lang="en"></p>', $this->object->enrich('<p title="no"></p>'));
	}

	/**
	 * @covers Enricher::overwriteAttribute
	 */
	public function testOverwriteAttributeWithoutPrevious() {
		$this->object->addSelector('p');
        $this->object->overwriteAttribute('title', 'yes');

		$this->assertEquals('<p title="yes"></p>', $this->object->enrich('<p></p>'));
	}

	/**
	 * @covers Enricher::overwriteAttribute
	 */
	public function testOverwriteAttributeWithPrevious() {
		$this->object->addSelector('p');
        $this->object->overwriteAttribute('title', 'yes');

		$this->assertEquals('<p title="yes"></p>', $this->object->enrich('<p title="no"></p>'));
	}

}
