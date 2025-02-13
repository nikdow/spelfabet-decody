<?php
add_filter( 'structure_min', function ($structure_level, $term_name, $structure){
  if( $term_name === 'Phonics Plus (Vic DET)'){
    $countV = array_reduce( str_split($structure), function( $acc, $letter){
      $acc += strtolower($letter) === "v" ? 1 : 0;
      return $acc;
    }, 0);
    $structure_level = match ($countV){
      2 => 11,
      3 => 21,
      4 => 27,
      default => 1,
    };
  }
  return $structure_level;
}, 10, 3);
