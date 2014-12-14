<?php
  $word = " 1. Everton                    104  4062  1652   998  1412  6373 - 5719  4302";
  $pattern = '/[A-Z]/';
  $woo = preg_match($pattern, $word, $match);
  // echo $woo;
  print_r($match);
?>