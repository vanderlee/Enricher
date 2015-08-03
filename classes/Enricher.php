<?php

/**
 * Enrich HTML with some attributes and styles
 */
class Enricher {
	private $selectors = '';
	private $backfill_attributes = array();
	private $backfill_styles = array();
	private $overwrite_attributes = array();
	private $overwrite_styles = array();
    private $add_classes = array();
    private $remove_classes = array();

    /**
     * Reset all configured options to default (empty)
     */
	public function reset() {
		$this->selectors = '';
		$this->backfill_attributes = array();
		$this->backfill_styles = array();
		$this->overwrite_attributes = array();
		$this->overwrite_styles = array();
        $this->add_classes = array();
        $this->remove_classes = array();
	}

    /**
     * Add a selector to specify which elements may be selected.
     * Select elements by specifying the tag. i.e. `div`.
     * Select by class by specifying the class. i.e. `div.blue`.
     * Select by id by specifying the id. i.e. `div#body`.
     * Select by attribute by specifying the attribute. i.e. `a[href]`.
     * Combine these to form selectors. i.e. `a[href][target].blue`.
     * You may specify multiple selectors by separating them using a comma (,).
     * @param string $selector
     */
	public function addSelector($selector) {
        if (empty($this->selectors)) {
            $this->selectors = $selector;
        } else {
            $this->selectors.= ',' . $selector;
        }
	}

    /**
     * Add selectors to specify which elements may be selected.
     * @param array $selectors
     */
    public function addSelectors($selectors) {
        foreach ($selectors as $selector) {
            $this->addSelector($selector);
        }
    }

    /**
     * Add an attribute to add to the selected elements if missing.
     * @param string $name
     * @param string $value
     */
	public function addAttribute($name, $value) {
		$this->backfill_attributes[$name] = $value;
	}

    /**
     * Add attributes to add to the selected elements if missing.
     * @param array $attributes Hashmap of name => value
     */
	public function addAttributes($attributes) {
		foreach ($attributes as $name => $value) {
			$this->addAttribute($name, $value);
		}
	}
	
    /**
     * Add an attribute to overwrite in the selected elements.
     * @param string $name
     * @param string $value
     */
    public function overwriteAttribute($name, $value = null) {
		$this->overwrite_attributes[$name] = $value;
	}
	
    /**
     * Add attributes to overwrite in the selected elements.
     * @param array $attributes Hashmap of name => value
     */
	public function overwriteAttributes($attributes) {
		foreach ($attributes as $name => $value) {
			$this->overwriteAttribute($name, $value);
		}
	}
	
    /**
     * Add an attribute to remove from the selected elements.
     * @param string $name
     */
	public function removeAttribute($name) {
		$this->overwriteAttribute($name);
	}

    /**
     * Add attributes to remove from the selected elements.
     * @param array $names Array of attribute names
     */
	public function removeAttributes($names) {
		foreach ($names as $name => $value) {
			$this->removeAttribute($name, $value);
		}
	}

    /**
     * Add style rule to add to the selected elements if missing.
     * @param string $name
     * @param string $value
     */
	public function addStyle($name, $value) {
		$this->backfill_styles[$name] = $value;
	}

    /**
     * Add style rules to add to the selected elements if missing.
     * @param array $styles Hashmap of name => value
     */
	public function addStyles($styles) {
		foreach ($styles as $name => $value) {
			$this->addStyle($name, $value);
		}
	}

    /**
     * Add a style rule to overwrite for the selected elements.
     * @param string $name the rulename
     * @param string $value
     */
	public function overwriteStyle($name, $value = null) {
		$this->overwrite_styles[$name] = $value;
	}

    /**
     * Add style rules to overwrite for the selected elements.
     * @param array $styles hashmap of name => value
     */
	public function overwriteStyles($styles) {
		foreach ($styles as $name => $value) {
			$this->overwriteStyle($name, $value);
		}
	}

    /**
     * Add a style rule to remove from the selected elements.
     * @param string $name
     */
	public function removeStyle($name) {
		$this->overwriteStyle($name);
	}

    /**
     * Add style rules to remove from the selected elements.
     * @param array $names array of rule names
     */
    public function removeStyles($names) {
		foreach ($names as $name) {
			$this->removeStyle($name);
		}
    }

    /**
     * Add a class to add to the selected elements.
     * @param string $class
     */
    public function addClass($class) {
        $this->add_classes[] = $class;
    }

    /**
     * Add classes to add to the selected elements.
     * @param array $classes
     */
    public function addClasses($classes) {
        foreach ($classes as $class) {
            $this->addClass($class);
        }
    }

    /**
     * Add a class to remove from the selected elements.
     * @param string $class
     */
    public function removeClass($class) {
        $this->remove_classes[] = $class;
    }

    /**
     * Add classes to remove from the selected elements.
     * @param array $classes
     */
    public function removeClasses($classes) {
        foreach ($classes as $class) {
            $this->removeClass($class);
        }
    }
	
	/**
	 * Enrich the selected elementes in the HTML according the specified
     * modifications for style, class and attributes.
	 * @param string $html
	 * @return string
	 */
	public function enrich($html) {
		if (empty($this->selectors)
				|| (empty($this->backfill_styles)
						&& empty($this->backfill_attributes)
						&& empty($this->overwrite_styles)
						&& empty($this->overwrite_attributes)
						&& empty($this->add_classes)
						&& empty($this->remove_classes))) {
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
			if (preg_match_all('~\.(-?[_a-z][_a-z0-9-]*)~i', $selector, $matches) > 0) {
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

            // No element; select any element
            $selector = empty($selector) ? '[^\s/>]+' : preg_quote($selector);

			// remainder = tag
			$pattern = '~(<'.$selector.'\b)([^/>]*)(/?>)~mis';
			$backfill_attributes = $this->backfill_attributes;
			$backfill_styles = $this->backfill_styles;
			$overwrite_attributes = $this->overwrite_attributes;
			$overwrite_styles = $this->overwrite_styles;
            $add_classes = $this->add_classes;
            $remove_classes = $this->remove_classes;

			$tag_cache = array();
			$html = preg_replace_callback($pattern, function($tag_match)
					use($has_id, $has_classes, $has_attributes,
						$backfill_attributes, $overwrite_attributes,
						$backfill_styles, $overwrite_styles,
                        $add_classes, $remove_classes,
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
				if ($has_id && (empty($attributes['id']) || $attributes['id'] !== $has_id)) {
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
					if (empty($attributes['class'])) {
						$tag_cache[$tag_match[0]] = $tag_match[0];
						return $tag_match[0];
					} else {
						$classes = preg_split('~\s+~', $attributes['class'], -1, PREG_SPLIT_NO_EMPTY);
                        $diff = array_diff($has_classes, $classes);
                        if (!empty($diff)) {
                            $tag_cache[$tag_match[0]] = $tag_match[0];
                            return $tag_match[0];
                        }
					}
				}

				// backfill, overwrite and delete empties
				$attributes = array_filter(array_merge($attributes + $backfill_attributes, $overwrite_attributes), 'strlen');

				// style (http://www.w3.org/TR/2011/REC-CSS2-20110607/grammar.html)
				if (!empty($backfill_styles) || !empty($overwrite_styles)) {
                    // get old
					$styles = array();
					if (isset($attributes['style'])) {				
						$declaration_matches = array();
						preg_match_all('~(-?[_a-z][-_a-z0-9]+)\s*:\s*([^;]*)~mis', $attributes['style'], $declaration_matches, PREG_SET_ORDER);
						foreach ($declaration_matches as $m) {
							$styles[strtolower($m[1])] = $m[2];
						}
					}

					// combine
					$styles = array_filter(array_merge($styles + $backfill_styles, $overwrite_styles), 'strlen');

                    // set new
                    if (empty($styles)) {
                        unset($attributes['style']);
                    } else {
                        $declarations = array();
                        foreach ($styles as $property => $expr) {
                            $declarations[] = $property.':'.$expr;
                        }
                        $attributes['style'] = join(';', $declarations);
                    }
				}		

                // class
                if (!empty($add_classes) || !empty($remove_classes)) {
                    // get old
                    if (!isset($classes)) {
                        $classes = isset($attributes['class']) ? preg_split('~\s+~', $attributes['class'], -1, PREG_SPLIT_NO_EMPTY) : array();
                    }
					
					// combine
                    if ($add_classes) {
                        $classes = array_unique(array_merge($classes, $add_classes));
                    }
                    if ($remove_classes) {
                        $classes = array_diff($classes, $remove_classes);
                    }
                    
                    // set new
                    if (empty($classes)) {
                        unset($attributes['class']);
                    } else {
                        $attributes['class'] = join(' ', $classes);
                    }
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