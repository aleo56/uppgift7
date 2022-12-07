<?php
session_start();
$loggedIn = isset($_SESSION["user"]);
if ($loggedIn) {
    echo "You are logged in.";
    echo "<br/>";
    echo "<a href='log-out.php'>Log out</<a>";
    echo "<br/>";
    echo "<a href='filuppladning.html'>Ladda upp fil</a>";
} else {
    echo '<a href="sign-in.html">Sign in</a>';
    echo "<br/>";
    echo '<a href="sign-up.html">Sign up</a>';
}
