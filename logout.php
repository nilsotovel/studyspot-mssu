<?php
session_start();

// Clear everything stored in the session, then end it
$_SESSION = [];
session_destroy();

header("Location: login.html");
exit();
