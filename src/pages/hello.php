<?php

$name = $request->query->get('name', 'World');

?>

Hello <?= htmlspecialchars($name, ENT_QUOTES);
