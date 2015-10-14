<?php
/* MapifyPro */
// Adds a column to the admin ui signifying the correct shortcode to use for maps
include_once('admin-column-shortcode.php');

// Adds a column to the admin ui signifying the map location permalink
include_once('admin-column-permalink.php');

// Allows maps to cluster markers
include_once('map-clustering.php');

// Allows maps to use snazzymaps styles
include_once('snazzymaps/snazzymaps.php');

// Provides batch uploading of map locations
include_once('batch-upload/batch-upload.php');

// Provides social sharing capabilities for locations
include_once('social-sharing/social-sharing.php');

// Provides ability to change various popup colors
include_once('popup-colors/popup-colors.php');

// Provides ability to display a location details block in the popup
include_once('popup-location-details.php');

// Provides ability to make posts act as locations
include_once('supported-post-types.php');

// Adds map locations to the blog listing along with normal posts
include_once('locations-in-blog.php');

// Adds image mode to maps
include_once('image-mode/image-mode.php');

// Adds image mode to maps
include_once('multiple-maps.php');
