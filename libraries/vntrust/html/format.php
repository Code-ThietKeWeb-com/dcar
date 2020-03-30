<?php

if (! defined('IN_vnT'))
{
  die('Hacking attempt!');
}
/**
 * Main Wordpress Formatting API.
 *
 * Handles many functions for formatting output.
 * @version : 1.0
 * @date upgrade : 09/01/2009 by Thai Son
 */

class vnT_Format
{
	
	/**
	 * Accepts matches array from preg_replace_callback in autop() or a string.
	 *
	 * Ensures that the contents of a <<pre>>...<</pre>> HTML block are not
	 * converted into paragraphs or line-breaks.
	 *
	 * @since 1.2.0
	 *
	 * @param array|string $matches The array or string
	 * @return string The pre block without paragraph/line-break conversion.
	 */
	function clean_pre($matches) {
		if ( is_array($matches) )
			$text = $matches[1] . $matches[2] . "</pre>";
		else
			$text = $matches;
	
		$text = str_replace('<br />', '', $text);
		$text = str_replace('<p>', "\n", $text);
		$text = str_replace('</p>', '', $text);
	
		return $text;
	}
	
	/**
	 * Replaces double line-breaks with paragraph elements.
	 *
	 * A group of regex replaces used to identify text formatted with newlines and
	 * replace double line-breaks with HTML paragraph tags. The remaining
	 * line-breaks after conversion become <<br />> tags, unless $br is set to '0'
	 * or 'false'.
	 *
	 * @since 0.71
	 *
	 * @param string $pee The text which has to be formatted.
	 * @param int|bool $br Optional. If set, this will convert all remaining line-breaks after paragraphing. Default true.
	 * @return string Text which has been converted into correct paragraph tags.
	 */
	function autop($pee, $br = 1) {
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
		// Space things out a little
		$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr)';
		$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
		$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
		$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
		if ( strpos($pee, '<object') !== false ) {
			$pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
			$pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
		}
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
		// make paragraphs, including one at the end
		$pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
		$pee = '';
		foreach ( $pees as $tinkle )
			$pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
		$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
		$pee = preg_replace( '|<p>|', "$1<p>", $pee );
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
		if ($br) {
			$pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'), $pee);
			$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
			$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
		}
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
		if (strpos($pee, '<pre') !== false)
			$pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'clean_pre', $pee );
		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
		$pee = preg_replace('/<p>\s*?(' . get_shortcode_regex() . ')\s*<\/p>/s', '$1', $pee); // don't auto-p wrap shortcodes that stand alone
	
		return $pee;
	}
	
	/**
	 * Checks to see if a string is utf8 encoded.
	 *
	 * @author bmorel at ssi dot fr
	 *
	 * @since 1.2.1
	 *
	 * @param string $Str The string to be checked
	 * @return bool True if $Str fits a UTF-8 model, false otherwise.
	 */
	function seems_utf8($Str) { # by bmorel at ssi dot fr
		$length = strlen($Str);
		for ($i=0; $i < $length; $i++) {
			if (ord($Str[$i]) < 0x80) continue; # 0bbbbbbb
			elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($Str[$i]) & 0xC0) != 0x80))
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Converts a number of special characters into their HTML entities.
	 *
	 * Specifically deals with: &, <, >, ", and '.
	 *
	 * $quote_style can be set to ENT_COMPAT to encode " to
	 * &quot;, or ENT_QUOTES to do both. Default is ENT_NOQUOTES where no quotes are encoded.
	 *
	 * @since 1.2.2
	 *
	 * @param string $string The text which is to be encoded.
	 * @param mixed $quote_style Optional. Converts double quotes if set to ENT_COMPAT, both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES. Also compatible with old values; converting single quotes if set to 'single', double if set to 'double' or both if otherwise set. Default is ENT_NOQUOTES.
	 * @param string $charset Optional. The character encoding of the string. Default is false.
	 * @param boolean $double_encode Optional. Whether or not to encode existing html entities. Default is false.
	 * @return string The encoded text with HTML entities.
	 */
	function specialchars( $string, $quote_style = ENT_NOQUOTES, $charset = "UTF-8", $double_encode = false )
	{
		$string = (string) $string;
	
		if ( 0 === strlen( $string ) ) {
			return '';
		}
	
		// Don't bother if there are no specialchars - saves some processing
		if ( !preg_match( '/[&<>"\']/', $string ) ) {
			return $string;
		}
	
		// Account for the previous behaviour of the function when the $quote_style is not an accepted value
		if ( empty( $quote_style ) ) {
			$quote_style = ENT_NOQUOTES;
		} elseif ( !in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) ) {
			$quote_style = ENT_QUOTES;
		}
	

		$_quote_style = $quote_style;
	
		if ( $quote_style === 'double' ) {
			$quote_style = ENT_COMPAT;
			$_quote_style = ENT_COMPAT;
		} elseif ( $quote_style === 'single' ) {
			$quote_style = ENT_NOQUOTES;
		}
	
		// Handle double encoding ourselves
		if ( !$double_encode ) {
			$string = $this->specialchars_decode( $string, $_quote_style );
			$string = preg_replace( '/&(#?x?[0-9]+|[a-z]+);/i', '|wp_entity|$1|/wp_entity|', $string );
		}
	
		$string = @htmlspecialchars( $string, $quote_style, $charset );
	
		// Handle double encoding ourselves
		if ( !$double_encode ) {
			$string = str_replace( array( '|wp_entity|', '|/wp_entity|' ), array( '&', ';' ), $string );
		}
	
		// Backwards compatibility
		if ( 'single' === $_quote_style ) {
			$string = str_replace( "'", '&#039;', $string );
		}
	
		return $string;
	}
	
	/**
	 * Converts a number of HTML entities into their special characters.
	 *
	 * Specifically deals with: &, <, >, ", and '.
	 *
	 * $quote_style can be set to ENT_COMPAT to decode " entities,
	 * or ENT_QUOTES to do both " and '. Default is ENT_NOQUOTES where no quotes are decoded.
	 *
	 * @since 2.8
	 *
	 * @param string $string The text which is to be decoded.
	 * @param mixed $quote_style Optional. Converts double quotes if set to ENT_COMPAT, both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES. Also compatible with old specialchars() values; converting single quotes if set to 'single', double if set to 'double' or both if otherwise set. Default is ENT_NOQUOTES.
	 * @return string The decoded text without HTML entities.
	 */
	function specialchars_decode( $string, $quote_style = ENT_NOQUOTES )
	{
		$string = (string) $string;
	
		if ( 0 === strlen( $string ) ) {
			return '';
		}
	
		// Don't bother if there are no entities - saves a lot of processing
		if ( strpos( $string, '&' ) === false ) {
			return $string;
		}
	
		// Match the previous behaviour of specialchars() when the $quote_style is not an accepted value
		if ( empty( $quote_style ) ) {
			$quote_style = ENT_NOQUOTES;
		} elseif ( !in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) ) {
			$quote_style = ENT_QUOTES;
		}
	
		// More complete than get_html_translation_table( HTML_SPECIALCHARS )
		$single = array( '&#039;'  => '\'', '&#x27;' => '\'' );
		$single_preg = array( '/&#0*39;/'  => '&#039;', '/&#x0*27;/i' => '&#x27;' );
		$double = array( '&quot;' => '"', '&#034;'  => '"', '&#x22;' => '"' );
		$double_preg = array( '/&#0*34;/'  => '&#034;', '/&#x0*22;/i' => '&#x22;' );
		$others = array( '&lt;'   => '<', '&#060;'  => '<', '&gt;'   => '>', '&#062;'  => '>', '&amp;'  => '&', '&#038;'  => '&', '&#x26;' => '&' );
		$others_preg = array( '/&#0*60;/'  => '&#060;', '/&#0*62;/'  => '&#062;', '/&#0*38;/'  => '&#038;', '/&#x0*26;/i' => '&#x26;' );
	
		if ( $quote_style === ENT_QUOTES ) {
			$translation = array_merge( $single, $double, $others );
			$translation_preg = array_merge( $single_preg, $double_preg, $others_preg );
		} elseif ( $quote_style === ENT_COMPAT || $quote_style === 'double' ) {
			$translation = array_merge( $double, $others );
			$translation_preg = array_merge( $double_preg, $others_preg );
		} elseif ( $quote_style === 'single' ) {
			$translation = array_merge( $single, $others );
			$translation_preg = array_merge( $single_preg, $others_preg );
		} elseif ( $quote_style === ENT_NOQUOTES ) {
			$translation = $others;
			$translation_preg = $others_preg;
		}
	
		// Remove zero padding on numeric entities
		$string = preg_replace( array_keys( $translation_preg ), array_values( $translation_preg ), $string );
	
		// Replace characters according to translation table
		return strtr( $string, $translation );
	}
	
	/**
	 * Checks for invalid UTF8 in a string.
	 *
	 * @since 2.8
	 *
	 * @param string $string The text which is to be checked.
	 * @param boolean $strip Optional. Whether to attempt to strip out invalid UTF8. Default is false.
	 * @return string The checked text.
	 */
	function check_invalid_utf8( $string, $strip = false )
	{
		global $vnT,$conf ;
		$string = (string) $string;
	
		if ( 0 === strlen( $string ) ) {
			return '';
		}
	
		// Store the site charset as a static to avoid multiple calls to get_option()
		static $is_utf8;
		if ( !isset( $is_utf8 ) ) {
			$is_utf8 = in_array( $conf['charset'], array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) );
		}
		if ( !$is_utf8 ) {
			return $string;
		}
	
		// Check for support for utf8 in the installed PCRE library once and store the result in a static
		static $utf8_pcre;
		if ( !isset( $utf8_pcre ) ) {
			$utf8_pcre = @preg_match( '/^./u', 'a' );
		}
		// We can't demand utf8 in the PCRE installation, so just return the string in those cases
		if ( !$utf8_pcre ) {
			return $string;
		}
	
		// preg_match fails when it encounters invalid UTF8 in $string
		if ( 1 === @preg_match( '/^./us', $string ) ) {
			return $string;
		}
	
		// Attempt to strip the bad chars if requested (not recommended)
		if ( $strip && function_exists( 'iconv' ) ) {
			return iconv( 'utf-8', 'utf-8', $string );
		}
	
		return '';
	}
	
	/**
	 * Encode the Unicode values to be used in the URI.
	 *
	 * @since 1.5.0
	 *
	 * @param string $utf8_string
	 * @param int $length Max length of the string
	 * @return string String with Unicode encoded for URI.
	 */
	function utf8_uri_encode( $utf8_string, $length = 0 ) {
		$unicode = '';
		$values = array();
		$num_octets = 1;
		$unicode_length = 0;
	
		$string_length = strlen( $utf8_string );
		for ($i = 0; $i < $string_length; $i++ ) {
	
			$value = ord( $utf8_string[ $i ] );
	
			if ( $value < 128 ) {
				if ( $length && ( $unicode_length >= $length ) )
					break;
				$unicode .= chr($value);
				$unicode_length++;
			} else {
				if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;
	
				$values[] = $value;
	
				if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
					break;
				if ( count( $values ) == $num_octets ) {
					if ($num_octets == 3) {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
						$unicode_length += 9;
					} else {
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
						$unicode_length += 6;
					}
	
					$values = array();
					$num_octets = 1;
				}
			}
		}
	
		return $unicode;
	}
	
	/**
	 * Converts all accent characters to ASCII characters.
	 *
	 * If there are no accent characters, then the string given is just returned.
	 *
	 * @since 1.2.1
	 *
	 * @param string $string Text that might have accent characters
	 * @return string Filtered string with replaced "nice" characters.
	 */
	function remove_accents($string) {
		if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;
	
		if ($this->seems_utf8($string)) {
			$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '');
	
			$string = strtr($string, $chars);
		} else {
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				.chr(252).chr(253).chr(255);
	
			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
	
			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}
	
		return $string;
	}
	
	/**
	 * Filters certain characters from the file name.
	 *
	 * Turns all strings to lowercase removing most characters except alphanumeric
	 * with spaces, dashes and periods. All spaces and underscores are converted to
	 * dashes. Multiple dashes are converted to a single dash. Finally, if the file
	 * name ends with a dash, it is removed.
	 *
	 * @since 2.1.0
	 *
	 * @param string $name The file name
	 * @return string Sanitized file name
	 */
	function sanitize_file_name( $name ) { // Like sanitize_title, but with periods
		$name = strtolower( $name );
		$name = preg_replace('/&.+?;/', '', $name); // kill entities
		$name = str_replace( '_', '-', $name );
		$name = preg_replace('/[^a-z0-9\s-.]/', '', $name);
		$name = preg_replace('/\s+/', '-', $name);
		$name = preg_replace('|-+|', '-', $name);
		$name = trim($name, '-');
		return $name;
	}
	
	/**
	 * Sanitize username stripping out unsafe characters.
	 *
	 * If $strict is true, only alphanumeric characters (as well as _, space, ., -,
	 * @) are returned.
	 * Removes tags, octets, entities, and if strict is enabled, will remove all
	 * non-ASCII characters. After sanitizing, it passes the username, raw username
	 * (the username in the parameter), and the strict parameter as parameters for
	 * the filter.
	 *
	 * @since 2.0.0
	 * @uses apply_filters() Calls 'sanitize_user' hook on username, raw username,
	 *		and $strict parameter.
	 *
	 * @param string $username The username to be sanitized.
	 * @param bool $strict If set limits $username to specific characters. Default false.
	 * @return string The sanitized username, after passing through filters.
	 */
	function sanitize_user( $username, $strict = false ) {
		$raw_username = $username;
		$username = strip_tags($username);
		// Kill octets
		$username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
		$username = preg_replace('/&.+?;/', '', $username); // Kill entities
	
		// If strict, reduce to ASCII for max portability.
		if ( $strict )
			$username = preg_replace('|[^a-z0-9 _.\-@]|i', '', $username);
	
		// Consolidate contiguous whitespace
		$username = preg_replace('|\s+|', ' ', $username);
	
		return $username;
	}
	
	
	/**
	 * Sanitizes title, replacing whitespace with dashes.
	 *
	 * Limits the output to alphanumeric characters, underscore (_) and dash (-).
	 * Whitespace becomes a dash.
	 *
	 * @since 1.2.0
	 *
	 * @param string $title The title to be sanitized.
	 * @return string The sanitized title.
	 */
	function sanitize_title_with_dashes($title) {
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
	
		$title = $this->remove_accents($title);
		if ($this->seems_utf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
			}
			$title = $this->utf8_uri_encode($title, 200);
		}
	
		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');
	
		return $title;
	}
	
	/**
	 * Ensures a string is a valid SQL order by clause.
	 *
	 * Accepts one or more columns, with or without ASC/DESC, and also accepts
	 * RAND().
	 *
	 * @since 2.5.1
	 *
	 * @param string $orderby Order by string to be checked.
	 * @return string|false Returns the order by clause if it is a match, false otherwise.
	 */
	function sanitize_sql_orderby( $orderby ){
		preg_match('/^\s*([a-z0-9_]+(\s+(ASC|DESC))?(\s*,\s*|\s*$))+|^\s*RAND\(\s*\)\s*$/i', $orderby, $obmatches);
		if ( !$obmatches )
			return false;
		return $orderby;
	}
	
	/**
	 * Converts a number of characters from a string.
	 *
	 * Metadata tags <<title>> and <<category>> are removed, <<br>> and <<hr>> are
	 * converted into correct XHTML and Unicode characters are converted to the
	 * valid range.
	 *
	 * @since 0.71
	 *
	 * @param string $content String of characters to be converted.
	 * @param string $deprecated Not used.
	 * @return string Converted string.
	 */
	function convert_chars($content, $deprecated = '') {
		// Translation of invalid Unicode references range to valid range
		$wp_htmltranswinuni = array(
		'&#128;' => '&#8364;', // the Euro sign
		'&#129;' => '',
		'&#130;' => '&#8218;', // these are Windows CP1252 specific characters
		'&#131;' => '&#402;',  // they would look weird on non-Windows browsers
		'&#132;' => '&#8222;',
		'&#133;' => '&#8230;',
		'&#134;' => '&#8224;',
		'&#135;' => '&#8225;',
		'&#136;' => '&#710;',
		'&#137;' => '&#8240;',
		'&#138;' => '&#352;',
		'&#139;' => '&#8249;',
		'&#140;' => '&#338;',
		'&#141;' => '',
		'&#142;' => '&#382;',
		'&#143;' => '',
		'&#144;' => '',
		'&#145;' => '&#8216;',
		'&#146;' => '&#8217;',
		'&#147;' => '&#8220;',
		'&#148;' => '&#8221;',
		'&#149;' => '&#8226;',
		'&#150;' => '&#8211;',
		'&#151;' => '&#8212;',
		'&#152;' => '&#732;',
		'&#153;' => '&#8482;',
		'&#154;' => '&#353;',
		'&#155;' => '&#8250;',
		'&#156;' => '&#339;',
		'&#157;' => '',
		'&#158;' => '',
		'&#159;' => '&#376;'
		);
	
		// Remove metadata tags
		$content = preg_replace('/<title>(.+?)<\/title>/','',$content);
		$content = preg_replace('/<category>(.+?)<\/category>/','',$content);
	
		// Converts lone & characters into &#38; (a.k.a. &amp;)
		$content = preg_replace('/&([^#])(?![a-z1-4]{1,8};)/i', '&#038;$1', $content);
	
		// Fix Word pasting
		$content = strtr($content, $wp_htmltranswinuni);
	
		// Just a little XHTML help
		$content = str_replace('<br>', '<br />', $content);
		$content = str_replace('<hr>', '<hr />', $content);
	
		return $content;
	}
	
	/**
	 * Fixes javascript bugs in browsers.
	 *
	 * Converts unicode characters to HTML numbered entities.
	 *
	 * @since 1.5.0
	 * @uses $is_macIE
	 * @uses $is_winIE
	 *
	 * @param string $text Text to be made safe.
	 * @return string Fixed text.
	 */
	function funky_javascript_fix($text) {
		// Fixes for browsers' javascript bugs
		global $is_macIE, $is_winIE;
	
		/** @todo use preg_replace_callback() instead */
		if ( $is_winIE || $is_macIE )
			$text =  preg_replace("/\%u([0-9A-F]{4,4})/e",  "'&#'.base_convert('\\1',16,10).';'", $text);
	
		return $text;
	}
	
	/**
	 * Will only balance the tags if forced to and the option is set to balance tags.
	 *
	 * The option 'use_balanceTags' is used for whether the tags will be balanced.
	 * Both the $force parameter and 'use_balanceTags' option will have to be true
	 * before the tags will be balanced.
	 *
	 * @since 0.71
	 *
	 * @param string $text Text to be balanced
	 * @param bool $force Forces balancing, ignoring the value of the option. Default false.
	 * @return string Balanced text
	 */
	function balanceTags( $text, $force = false ) {
		if ( !$force && get_option('use_balanceTags') == 0 )
			return $text;
		return force_balance_tags( $text );
	}
	
	/**
	 * Balances tags of string using a modified stack.
	 *
	 * @since 2.0.4
	 *
	 * @author Leonard Lin <leonard@acm.org>
	 * @license GPL v2.0
	 * @copyright November 4, 2001
	 * @version 1.1
	 * @todo Make better - change loop condition to $text in 1.2
	 * @internal Modified by Scott Reilly (coffee2code) 02 Aug 2004
	 *		1.1  Fixed handling of append/stack pop order of end text
	 *			 Added Cleaning Hooks
	 *		1.0  First Version
	 *
	 * @param string $text Text to be balanced.
	 * @return string Balanced text.
	 */
	function force_balance_tags( $text ) {
		$tagstack = array(); $stacksize = 0; $tagqueue = ''; $newtext = '';
		$single_tags = array('br', 'hr', 'img', 'input'); //Known single-entity/self-closing tags
		$nestable_tags = array('blockquote', 'div', 'span'); //Tags that can be immediately nested within themselves
	
		# WP bug fix for comments - in case you REALLY meant to type '< !--'
		$text = str_replace('< !--', '<    !--', $text);
		# WP bug fix for LOVE <3 (and other situations with '<' before a number)
		$text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);
	
		while (preg_match("/<(\/?\w*)\s*([^>]*)>/",$text,$regex)) {
			$newtext .= $tagqueue;
	
			$i = strpos($text,$regex[0]);
			$l = strlen($regex[0]);
	
			// clear the shifter
			$tagqueue = '';
			// Pop or Push
			if ($regex[1][0] == "/") { // End Tag
				$tag = strtolower(substr($regex[1],1));
				// if too many closing tags
				if($stacksize <= 0) {
					$tag = '';
					//or close to be safe $tag = '/' . $tag;
				}
				// if stacktop value = tag close value then pop
				else if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
					$tag = '</' . $tag . '>'; // Close Tag
					// Pop
					array_pop ($tagstack);
					$stacksize--;
				} else { // closing tag not at top, search for it
					for ($j=$stacksize-1;$j>=0;$j--) {
						if ($tagstack[$j] == $tag) {
						// add tag to tagqueue
							for ($k=$stacksize-1;$k>=$j;$k--){
								$tagqueue .= '</' . array_pop ($tagstack) . '>';
								$stacksize--;
							}
							break;
						}
					}
					$tag = '';
				}
			} else { // Begin Tag
				$tag = strtolower($regex[1]);
	
				// Tag Cleaning
	
				// If self-closing or '', don't do anything.
				if((substr($regex[2],-1) == '/') || ($tag == '')) {
				}
				// ElseIf it's a known single-entity tag but it doesn't close itself, do so
				elseif ( in_array($tag, $single_tags) ) {
					$regex[2] .= '/';
				} else {	// Push the tag onto the stack
					// If the top of the stack is the same as the tag we want to push, close previous tag
					if (($stacksize > 0) && !in_array($tag, $nestable_tags) && ($tagstack[$stacksize - 1] == $tag)) {
						$tagqueue = '</' . array_pop ($tagstack) . '>';
						$stacksize--;
					}
					$stacksize = array_push ($tagstack, $tag);
				}
	
				// Attributes
				$attributes = $regex[2];
				if($attributes) {
					$attributes = ' '.$attributes;
				}
				$tag = '<'.$tag.$attributes.'>';
				//If already queuing a close tag, then put this tag on, too
				if ($tagqueue) {
					$tagqueue .= $tag;
					$tag = '';
				}
			}
			$newtext .= substr($text,0,$i) . $tag;
			$text = substr($text,$i+$l);
		}
	
		// Clear Tag Queue
		$newtext .= $tagqueue;
	
		// Add Remaining text
		$newtext .= $text;
	
		// Empty Stack
		while($x = array_pop($tagstack)) {
			$newtext .= '</' . $x . '>'; // Add remaining tags to close
		}
	
		// WP fix for the bug with HTML comments
		$newtext = str_replace("< !--","<!--",$newtext);
		$newtext = str_replace("<    !--","< !--",$newtext);
	
		return $newtext;
	}
	


	
	/**
	 * Add leading zeros when necessary.
	 *
	 * If you set the threshold to '4' and the number is '10', then you will get
	 * back '0010'. If you set the number to '4' and the number is '5000', then you
	 * will get back '5000'.
	 *
	 * Uses sprintf to append the amount of zeros based on the $threshold parameter
	 * and the size of the number. If the number is large enough, then no zeros will
	 * be appended.
	 *
	 * @since 0.71
	 *
	 * @param mixed $number Number to append zeros to if not greater than threshold.
	 * @param int $threshold Digit places number needs to be to not have zeros added.
	 * @return string Adds leading zeros to number if needed.
	 */
	function zeroise($number, $threshold) {
		return sprintf('%0'.$threshold.'s', $number);
	}
	
	/**
	 * Adds backslashes before letters and before a number at the start of a string.
	 *
	 * @since 0.71
	 *
	 * @param string $string Value to which backslashes will be added.
	 * @return string String with backslashes inserted.
	 */
	function backslashit($string) {
		$string = preg_replace('/^([0-9])/', '\\\\\\\\\1', $string);
		$string = preg_replace('/([a-z])/i', '\\\\\1', $string);
		return $string;
	}
	
	/**
	 * Appends a trailing slash.
	 *
	 * Will remove trailing slash if it exists already before adding a trailing
	 * slash. This prevents double slashing a string or path.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @since 1.2.0
	 * @uses untrailingslashit() Unslashes string if it was slashed already.
	 *
	 * @param string $string What to add the trailing slash to.
	 * @return string String with trailing slash added.
	 */
	function trailingslashit($string) {
		return untrailingslashit($string) . '/';
	}
	
	/**
	 * Removes trailing slash if it exists.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @since 2.2.0
	 *
	 * @param string $string What to remove the trailing slash from.
	 * @return string String without the trailing slash.
	 */
	function untrailingslashit($string) {
		return rtrim($string, '/');
	}
	

	/**
	 * Navigates through an array and removes slashes from the values.
	 *
	 * If an array is passed, the array_map() function causes a callback to pass the
	 * value back to the function. The slashes from this value will removed.
	 *
	 * @since 2.0.0
	 *
	 * @param array|string $value The array or string to be striped.
	 * @return array|string Stripped array (or string in the callback).
	 */
	function stripslashes_deep($value) {
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}
	
	/**
	 * Navigates through an array and encodes the values to be used in a URL.
	 *
	 * Uses a callback to pass the value of the array back to the function as a
	 * string.
	 *
	 * @since 2.2.0
	 *
	 * @param array|string $value The array or string to be encoded.
	 * @return array|string $value The encoded array (or string from the callback).
	 */
	function urlencode_deep($value) {
		$value = is_array($value) ? array_map('urlencode_deep', $value) : urlencode($value);
		return $value;
	}
	
	/**
	 * Converts email addresses characters to HTML entities to block spam bots.
	 *
	 * @since 0.71
	 *
	 * @param string $emailaddy Email address.
	 * @param int $mailto Optional. Range from 0 to 1. Used for encoding.
	 * @return string Converted email address.
	 */
	function antispambot($emailaddy, $mailto=0) {
		$emailNOSPAMaddy = '';
		srand ((float) microtime() * 1000000);
		for ($i = 0; $i < strlen($emailaddy); $i = $i + 1) {
			$j = floor(rand(0, 1+$mailto));
			if ($j==0) {
				$emailNOSPAMaddy .= '&#'.ord(substr($emailaddy,$i,1)).';';
			} elseif ($j==1) {
				$emailNOSPAMaddy .= substr($emailaddy,$i,1);
			} elseif ($j==2) {
				$emailNOSPAMaddy .= '%'.zeroise(dechex(ord(substr($emailaddy, $i, 1))), 2);
			}
		}
		$emailNOSPAMaddy = str_replace('@','&#64;',$emailNOSPAMaddy);
		return $emailNOSPAMaddy;
	}
	
	/**
	 * Callback to convert URI match to HTML A element.
	 *
	 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
	 * make_clickable()}.
	 *
	 * @since 2.3.2
	 * @access private
	 *
	 * @param array $matches Single Regex Match.
	 * @return string HTML A element with URI address.
	 */
	function _make_url_clickable_cb($matches) {
		$ret = '';
		$url = $matches[2];
		$url = clean_url($url);
		if ( empty($url) )
			return $matches[0];
		// removed trailing [.,;:] from URL
		if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
			$ret = substr($url, -1);
			$url = substr($url, 0, strlen($url)-1);
		}
		return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
	}
	
	/**
	 * Callback to convert URL match to HTML A element.
	 *
	 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
	 * make_clickable()}.
	 *
	 * @since 2.3.2
	 * @access private
	 *
	 * @param array $matches Single Regex Match.
	 * @return string HTML A element with URL address.
	 */
	function _make_web_ftp_clickable_cb($matches) {
		$ret = '';
		$dest = $matches[2];
		$dest = 'http://' . $dest;
		$dest = clean_url($dest);
		if ( empty($dest) )
			return $matches[0];
		// removed trailing [,;:] from URL
		if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
			$ret = substr($dest, -1);
			$dest = substr($dest, 0, strlen($dest)-1);
		}
		return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
	}
	
	/**
	 * Callback to convert email address match to HTML A element.
	 *
	 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
	 * make_clickable()}.
	 *
	 * @since 2.3.2
	 * @access private
	 *
	 * @param array $matches Single Regex Match.
	 * @return string HTML A element with email address.
	 */
	function _make_email_clickable_cb($matches) {
		$email = $matches[2] . '@' . $matches[3];
		return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
	}
	
	/**
	 * Convert plaintext URI to HTML links.
	 *
	 * Converts URI, www and ftp, and email addresses. Finishes by fixing links
	 * within links.
	 *
	 * @since 0.71
	 *
	 * @param string $ret Content to convert URIs.
	 * @return string Content with converted URIs.
	 */
	function make_clickable($ret) {
		$ret = ' ' . $ret;
		// in testing, using arrays here was found to be faster
		$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
		$ret = trim($ret);
		return $ret;
	}
	

	
	/**
	 * Checks to see if the text is a valid email address.
	 *
	 * @since 0.71
	 *
	 * @param string $user_email The email address to be checked.
	 * @return bool Returns true if valid, otherwise false.
	 */
	function is_email($user_email) {
		$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
		if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false) {
			if (preg_match($chars, $user_email)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Convert to ASCII from email subjects.
	 *
	 * @since 1.2.0
	 * @usedby wp_mail() handles charsets in email subjects
	 *
	 * @param string $string Subject line
	 * @return string Converted string to ASCII
	 */
	function iso_descrambler($string) {
		/* this may only work with iso-8859-1, I'm afraid */
		if (!preg_match('#\=\?(.+)\?Q\?(.+)\?\=#i', $string, $matches)) {
			return $string;
		} else {
			$subject = str_replace('_', ' ', $matches[2]);
			$subject = preg_replace_callback('#\=([0-9a-f]{2})#i', create_function('$match', 'return chr(hexdec(strtolower($match[1])));'), $subject);
			return $subject;
		}
	}
	
	/**
	 * Returns a date in the GMT equivalent.
	 *
	 * Requires and returns a date in the Y-m-d H:i:s format. Simply subtracts the
	 * value of the 'gmt_offset' option.
	 *
	 * @since 1.2.0
	 *
	 * @uses get_option() to retrieve the the value of 'gmt_offset'.
	 * @param string $string The date to be converted.
	 * @return string GMT version of the date provided.
	 */
	function get_gmt_from_date($string) {
		preg_match('#([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#', $string, $matches);
		$string_time = gmmktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
		$string_gmt = gmdate('Y-m-d H:i:s', $string_time - get_option('gmt_offset') * 3600);
		return $string_gmt;
	}
	
	/**
	 * Converts a GMT date into the correct format for the blog.
	 *
	 * Requires and returns in the Y-m-d H:i:s format. Simply adds the value of
	 * gmt_offset.
	 *
	 * @since 1.2.0
	 *
	 * @param string $string The date to be converted.
	 * @return string Formatted date relative to the GMT offset.
	 */
	function get_date_from_gmt($string) {
		preg_match('#([0-9]{1,4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})#', $string, $matches);
		$string_time = gmmktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
		$string_localtime = gmdate('Y-m-d H:i:s', $string_time + get_option('gmt_offset')*3600);
		return $string_localtime;
	}
	
	/**
	 * Computes an offset in seconds from an iso8601 timezone.
	 *
	 * @since 1.5.0
	 *
	 * @param string $timezone Either 'Z' for 0 offset or '±hhmm'.
	 * @return int|float The offset in seconds.
	 */
	function iso8601_timezone_to_offset($timezone) {
		// $timezone is either 'Z' or '[+|-]hhmm'
		if ($timezone == 'Z') {
			$offset = 0;
		} else {
			$sign    = (substr($timezone, 0, 1) == '+') ? 1 : -1;
			$hours   = intval(substr($timezone, 1, 2));
			$minutes = intval(substr($timezone, 3, 4)) / 60;
			$offset  = $sign * 3600 * ($hours + $minutes);
		}
		return $offset;
	}
	
	/**
	 * Converts an iso8601 date to MySQL DateTime format used by post_date[_gmt].
	 *
	 * @since 1.5.0
	 *
	 * @param string $date_string Date and time in ISO 8601 format {@link http://en.wikipedia.org/wiki/ISO_8601}.
	 * @param string $timezone Optional. If set to GMT returns the time minus gmt_offset. Default is 'user'.
	 * @return string The date and time in MySQL DateTime format - Y-m-d H:i:s.
	 */
	function iso8601_to_datetime($date_string, $timezone = 'user') {
		$timezone = strtolower($timezone);
	
		if ($timezone == 'gmt') {
	
			preg_match('#([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(Z|[\+|\-][0-9]{2,4}){0,1}#', $date_string, $date_bits);
	
			if (!empty($date_bits[7])) { // we have a timezone, so let's compute an offset
				$offset = iso8601_timezone_to_offset($date_bits[7]);
			} else { // we don't have a timezone, so we assume user local timezone (not server's!)
				$offset = 3600 * get_option('gmt_offset');
			}
	
			$timestamp = gmmktime($date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1]);
			$timestamp -= $offset;
	
			return gmdate('Y-m-d H:i:s', $timestamp);
	
		} else if ($timezone == 'user') {
			return preg_replace('#([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(Z|[\+|\-][0-9]{2,4}){0,1}#', '$1-$2-$3 $4:$5:$6', $date_string);
		}
	}
	
	/**
	 * Adds a element attributes to open links in new windows.
	 *
	 * Comment text in popup windows should be filtered through this. Right now it's
	 * a moderately dumb function, ideally it would detect whether a target or rel
	 * attribute was already there and adjust its actions accordingly.
	 *
	 * @since 0.71
	 *
	 * @param string $text Content to replace links to open in a new window.
	 * @return string Content that has filtered links.
	 */
	function popuplinks($text) {
		$text = preg_replace('/<a (.+?)>/i', "<a $1 target='_blank' rel='external'>", $text);
		return $text;
	}
	
	/**
	 * Strips out all characters that are not allowable in an email.
	 *
	 * @since 1.5.0
	 *
	 * @param string $email Email address to filter.
	 * @return string Filtered email address.
	 */
	function sanitize_email($email) {
		return preg_replace('/[^a-z0-9+_.@-]/i', '', $email);
	}
	
	/**
	 * Determines the difference between two timestamps.
	 *
	 * The difference is returned in a human readable format such as "1 hour",
	 * "5 mins", "2 days".
	 *
	 * @since 1.5.0
	 *
	 * @param int $from Unix timestamp from which the difference begins.
	 * @param int $to Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
	 * @return string Human readable time difference.
	 */
	function human_time_diff( $from, $to = '' ) {
		if ( empty($to) )
			$to = time();
		$diff = (int) abs($to - $from);
		if ($diff <= 3600) {
			$mins = round($diff / 60);
			if ($mins <= 1) {
				$mins = 1;
			}
			$since = sprintf(__ngettext('%s min', '%s mins', $mins), $mins);
		} else if (($diff <= 86400) && ($diff > 3600)) {
			$hours = round($diff / 3600);
			if ($hours <= 1) {
				$hours = 1;
			}
			$since = sprintf(__ngettext('%s hour', '%s hours', $hours), $hours);
		} elseif ($diff >= 86400) {
			$days = round($diff / 86400);
			if ($days <= 1) {
				$days = 1;
			}
			$since = sprintf(__ngettext('%s day', '%s days', $days), $days);
		}
		return $since;
	}
	
	
	/**
	 * Converts named entities into numbered entities.
	 *
	 * @since 1.5.1
	 *
	 * @param string $text The text within which entities will be converted.
	 * @return string Text with converted entities.
	 */
	function ent2ncr($text) {
		$to_ncr = array(
			'&quot;' => '&#34;',
			'&amp;' => '&#38;',
			'&frasl;' => '&#47;',
			'&lt;' => '&#60;',
			'&gt;' => '&#62;',
			'|' => '&#124;',
			'&nbsp;' => '&#160;',
			'&iexcl;' => '&#161;',
			'&cent;' => '&#162;',
			'&pound;' => '&#163;',
			'&curren;' => '&#164;',
			'&yen;' => '&#165;',
			'&brvbar;' => '&#166;',
			'&brkbar;' => '&#166;',
			'&sect;' => '&#167;',
			'&uml;' => '&#168;',
			'&die;' => '&#168;',
			'&copy;' => '&#169;',
			'&ordf;' => '&#170;',
			'&laquo;' => '&#171;',
			'&not;' => '&#172;',
			'&shy;' => '&#173;',
			'&reg;' => '&#174;',
			'&macr;' => '&#175;',
			'&hibar;' => '&#175;',
			'&deg;' => '&#176;',
			'&plusmn;' => '&#177;',
			'&sup2;' => '&#178;',
			'&sup3;' => '&#179;',
			'&acute;' => '&#180;',
			'&micro;' => '&#181;',
			'&para;' => '&#182;',
			'&middot;' => '&#183;',
			'&cedil;' => '&#184;',
			'&sup1;' => '&#185;',
			'&ordm;' => '&#186;',
			'&raquo;' => '&#187;',
			'&frac14;' => '&#188;',
			'&frac12;' => '&#189;',
			'&frac34;' => '&#190;',
			'&iquest;' => '&#191;',
			'&Agrave;' => '&#192;',
			'&Aacute;' => '&#193;',
			'&Acirc;' => '&#194;',
			'&Atilde;' => '&#195;',
			'&Auml;' => '&#196;',
			'&Aring;' => '&#197;',
			'&AElig;' => '&#198;',
			'&Ccedil;' => '&#199;',
			'&Egrave;' => '&#200;',
			'&Eacute;' => '&#201;',
			'&Ecirc;' => '&#202;',
			'&Euml;' => '&#203;',
			'&Igrave;' => '&#204;',
			'&Iacute;' => '&#205;',
			'&Icirc;' => '&#206;',
			'&Iuml;' => '&#207;',
			'&ETH;' => '&#208;',
			'&Ntilde;' => '&#209;',
			'&Ograve;' => '&#210;',
			'&Oacute;' => '&#211;',
			'&Ocirc;' => '&#212;',
			'&Otilde;' => '&#213;',
			'&Ouml;' => '&#214;',
			'&times;' => '&#215;',
			'&Oslash;' => '&#216;',
			'&Ugrave;' => '&#217;',
			'&Uacute;' => '&#218;',
			'&Ucirc;' => '&#219;',
			'&Uuml;' => '&#220;',
			'&Yacute;' => '&#221;',
			'&THORN;' => '&#222;',
			'&szlig;' => '&#223;',
			'&agrave;' => '&#224;',
			'&aacute;' => '&#225;',
			'&acirc;' => '&#226;',
			'&atilde;' => '&#227;',
			'&auml;' => '&#228;',
			'&aring;' => '&#229;',
			'&aelig;' => '&#230;',
			'&ccedil;' => '&#231;',
			'&egrave;' => '&#232;',
			'&eacute;' => '&#233;',
			'&ecirc;' => '&#234;',
			'&euml;' => '&#235;',
			'&igrave;' => '&#236;',
			'&iacute;' => '&#237;',
			'&icirc;' => '&#238;',
			'&iuml;' => '&#239;',
			'&eth;' => '&#240;',
			'&ntilde;' => '&#241;',
			'&ograve;' => '&#242;',
			'&oacute;' => '&#243;',
			'&ocirc;' => '&#244;',
			'&otilde;' => '&#245;',
			'&ouml;' => '&#246;',
			'&divide;' => '&#247;',
			'&oslash;' => '&#248;',
			'&ugrave;' => '&#249;',
			'&uacute;' => '&#250;',
			'&ucirc;' => '&#251;',
			'&uuml;' => '&#252;',
			'&yacute;' => '&#253;',
			'&thorn;' => '&#254;',
			'&yuml;' => '&#255;',
			'&OElig;' => '&#338;',
			'&oelig;' => '&#339;',
			'&Scaron;' => '&#352;',
			'&scaron;' => '&#353;',
			'&Yuml;' => '&#376;',
			'&fnof;' => '&#402;',
			'&circ;' => '&#710;',
			'&tilde;' => '&#732;',
			'&Alpha;' => '&#913;',
			'&Beta;' => '&#914;',
			'&Gamma;' => '&#915;',
			'&Delta;' => '&#916;',
			'&Epsilon;' => '&#917;',
			'&Zeta;' => '&#918;',
			'&Eta;' => '&#919;',
			'&Theta;' => '&#920;',
			'&Iota;' => '&#921;',
			'&Kappa;' => '&#922;',
			'&Lambda;' => '&#923;',
			'&Mu;' => '&#924;',
			'&Nu;' => '&#925;',
			'&Xi;' => '&#926;',
			'&Omicron;' => '&#927;',
			'&Pi;' => '&#928;',
			'&Rho;' => '&#929;',
			'&Sigma;' => '&#931;',
			'&Tau;' => '&#932;',
			'&Upsilon;' => '&#933;',
			'&Phi;' => '&#934;',
			'&Chi;' => '&#935;',
			'&Psi;' => '&#936;',
			'&Omega;' => '&#937;',
			'&alpha;' => '&#945;',
			'&beta;' => '&#946;',
			'&gamma;' => '&#947;',
			'&delta;' => '&#948;',
			'&epsilon;' => '&#949;',
			'&zeta;' => '&#950;',
			'&eta;' => '&#951;',
			'&theta;' => '&#952;',
			'&iota;' => '&#953;',
			'&kappa;' => '&#954;',
			'&lambda;' => '&#955;',
			'&mu;' => '&#956;',
			'&nu;' => '&#957;',
			'&xi;' => '&#958;',
			'&omicron;' => '&#959;',
			'&pi;' => '&#960;',
			'&rho;' => '&#961;',
			'&sigmaf;' => '&#962;',
			'&sigma;' => '&#963;',
			'&tau;' => '&#964;',
			'&upsilon;' => '&#965;',
			'&phi;' => '&#966;',
			'&chi;' => '&#967;',
			'&psi;' => '&#968;',
			'&omega;' => '&#969;',
			'&thetasym;' => '&#977;',
			'&upsih;' => '&#978;',
			'&piv;' => '&#982;',
			'&ensp;' => '&#8194;',
			'&emsp;' => '&#8195;',
			'&thinsp;' => '&#8201;',
			'&zwnj;' => '&#8204;',
			'&zwj;' => '&#8205;',
			'&lrm;' => '&#8206;',
			'&rlm;' => '&#8207;',
			'&ndash;' => '&#8211;',
			'&mdash;' => '&#8212;',
			'&lsquo;' => '&#8216;',
			'&rsquo;' => '&#8217;',
			'&sbquo;' => '&#8218;',
			'&ldquo;' => '&#8220;',
			'&rdquo;' => '&#8221;',
			'&bdquo;' => '&#8222;',
			'&dagger;' => '&#8224;',
			'&Dagger;' => '&#8225;',
			'&bull;' => '&#8226;',
			'&hellip;' => '&#8230;',
			'&permil;' => '&#8240;',
			'&prime;' => '&#8242;',
			'&Prime;' => '&#8243;',
			'&lsaquo;' => '&#8249;',
			'&rsaquo;' => '&#8250;',
			'&oline;' => '&#8254;',
			'&frasl;' => '&#8260;',
			'&euro;' => '&#8364;',
			'&image;' => '&#8465;',
			'&weierp;' => '&#8472;',
			'&real;' => '&#8476;',
			'&trade;' => '&#8482;',
			'&alefsym;' => '&#8501;',
			'&crarr;' => '&#8629;',
			'&lArr;' => '&#8656;',
			'&uArr;' => '&#8657;',
			'&rArr;' => '&#8658;',
			'&dArr;' => '&#8659;',
			'&hArr;' => '&#8660;',
			'&forall;' => '&#8704;',
			'&part;' => '&#8706;',
			'&exist;' => '&#8707;',
			'&empty;' => '&#8709;',
			'&nabla;' => '&#8711;',
			'&isin;' => '&#8712;',
			'&notin;' => '&#8713;',
			'&ni;' => '&#8715;',
			'&prod;' => '&#8719;',
			'&sum;' => '&#8721;',
			'&minus;' => '&#8722;',
			'&lowast;' => '&#8727;',
			'&radic;' => '&#8730;',
			'&prop;' => '&#8733;',
			'&infin;' => '&#8734;',
			'&ang;' => '&#8736;',
			'&and;' => '&#8743;',
			'&or;' => '&#8744;',
			'&cap;' => '&#8745;',
			'&cup;' => '&#8746;',
			'&int;' => '&#8747;',
			'&there4;' => '&#8756;',
			'&sim;' => '&#8764;',
			'&cong;' => '&#8773;',
			'&asymp;' => '&#8776;',
			'&ne;' => '&#8800;',
			'&equiv;' => '&#8801;',
			'&le;' => '&#8804;',
			'&ge;' => '&#8805;',
			'&sub;' => '&#8834;',
			'&sup;' => '&#8835;',
			'&nsub;' => '&#8836;',
			'&sube;' => '&#8838;',
			'&supe;' => '&#8839;',
			'&oplus;' => '&#8853;',
			'&otimes;' => '&#8855;',
			'&perp;' => '&#8869;',
			'&sdot;' => '&#8901;',
			'&lceil;' => '&#8968;',
			'&rceil;' => '&#8969;',
			'&lfloor;' => '&#8970;',
			'&rfloor;' => '&#8971;',
			'&lang;' => '&#9001;',
			'&rang;' => '&#9002;',
			'&larr;' => '&#8592;',
			'&uarr;' => '&#8593;',
			'&rarr;' => '&#8594;',
			'&darr;' => '&#8595;',
			'&harr;' => '&#8596;',
			'&loz;' => '&#9674;',
			'&spades;' => '&#9824;',
			'&clubs;' => '&#9827;',
			'&hearts;' => '&#9829;',
			'&diams;' => '&#9830;'
		);
	
		return str_replace( array_keys($to_ncr), array_values($to_ncr), $text );
	}
	


	/**
	 * Checks and cleans a URL.
	 *
	 * A number of characters are removed from the URL. If the URL is for displaying
	 * (the default behaviour) amperstands are also replaced. The 'clean_url' filter
	 * is applied to the returned cleaned URL.
	 *
	 * @since 1.2.0
	 * @uses wp_kses_bad_protocol() To only permit protocols in the URL set
	 *		via $protocols or the common ones set in the function.
	 *
	 * @param string $url The URL to be cleaned.
	 * @param array $protocols Optional. An array of acceptable protocols.
	 *		Defaults to 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' if not set.
	 * @param string $context Optional. How the URL will be used. Default is 'display'.
	 * @return string The cleaned $url after the 'cleaned_url' filter is applied.
	 */
	function clean_url( $url, $protocols = null, $context = 'display' ) {
		$original_url = $url;
	
		if ('' == $url) return $url;
		$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$*\'()\\x80-\\xff]|i', '', $url);
		$strip = array('%0d', '%0a');
		$url = str_replace($strip, '', $url);
		$url = str_replace(';//', '://', $url);
		/* If the URL doesn't appear to contain a scheme, we
		 * presume it needs http:// appended (unless a relative
		 * link starting with / or a php file).
		 */
		if ( strpos($url, ':') === false &&
			substr( $url, 0, 1 ) != '/' && !preg_match('/^[a-z0-9-]+?\.php/i', $url) )
			$url = 'http://' . $url;
	
		// Replace ampersands and single quotes only when displaying.
		if ( 'display' == $context ) {
			$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
			$url = str_replace( "'", '&#039;', $url );
		}
	
	
		return $url;
	}
	
	/**
	 * Performs clean_url() for database usage.
	 *
	 * @see clean_url()
	 *
	 * @since 2.3.1
	 *
	 * @param string $url The URL to be cleaned.
	 * @param array $protocols An array of acceptable protocols.
	 * @return string The cleaned URL.
	 */
	function sanitize_url( $url, $protocols = null ) {
		return clean_url( $url, $protocols, 'db' );
	}
	
	/**
	 * Convert entities, while preserving already-encoded entities.
	 *
	 * @link http://www.php.net/htmlentities Borrowed from the PHP Manual user notes.
	 *
	 * @since 1.2.2
	 *
	 * @param string $myHTML The text to be converted.
	 * @return string Converted text.
	 */
	function htmlentities2($myHTML) {
		$translation_table = get_html_translation_table( HTML_ENTITIES, ENT_QUOTES );
		$translation_table[chr(38)] = '&';
		return preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/", "&amp;", strtr($myHTML, $translation_table) );
	}
	
	/**
	 * Escape single quotes, specialchar double quotes, and fix line endings.
	 *
	 * The filter 'js_escape' is also applied here.
	 *
	 * @since 2.0.4
	 *
	 * @param string $text The text to be escaped.
	 * @return string Escaped text.
	 */
	function js_escape($text) {
		$safe_text = $this->check_invalid_utf8( $text );
		$safe_text = $this->specialchars( $safe_text, ENT_COMPAT );
		$safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );
		$safe_text = preg_replace( "/\r?\n/", "\\n", addslashes( $safe_text ) );
		return $safe_text ;
	}
	
	/**
	 * Escaping for HTML attributes.
	 *
	 * @since 2.0.6
	 *
	 * @param string $text
	 * @return string
	 */
	function attribute_escape( $text ) {
		$safe_text = $this->check_invalid_utf8( $text );
		$safe_text = $this->specialchars( $safe_text, ENT_QUOTES );
		return $safe_text;
	}
	
	/**
	 * Escape a HTML tag name.
	 *
	 * @since 2.5.0
	 *
	 * @param string $tag_name
	 * @return string
	 */
	function tag_escape($tag_name) {
		$safe_tag = strtolower( preg_replace('/[^a-zA-Z_:]/', '', $tag_name) );
		return $safe_tag;
	}
	
	/**
	 * Escapes text for SQL LIKE special characters % and _.
	 *
	 * @since 2.5.0
	 *
	 * @param string $text The text to be escaped.
	 * @return string text, safe for inclusion in LIKE query.
	 */
	function like_escape($text) {
		return str_replace(array("%", "_"), array("\\%", "\\_"), $text);
	}
	
	/**
	 * Convert full URL paths to absolute paths.
	 *
	 * Removes the http or https protocols and the domain. Keeps the path '/' at the
	 * beginning, so it isn't a true relative link, but from the web root base.
	 *
	 * @since 2.1.0
	 *
	 * @param string $link Full URL path.
	 * @return string Absolute path.
	 */
	function wp_make_link_relative( $link ) {
		return preg_replace( '|https?://[^/]+(/.*)|i', '$1', $link );
	}
	


	
	/**
	 * Safely extracts not more than the first $count characters from html string.
	 *
	 * UTF-8, tags and entities safe prefix extraction. Entities inside will *NOT*
	 * be counted as one character. For example &amp; will be counted as 4, &lt; as
	 * 3, etc.
	 *
	 * @since 2.5.0
	 *
	 * @param integer $str String to get the excerpt from.
	 * @param integer $count Maximum number of characters to take.
	 * @return string The excerpt.
	 */
	function html_excerpt( $str, $count ) {
		$str = strip_tags( $str );
		$str = mb_strcut( $str, 0, $count );
		// remove part of an entity at the end
		$str = preg_replace( '/&[^;\s]{0,6}$/', '', $str );
		return $str;
	}
	
	/**
	 * Add a Base url to relative links in passed content.
	 *
	 * By default it supports the 'src' and 'href' attributes. However this can be
	 * changed via the 3rd param.
	 *
	 * @since 2.7.0
	 *
	 * @param string $content String to search for links in.
	 * @param string $base The base URL to prefix to links.
	 * @param array $attrs The attributes which should be processed.
	 * @return string The processed content.
	 */
	function links_add_base_url( $content, $base, $attrs = array('src', 'href') ) {
		$attrs = implode('|', (array)$attrs);
		return preg_replace_callback("!($attrs)=(['\"])(.+?)\\2!i",
				create_function('$m', 'return _links_add_base($m, "' . $base . '");'),
				$content);
	}
	
	/**
	 * Callback to add a base url to relative links in passed content.
	 *
	 * @since 2.7.0
	 * @access private
	 *
	 * @param string $m The matched link.
	 * @param string $base The base URL to prefix to links.
	 * @return string The processed link.
	 */
	function _links_add_base($m, $base) {
		//1 = attribute name  2 = quotation mark  3 = URL
		return $m[1] . '=' . $m[2] .
			(strpos($m[3], 'http://') === false ?
				path_join($base, $m[3]) :
				$m[3])
			. $m[2];
	}
	
	/**
	 * Adds a Target attribute to all links in passed content.
	 *
	 * This function by default only applies to <a> tags, however this can be
	 * modified by the 3rd param.
	 *
	 * <b>NOTE:</b> Any current target attributed will be striped and replaced.
	 *
	 * @since 2.7.0
	 *
	 * @param string $content String to search for links in.
	 * @param string $target The Target to add to the links.
	 * @param array $tags An array of tags to apply to.
	 * @return string The processed content.
	 */
	function links_add_target( $content, $target = '_blank', $tags = array('a') ) {
		$tags = implode('|', (array)$tags);
		return preg_replace_callback("!<($tags)(.+?)>!i",
				create_function('$m', 'return _links_add_target($m, "' . $target . '");'),
				$content);
	}
	/**
	 * Callback to add a target attribute to all links in passed content.
	 *
	 * @since 2.7.0
	 * @access private
	 *
	 * @param string $m The matched link.
	 * @param string $target The Target to add to the links.
	 * @return string The processed link.
	 */
	function _links_add_target( $m, $target ) {
		$tag = $m[1];
		$link = preg_replace('|(target=[\'"](.*?)[\'"])|i', '', $m[2]);
		return '<' . $tag . $link . ' target="' . $target . '">';
	}
	
	// normalize EOL characters and strip duplicate whitespace
	function normalize_whitespace( $str ) {
		$str  = trim($str);
		$str  = str_replace("\r", "\n", $str);
		$str  = preg_replace( array( '/\n+/', '/[ \t]+/' ), array( "\n", ' ' ), $str );
		return $str;
	}
	/**
   * @function : txt_serialize  
   * @param 		: $arr_text -> 1 mang
   * @return		: 1 chuoi string
   */
  function txt_serialize ($arr_text = array())
  {
		$arr_text_new = array();
		if (is_array($arr_text) )
		{
			foreach ($arr_text as $key => $value){
				$arr_text_new[$key ] = stripslashes($value);
			}
		}
   	$t = serialize($arr_text_new);
    return $t;
  }
	
	//end class
}
?>