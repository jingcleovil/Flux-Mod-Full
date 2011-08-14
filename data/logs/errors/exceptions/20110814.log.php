<?php exit('Forbidden'); ?>
[2011-08-14 04:20:02] (PDOException) Exception PDOException: SQLSTATE[42000] [1044] Access denied for user 'ragnarok'@'localhost' to database 'ragnarok2'
[2011-08-14 04:20:02] (PDOException) **TRACE** #0 D:\htdocs\ragnarok\fluxmod\lib\Flux\Connection.php(81): PDO->__construct('mysql:host=127....', 'ragnarok', 'ragnarok', Array)
[2011-08-14 04:20:02] (PDOException) **TRACE** #1 D:\htdocs\ragnarok\fluxmod\lib\Flux\Connection.php(94): Flux_Connection->connect(Object(Flux_Config))
[2011-08-14 04:20:02] (PDOException) **TRACE** #2 D:\htdocs\ragnarok\fluxmod\lib\Flux\Connection.php(159): Flux_Connection->getConnection()
[2011-08-14 04:20:02] (PDOException) **TRACE** #3 D:\htdocs\ragnarok\fluxmod\modules\install\index.php(17): Flux_Connection->getStatement('SELECT VERSION(...')
[2011-08-14 04:20:02] (PDOException) **TRACE** #4 D:\htdocs\ragnarok\fluxmod\lib\Flux\Template.php(337): include('D:\htdocs\ragna...')
[2011-08-14 04:20:02] (PDOException) **TRACE** #5 D:\htdocs\ragnarok\fluxmod\lib\Flux\Dispatcher.php(168): Flux_Template->render()
[2011-08-14 04:20:02] (PDOException) **TRACE** #6 D:\htdocs\ragnarok\fluxmod\index.php(170): Flux_Dispatcher->dispatch(Array)
[2011-08-14 04:20:02] (PDOException) **TRACE** #7 {main}
