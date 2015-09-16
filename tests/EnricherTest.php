<?php

class EnricherTest extends PHPUnit_Framework_TestCase {

    const LF = "\n";

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
	public function testAddSelectorElementEmptyXml() {
		$this->object->addSelector('hr');
        $this->object->addAttribute('title', 'ok');

		$this->assertEquals('<hr title="ok"/>', $this->object->enrich('<hr/>'));
	}

	/**
	 * @covers Enricher::addSelector
	 */
	public function testAddSelectorElementEmptyHtml() {
		$this->object->addSelector('hr');
        $this->object->addAttribute('title', 'ok');

		$this->assertEquals('<hr title="ok">', $this->object->enrich('<hr>'));
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
	 * @covers Enricher::addSelector
	 */
	public function testAddSelectorSlash() {
		$this->object->addSelector('a');
        $this->object->addAttribute('title', 'ok');

		$this->assertEquals('<a href="http://example.com" title="ok">Link</a>', $this->object->enrich('<a href="http://example.com">Link</a>'));
	}

	/**
	 * @covers Enricher::addSelectors
	 */
	public function testAddSelectors() {
		$this->object->addSelectors(array('b', 'i'));
        $this->object->addAttribute('title', 'yes');

		$this->assertEquals('<b title="yes"></b><p></p><i title="yes"></i>', $this->object->enrich('<b></b><p></p><i></i>'));
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

	/**
	 * @covers Enricher::overwriteAttributes
	 */
	public function testOverwriteAttributes() {
		$this->object->addSelector('p');
        $this->object->overwriteAttributes(array(
			'title'	=> 'yes',
			'lang'	=> 'nl',
		));

		$this->assertEquals('<p title="yes" lang="nl"></p>', $this->object->enrich('<p title="no" lang="en"></p>'));
	}

	/**
	 * @covers Enricher::removeAttribute
	 */
	public function testRemoveAttributeWithoutPrevious() {
		$this->object->addSelector('p');
        $this->object->removeAttribute('title');

		$this->assertEquals('<p></p>', $this->object->enrich('<p></p>'));
	}

	/**
	 * @covers Enricher::removeAttribute
	 */
	public function testRemoveAttributeWithPrevious() {
		$this->object->addSelector('p');
        $this->object->removeAttribute('title');

		$this->assertEquals('<p></p>', $this->object->enrich('<p title="no"></p>'));
	}

	/**
	 * @covers Enricher::addStyle
	 */
	public function testAddStyleWithoutPrevious() {
		$this->object->addSelector('p');
        $this->object->addStyle('color', 'blue');

		$this->assertEquals('<p style="color:blue"></p>', $this->object->enrich('<p></p>'));
	}

	/**
	 * @covers Enricher::addStyle
	 */
	public function testAddStyleWithPrevious() {
		$this->object->addSelector('p');
        $this->object->addStyle('color', 'blue');

		$this->assertEquals('<p style="color:red"></p>', $this->object->enrich('<p style="color:red"></p>'));
	}

	/**
	 * @covers Enricher::addStyle
	 */
	public function testAddStyleWithPreviousWithOther() {
		$this->object->addSelector('p');
        $this->object->addStyle('color', 'blue');

		$this->assertEquals('<p style="background:red;color:blue"></p>', $this->object->enrich('<p style="background:red"></p>'));
	}

	/**
	 * @covers Enricher::addStyle
	 */
	public function testAddStyleWithPreviousWithWhitespacing() {
		$this->object->addSelector('p');
        $this->object->addStyle('color', 'blue');

		$this->assertEquals('<p style="background:red;color:blue"></p>', $this->object->enrich('<p style="background    :'.self::LF.'red"></p>'));
	}

	/**
	 * @covers Enricher::overwriteStyle
	 */
	public function testOverwriteStyleWithoutPrevious() {
		$this->object->addSelector('p');
        $this->object->overwriteStyle('color', 'blue');

		$this->assertEquals('<p style="color:blue"></p>', $this->object->enrich('<p></p>'));
	}

	/**
	 * @covers Enricher::overwriteStyle
	 */
	public function testOverwriteStyleWithPrevious() {
		$this->object->addSelector('p');
        $this->object->overwriteStyle('color', 'blue');

		$this->assertEquals('<p style="color:blue"></p>', $this->object->enrich('<p style="color:red"></p>'));
	}

	/**
	 * @covers Enricher::overwriteStyles
	 */
	public function testOverwriteStyles() {
		$this->object->addSelector('p');
        $this->object->overwriteStyles(array(
			'color'	=> 'blue',
			'float'	=> 'left',
		));

		$this->assertEquals('<p style="float:left;color:blue"></p>', $this->object->enrich('<p style="float:right;color:red"></p>'));
	}

	/**
	 * @covers Enricher::removeStyle
	 */
	public function testRemoveStyleWithoutPrevious() {
		$this->object->addSelector('p');
        $this->object->removeStyle('color');

		$this->assertEquals('<p></p>', $this->object->enrich('<p></p>'));
	}

	/**
	 * @covers Enricher::removeStyle
	 */
	public function testRemoveStyleWithPrevious() {
		$this->object->addSelector('p');
        $this->object->removeStyle('color', 'blue');

		$this->assertEquals('<p></p>', $this->object->enrich('<p style="color:red"></p>'));
	}

	/**
	 * @covers Enricher::removeStyle
	 */
	public function testRemoveStyleWithOther() {
		$this->object->addSelector('p');
        $this->object->removeStyle('color');

		$this->assertEquals('<p style="background:red"></p>', $this->object->enrich('<p style="background:red;color:green"></p>'));
	}

	/**
	 * @covers Enricher::removeStyles
	 */
	public function testRemoveStyles() {
		$this->object->addSelector('p');
        $this->object->removeStyles(array('color', 'float'));

		$this->assertEquals('<p style="background:red"></p>', $this->object->enrich('<p style="float:left;background:red;color:green"></p>'));
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
	 * @covers Enricher::addClasses
	 */
	public function testAddClasses() {
		$this->object->addSelector('p');
        $this->object->addClasses(array('a', 'b'));

		$this->assertEquals('<p class="c a b"></p>', $this->object->enrich('<p class="c"></p>'));
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

	/**
	 * @covers Enricher::removeClasses
	 */
	public function testRemoveClasses() {
		$this->object->addSelector('p');
        $this->object->removeClasses(array('a', 'b'));

		$this->assertEquals('<p class="c"></p>', $this->object->enrich('<p class="a b c"></p>'));
	}

	/**
	 * @covers Enricher::reset
	 */
	public function testReset() {
		$this->object->addSelector('b');
        $this->object->addClass('new');
        $this->object->addAttribute('title', 'yes');
        $this->object->addStyle('title', 'yes');
		$this->object->reset();
		$this->object->addSelector('i');
        $this->object->addClass('reset');

		$this->assertEquals('<b></b><i class="reset"></i>', $this->object->enrich('<b></b><i></i>'));
	}

}
