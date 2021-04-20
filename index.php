<?php

$name = isset($_GET['name']) ? $_GET['name'] : "world";

printf('hello %s', $name);