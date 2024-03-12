<?php
function randomName($model) 
{
  $min = 1;
  $max = 1000000000;
  $random_number = rand($min, $max);

	$characters = '0123456789abcdefghijklmnopqrstuvwxyz!?';
	$charactersLength = strlen($characters);
	$rand_char = $characters[random_int(0, $charactersLength - 1)];

  $name = 'php-sdk-' . $model . '-' . $rand_char . $random_number;

  return $name;
};
