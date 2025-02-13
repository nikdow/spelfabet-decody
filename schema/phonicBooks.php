<?php
add_filter('pgcs_min', function ($structure_level, $term_name, $pgcs, $pgc_level){
  if( $term_name === 'phonic books') {
    $lastSyllable = end($pgcs);
    $grapheme = explode(":", $lastSyllable)[0];
    if (strtolower($grapheme) === "le") $structure_level = 20;
  }
  return $structure_level;
}, 10, 4);
add_filter( 'structure_min', function ($structure_level, $term_name, $structure){
  if( $term_name === 'phonic books'){
    $countV = array_reduce( str_split($structure), function( $acc, $letter){
      $acc += strtolower($letter) === "v" ? 1 : 0;
      return $acc;
    }, 0);
    if( $countV === 2 ) $structure_level = 17;
  }
  return $structure_level;
}, 10, 3);
add_filter( 'no_structure', function( $structure_level, $term_name, $structure){
  if( $term_name === 'phonic books') {
      $structure_level = 22;
  }
  return $structure_level;
}, 10, 3);