<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/database/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shiv Kailasa | Private Residences, MIHAN Nagpur</title>
<meta name="description" content="Two and three bedroom residences within thirty private acres, opposite AIIMS &amp; IIM, MIHAN, Nagpur.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div id="loader"><div><b>SHIV KAILASA</b><span>Loading...</span></div></div>

<!-- Header -->
 <?php /*
<header id="hdr">
  <div class="wrap">
  <a href="#home" class="logo"><b>SHIV KAILASA</b><small>Private Residences</small></a>
  <nav id="nav"><ul>
    <li><a href="#home">Home</a></li><li><a href="#about">About</a></li><li><a href="#residences">Residences</a></li>
    <li><a href="#amenities">Amenities</a></li><li><a href="#location">Location</a></li><li><a href="#gallery">Gallery</a></li><li><a href="#contact">Contact</a></li>
  </ul></nav>
  <a href="#contact" class="vbtn">Viewings by appointment</a>
  <button class="burger" id="burger" aria-label="Menu"><i></i><i></i></button>
</div>
</header>*/ ?>

