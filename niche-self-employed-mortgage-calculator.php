<?php

/*
Plugin Name: Niche Self Employment Mortgage Calculator
Description: Self Employment Mortgage Calculator provided via the <code>[niche-self-employed-mortgage-calculator]</code> shortcode. This will render the full calculator.
Version: 1.0
Author: Niche Mortgage Info (https://nichemortgageinfo.co.uk)
*/

/**
 * Require the class.
 */
require __DIR__ . '/lib/SelfEmployedMortgageCalculator.php';

/**
 * Instantiate a new instance of our plugin class.
 */
$SelfEmployedMortgageCalculator = new Conobe\SelfEmployedMortgageCalculator(__DIR__);

// init
$SelfEmployedMortgageCalculator->ignition();
