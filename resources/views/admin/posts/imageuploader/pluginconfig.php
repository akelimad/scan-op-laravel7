<?php

// Version of the plugin
$currentpluginver = "4.1.3";

// show/hide file extension
if(!isset($_COOKIE["file_extens"])){
    $file_extens = "no";
} else {
    $file_extens = $_COOKIE["file_extens"];
}

// file_style
if(!isset($_COOKIE["file_style"])){
    $file_style = "block";
}

// Path to the upload folder, please set the path using the Image Browser Settings menu.

$useruploadfolder = 'uploads/posts/'. Auth::user()->team_id;;
$useruploadpath = "$useruploadfolder/";
