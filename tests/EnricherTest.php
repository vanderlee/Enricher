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
	 * @covers Enricher::addSelector
	 */
	public function testAddSelectorElement() {
		$this->object->addSelector('b');
        $this->object->addAttribute('title', 'ok');

		$this->assertEquals('<b title="ok"></b><i></i>', $this->object->enrich('<b></b><i></i>'));
	}

	/**
	 * @covers Enricher::addSelector
	 */
	public function testAddSelectorClass() {
		$this->object->addSelector('.a');
        $this->object->addAttribute('title', 'ok');

		$this->assertEquals('<b class="a" title="ok"></b><b class="b"></b>', $this->object->enrich('<b class="a"></b><b class="b"></b>'));
	}

	/**
	 * @covers Enricher::addSelector
	 */
	public function testAddSelectorId() {
		$this->object->addSelector('#a');
        $this->object->addAttribute('title', 'ok');

		$this->assertEquals('<b id="a" title="ok"></b><b id="b"></b>', $this->object->enrich('<b id="a"></b><b id="b"></b>'));
	}

	/**
	 * @covers Enricher::addSelector
	 */
	public function testAddSelectorAttribute() {
		$this->object->addSelector('[id]');
        $this->object->addAttribute('title', 'ok');

		$this->assertEquals('<b id="a" title="ok"></b><b class="a"></b>', $this->object->enrich('<b id="a"></b><b class="a"></b>'));
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

	/**
	 * @covers Enricher::addClass
	 */
	public function testAddClassWithoutPrevious() {
		$this->object->addSelector('p');
        $this->object->addClass('new');

		$this->assertEquals('<p class="new"></p>', $this->object->enrich('<p></p>'));
	}

	/**
	 * @covers Enricher::addClass
	 */
	public function testAddClassWithPrevious() {
		$this->object->addSelector('p');
        $this->object->addClass('new');

		$this->assertEquals('<p class="old new"></p>', $this->object->enrich('<p class="old"></p>'));
	}

	/**
	 * @covers Enricher::addClass
	 */
	public function testAddClassDuplicate() {
		$this->object->addSelector('p');
        $this->object->addClass('old');

		$this->assertEquals('<p class="old"></p>', $this->object->enrich('<p class="old"></p>'));
	}

	/**
	 * @covers Enricher::removeClass
	 */
	public function testRemoveClassWithoutPrevious() {
		$this->object->addSelector('p');
        $this->object->removeClass('old');

		$this->assertEquals('<p></p>', $this->object->enrich('<p></p>'));
	}

	/**
	 * @covers Enricher::removeClass
	 */
	public function testRemoveClassWithPrevious() {
		$this->object->addSelector('p');
        $this->object->removeClass('old');

		$this->assertEquals('<p></p>', $this->object->enrich('<p class="old"></p>'));
	}

	/**
	 * @covers Enricher::removeClass
	 */
	public function testRemoveClassWithUnrelated() {
		$this->object->addSelector('p');
        $this->object->removeClass('old');

		$this->assertEquals('<p class="untouchable"></p>', $this->object->enrich('<p class="untouchable"></p>'));
	}

	/**
	 * @covers Enricher::removeClass
	 * @covers Enricher::addClass
	 */
	public function testRemoveClassOverrulesAddClass() {
		$this->object->addSelector('p');
        $this->object->removeClass('old');
        $this->object->addClass('old');

		$this->assertEquals('<p></p>', $this->object->enrich('<p class="old"></p>'));
	}

}
