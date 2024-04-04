<?php

define('ENV', parse_ini_file(__DIR__ . '/../.env'));
define('IP', $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

define('EMAIL_TITLE_REGISTER', 'Carol Dance - Cadastro');
define('EMAIL_BODY_REGISTER', 'Olá, seja bem vindo ao Estudio Carol Dance.<br><br> 
Ficamos felizes em te-lo conosco nesse mundo mágico da dança e música, abaixo estão dados de acesso.<br> 
Lembre-se de não compartilhar sua senha com outras pessoas. A mesma garante a integridade dos dados fornecido dentro do sistema.<br><br>');

define('EMAIL_TITLE_FORGOT_PASSWORD', 'Carol Dance - Recuperar Senha');
define('EMAIL_BODY_FORGOT_PASSWORD', "Olá, parece que vocẽ esqueceu sua senha!<br>
Clique no link abaixo para efetuar a troca de senha. <br>
Lembre-se de não compartilhar sua senha com outras pessoas. A mesma garante a integridade dos dados fornecido dentro do sistema.<br><br>");

define('EMAIL_LINK_CHANGE_PASSWORD', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] . "/caroldance/signin/changePassword");