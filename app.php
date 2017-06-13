<?php

require 'Search.php';

$search = new Search();
$query = 'Системный администратор БД и безопасности';

echo $search->generate($query);