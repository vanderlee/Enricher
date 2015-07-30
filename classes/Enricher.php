<?php

//@todo testsuite phpunit

/**
 * Enrich HTML with some attributes and styles
 */
class Enricher {
	private $selectors = array();
	private $backfill_attributes = array();
	private $backfill_styles = array();
	private $overwrite_attributes = array();
	private $overwrite_styles = array();
	
	public function reset() {
		$this->selectors = array();
		$this->backfill_attributes = array();
		$this->backfill_styles = array();
		$this->overwrite_attributes = array();
		$this->overwrite_styles = array();		
	}
	
	public function addSelector($selector) {
		$this->selectors = $selector;
	}
	
	public function addAttribute($name, $value) {
		$this->backfill_attributes[$name] = $value;
	}
	
	public function addAttributes($attributes) {
		foreach ($attributes as $name => $value) {
			$this->addAttribute($name, $value);
		}
	}
	
	public function addStyle($name, $value) {
		$this->backfill_styles[$name] = $value;
	}
	
	public function overwriteAttribute($name, $value = null) {
		$this->overwrite_attributes[$name] = $value;
	}
	
	public function overwriteAttributes($attributes) {
		foreach ($attributes as $name => $value) {
			$this->overwriteAttribute($name, $value);
		}
	}
	
	public function removeAttribute($name) {
		$this->overwriteAttribute($name);
	}
	
	public function overwriteStyle($name, $value = null) {
		$this->overwrite_styles[$name] = $value;
	}
	
	public function removeStyle($name) {
		$this->overwriteStyle($name);
	}	

	public function addStyles($styles) {
		foreach ($styles as $name => $value) {
			$this->addStyle($name, $value);
		}
	}
	
	public function overwriteStyles($styles) {
		foreach ($styles as $name => $value) {
			$this->overwriteStyle($name, $value);
		}
	}	
	
	/**
	 * Enrich
	 * @param string $html
	 * @return string
	 */
	public function enrich($html) {
		if (empty($this->selectors)
				|| (empty($this->backfill_styles)
						&& empty($this->backfill_attributes)
						&& empty($this->overwrite_styles)
						&& empty($this->overwrite_attributes))) {
			return $html;
		}
		
		foreach (preg_split('~\s*,\s*~', $this->selectors) as $selector) {	
			// id
			$has_id = null;
			$matches = array();
			if (preg_match('~#([-_a-z0-9]+)~i', $selector, $match) === 1) {
				$has_id = $match[1];
				$selector = str_replace($match[0], '', $selector);
			}

			// classes
			$has_classes = array();
			$matches = array();
			if (preg_match_all('~\.(-?[_a-z][-_a-z0-9]+)~i', $selector, $matches) > 0) {
				$has_classes = $matches[1];
				$selector = str_replace($matches[0], '', $selector);
			}

			// has-attributes
			$has_attributes = array();
			$matches = array();
			if (preg_match_all('~\[([^\]]+)\]~', $selector, $matches) > 0) {
				$has_attributes = array_map('strtolower', $matches[1]);
				$selector = str_replace($matches[0], '', $selector);
			}

			// remainder = tag
			$pattern = '~(<'.preg_quote($selector).'\b)([^>]*)(>)~mis';	

			$backfill_attributes = $this->backfill_attributes;
			$backfill_styles = $this->backfill_styles;
			$overwrite_attributes = $this->overwrite_attributes;
			$overwrite_styles = $this->overwrite_styles;

			$tag_cache = array();
			$html = preg_replace_callback($pattern, function($tag_match)
					use($has_id, $has_classes, $has_attributes,
						$backfill_attributes, $overwrite_attributes,
						$backfill_styles, $overwrite_styles,
						&$tag_cache) {	
				if (isset($tag_cache[$tag_match[0]])) {
					return $tag_cache[$tag_match[0]];
				}

				// extract all attributes
				$attribute_matches = array();
				preg_match_all('~([a-z]+)\\s*=\\s*(["\'])(.*?)\\g{2}~mis', $tag_match[2], $attribute_matches, PREG_SET_ORDER);
				$attributes = array();
				foreach ($attribute_matches as $m) {
					$attributes[strtolower($m[1])] = strpos($m[3], '&') === false ? $m[3] : html_entity_decode($m[3]);
				}

				// has id
				if ($has_id && (empty($attribute['id']) || $attribute['id'] !== $has_id)) {
					$tag_cache[$tag_match[0]] = $tag_match[0];
					return $tag_match[0];
				}

				// has attributes
				if (!empty($has_attributes)) {
					foreach ($has_attributes as $has_attribute) {
						if (!isset($attributes[$has_attribute])) {
							$tag_cache[$tag_match[0]] = $tag_match[0];
							return $tag_match[0];
						}
					}
				}

				// has classes
				if (!empty($has_classes)) {
					if (empty($attribute['class'])) {
						$tag_cache[$tag_match[0]] = $tag_match[0];
						return $tag_match[0];
					} else {
						$classes = preg_split('\s+', $attribute['class'], PREG_SPLIT_NO_EMPTY);
						foreach ($has_classes as $has_class) {
							if (!in_array($has_class, $classes)) {
								$tag_cache[$tag_match[0]] = $tag_match[0];
								return $tag_match[0];
							}
						}
					}
				}

				// backfill, overwrite and delete empties
				$attributes = array_filter(array_merge($attributes + $backfill_attributes, $overwrite_attributes), 'strlen');
				
				// style (http://www.w3.org/TR/2011/REC-CSS2-20110607/grammar.html)
				if (!empty($backfill_styles) || !empty($overwrite_styles)) {
					$styles = array();
					if (isset($attributes['style'])) {				
						$declaration_matches = array();
						preg_match_all('~(-?[_a-z][-_a-z0-9]+)\s*:\s*([^;]*)~mis', $attributes['style'], $declaration_matches, PREG_SET_ORDER);
						foreach ($declaration_matches as $m) {
							$styles[strtolower($m[1])] = $m[2];
						}
					}

					// backfill, overwrite and delete empties
					$styles = array_filter(array_merge($styles + $backfill_styles, $overwrite_styles), 'strlen');
				
					// put the styles back together again
					$declarations = array();
					foreach ($styles as $property => $expr) {
						$declarations[] = $property.':'.$expr;
					}
					$attributes['style'] = join(';', $declarations);			
				}		

				// put the attributes back together again
				$tag_match[2] = '';
				foreach ($attributes as $name => $value) {
					$tag_match[2].= ' '.$name.'="'.htmlentities($value).'"';
				}

				$tag_cache[$tag_match[0]] = $tag_match[1].$tag_match[2].$tag_match[3];
				return $tag_cache[$tag_match[0]];
			}, $html);
		}

		return $html;
	}	
}