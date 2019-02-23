<?php
session_start();

$project    = "overflow";
$siteTitle  = "R&S Overflow";
$pageTitle  = "R&S Overflow";
$image      = "https://overflow.religionandstory.com/common/images/church.jpg";
$description= "This site contains various overflow projects from Daniel Crouch and the Religion & Story network.";
$keywords   = "IMDb,Scripture,Poker";
$homeUrl    = "https://overflow.religionandstory.com";

function includeHeadInfo()
{
    global $siteTitle;
    global $pageTitle;
    global $image;
    global $description;
    global $keywords;
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/head.php");
}

function includeHeader()
{
    global $homeUrl;
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/header.php");
}

function includeModals()
{
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/modal.html");
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/modal-binary.html");
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/modal-prompt.html");
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/modal-prompt-big.html");
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/toaster.html");
}

function getHelpImage()
{
    echo "https://religionandstory.com/common/images/question-mark.png";
}

function getConstructionImage()
{
    echo "https://image.freepik.com/free-icon/traffic-cone-signal-tool-for-traffic_318-62079.jpg";
}

?>