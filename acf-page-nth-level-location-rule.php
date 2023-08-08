<?php
/**
 * Plugin Name: ACF Page Nth Level Location Rule
 * Plugin URI: https://robertwent.com/
 * Description: A custom rule for Advanced Custom Fields that allows targeting of pages at a specific level. <a target="_blank" rel="noopener" href="https://github.com/Hube2/acf-filters-and-functions/">Original Code</a>
 * Version: 1.0.0
 * Author: Robert Went /
 * Author URI: https://robertwent.com/
 * License: GPL2
 */

 /**
  * https://github.com/Hube2/acf-filters-and-functions/blob/master/page-nth-level-location-rule.php
  * Custom location rule for ACF: Page Level
  * Level "1" = top level parent page
  * This should work on any hierarchical post type
  * Works on number of ancestors
  */

  add_filter( 'acf/location/rule_types', function( $choices ) {
	$choices['Page']['page_level'] = 'Page Level';

	return $choices;
  } );

  add_filter( 'acf/location/rule_operators', function( $choices ) {
	$new_choices = array(
		'<'  => 'is less than',
		'<=' => 'is less than or equal to',
		'>=' => 'is greater than or equal to',
		'>'  => 'is greater than'
	);
	foreach ( $new_choices as $key => $value ) {
		$choices[ $key ] = $value;
	}

	return $choices;
  } );

  add_filter( 'acf/location/rule_values/page_level', function( $choices ) {
	for ( $i = 1; $i <= 10; $i ++ ) {
		$choices[ $i ] = $i;
	}

	return $choices;
  } );

  add_filter( 'acf/location/rule_match/page_level', function( $match, $rule, $options ) {
	if ( ! isset( $options['post_id'] ) ) {
		return $match;
	}
	$post_type   = get_post_type( $options['post_id'] );
	$page_parent = 0;
	if ( ! array_key_exists( 'page_parent', $options ) || ! $options['page_parent'] ) {
		$post        = get_post( $options['post_id'] );
		$page_parent = $post->post_parent;
	} else {
		$page_parent = $options['page_parent'];
	}
	if ( ! $page_parent ) {
		$page_level = 1;
	} else {
		$ancestors  = get_ancestors( $page_parent, $post_type );
		$page_level = count( $ancestors ) + 2;
	}
	$operator = $rule['operator'];
	$value    = $rule['value'];
	switch ( $operator ) {
		case '==':
			$match = ( $page_level == $value );
			break;
		case '!=':
			$match = ( $page_level != $value );
			break;
		case '<':
			$match = ( $page_level < $value );
			break;
		case '<=':
			$match = ( $page_level <= $value );
			break;
		case '>=':
			$match = ( $page_level >= $value );
			break;
		case '>':
			$match = ( $page_level > $value );
			break;
	}

	return $match;
  }, 10, 3 );
