<?php

$project = "overflow";
$homeUrl = "http://overflow.religionandstory.com";

function includeHeadInfo()
{
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/head.html");
}

function includeHeader()
{
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/header.html");
}

function includeModals()
{
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/modal.html");
    include("$_SERVER[DOCUMENT_ROOT]/../common/html/toaster.html");
}

function getHelpImage()
{
    echo "$_SERVER[DOCUMENT_ROOT]/../common/images/question-mark.png";
}

function getConstructionImage()
{
    echo "https://image.freepik.com/free-icon/traffic-cone-signal-tool-for-traffic_318-62079.jpg";
}

?>