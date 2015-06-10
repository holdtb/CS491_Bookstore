<?php
session_start();
// nuke the session.
session_destroy();
$_SESSION = array();
header('Location: https://websso.wwu.edu/cas/logout');
?>