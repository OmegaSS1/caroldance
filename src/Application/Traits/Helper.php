<?php

declare(strict_types=1);

namespace App\Application\Traits;

use App\Application\Actions\Signin\SigninException;
use App\Database\DatabaseInterface;
use App\Domain\DomainException\CustomDomainException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\Response as newResponse;
use Slim\Psr7\Stream;
use Slim\Routing\RouteContext;
use Datetime;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

use ZipArchive;

use Dompdf\Dompdf;
use Dompdf\Options;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use App\Model\Database as DB;
use App\Model\Database;
use App\Query\Query;
use DateInterval;
use DOMDocument;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

use Mpdf\Mpdf;

use MercadoPago\SDK;
use MercadoPago\Item;
use MercadoPago\Preference;


trait Helper
{

	/**
	 * Remove toda tag de sql.
	 *
	 * @param array $form Conteudo a ser verificado.
	 * @return array
	 */
	public function removeSQLInjection(array $form): array
	{
		$search = array('create', 'insert into', 'select', 'update', 'delete', 'where', ' and ', ' or ', ' not ', 'order', 'limit', 'group', 'by', 'inner', 'join', ' on ', 'union', 'min ', 'max', 'like', '[*]', 'from', '<', '>', 'script', 'svg', 'onload');
		foreach ($search as $s) {
			foreach ($form as $key => $value) {
				if (is_string($value)) {
					preg_match("/$s/i", $value, $matches);
					if (!empty($matches)) {
						$this->logger->warning("[Helper - (removeSQLInjection) ID {$this->USER->data->id} IP " . IP . "] Tentativa de SQLInjection!", [$value]);
						$value = str_replace($matches[0], '', $value);
					}
					$form[$key] = trim(preg_replace("/\s+/", ' ', $value));
				}
			}
		}
		return $form;
	}

	public function validKeysForm(array $form, array $keys)
	{
		foreach ($keys as $k) {
			if (!isset($form[$k])) {
				$this->logger->info("[Helper (validKeysForm) ID {$this->USER->data->id} - IP " . IP . "] Parametros incompletos!", $form);
				throw new Exception("É obrigatório informar o campo " . strtoupper($k));
			} else if ($form[$k] == "") {
				$this->logger->info("[Helper (validKeysForm) ID {$this->USER->data->id} - IP " . IP . "] Parametros incompletos!", $form);
				throw new Exception("É obrigatório informar o campo " . strtoupper($k));
			} else if (is_float($form[$k]) or is_int($form[$k])) {
				if ($form[$k] < 0) {
					$this->logger->info("[Helper (validKeysForm) ID {$this->USER->data->id} - IP " . IP . "] Parametros incompletos!", $form);
					throw new Exception("O valor do campo " . strtoupper($k) . " não pode ser menor que 0");
				}
			}
		}
	}

	public function generateTokenCSRF(string $ip_request): string
	{
		$token = bin2hex(random_bytes(32));
		$table = 'token_csrf';

		if (!!$this->database->select('token', $table, "ip = '$ip_request'"))
			$this->database->update($table, ['token' => $token], "ip = '$ip_request'");
		else
			$this->database->insert($table, ['ip' => $ip_request, 'token' => $token]);

		$this->database->commit();

		return $token;
	}

	public function sendWPPMessage()
	{

		$url = "https://graph.facebook.com/v18.0/110748565214335/messages";
		$body = [
			"messaging_product" => "whatsapp",
			"to" => "5511983856319",
			"type" => "image",
			"image" => [
				"link" => "https://h-simcepi.smsprefeiturasp.com.br/appteste/teste"
			]
			// "template" => [
			// "name" => "hello_world", 
			// "language" => [
			// "code" => "en_US"
			// ],
			// ]
		];

		$curl_options = array(
			CURLOPT_URL => $url,
			CURLOPT_HTTPHEADER => [
				"Authorization: Bearer " . ENV['WHATSAPP_TOKEN'],
				"Content-Type: application/json"
			],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => json_encode($body)
		);
		$ch = curl_init();
		curl_setopt_array($ch, $curl_options);

		$response = curl_exec($ch);
		if (!$response) {
			die("Error: " . curl_error($ch));
		}
		;

		return $response;
	}
	/**
	 * Compara duas datas, e retorna a diferença de tempo entre elas.
	 *
	 * @param string $min Data mais antiga.
	 * @param string $max Data mais recente.
	 * @param string $type [optional] Caso nenhum valor seja atribuído, retorna todas as diferenças de tempo entre as datas.
	 * @return mixed
	 *
	 * @annotation
	 * y-m-d Retorna ano, mês e dia.
	 * y-m   Retorna ano e mês.
	 * y     Retorna ano.
	 * m     Retorna mẽs.
	 * d     Retorna dia.
	 * h-i-s Retorna hora, minuto e segundo.
	 * h-i   Retorna hora e minuto.
	 * h     Retorna hora.
	 * i 	   Retorna minuto.
	 * s 	   Retorna segunto.
	 * days  Retorna a diferença total em dias.
	 */
	public function diffBetweenDatetimes(string $min, string $max, string $type = '')
	{
		$min = new Datetime($min);
		$max = new Datetime($max);
		$diff = $min->diff($max);

		if (!empty($min) && !empty($max)) {
			switch ($type) {
				case 'y-m-d':
					return $diff->y . '-' . $diff->m . '-' . $diff->d;
				case 'y-m':
					return $diff->y . '-' . $diff->m;
				case 'y':
					return $diff->y;
				case 'm':
					return $diff->m;
				case 'd':
					return $diff->d;
				case 'h-i-s':
					return $diff->h . ':' . $diff->i . ':' . $diff->s;
				case 'h-i':
					return $diff->h . ':' . $diff->i;
				case 'h':
					return $diff->h;
				case 'i':
					return $diff->i;
				case 's':
					return $diff->s;
				case 'days':
					return $diff->days;
				default:
					return $diff;
			}
		} else
			return "";
	}

	/**
	 * Formata a string substituindo caracteres especiais
	 * @param string $value
	 * @return string
	 */
	public function format_string(string $value): string
	{
		return preg_replace(array("//", "/á|à|ã|â|ä/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/"), explode(" ", " a A e E i I o O u U n N c"), $value);
	}

	/**
	 * Verifica se o CPF é válido.
	 * @param string $cpf
	 * @return string
	 * @throws Exception 
	 */
	public function isCPF(string $cpf): string
	{
		if (empty($cpf) or strlen($cpf) < 11)
			throw new Exception('O CPF informado está inválido!');
		else if (preg_match('/(\d)\1{10}/', $cpf))
			throw new Exception('O CPF informado está inválido!');
		$cpf = preg_replace('/\D/', '', (string) $cpf);


		$validacao = substr($cpf, 0, 9);

		for ($i = 0; $i < 2; $i++) {
			$calculo = strlen($validacao) + 1;
			$soma = 0;

			for ($c = 0; $c < strlen($validacao); $c++) {
				$soma += $validacao[$c] * $calculo;
				$calculo--;
			}
			$validacao .= $soma % 11 > 1 ? 11 - ($soma % 11) : 0;
		}

		if ($validacao != $cpf)
			throw new Exception('O CPF informado está inválido!');
		return $cpf;
	}

	/**
	 * Verifica se o CPF é válido.
	 * @param string $cpf
	 * @return string
	 * @throws Exception 
	 */
	public function isCNPJ(string $cnpj): string
	{
		if (strlen($cnpj) != 14)
			throw new Exception('O CNPJ informado está inválido!', 400);
		else if (preg_match('/(\d)\1{13}/', $cnpj))
			throw new Exception('O CNPJ informado está inválido!');
		$cnpj = preg_replace('/\D/', '', (string) $cnpj);

		// Valida primeiro dígito verificador
		for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
			$soma += $cnpj[$i] * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}

		$resto = $soma % 11;

		if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
			throw new Exception('O CNPJ informado está inválido!', 400);
		;

		// Valida segundo dígito verificador
		for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
			$soma += $cnpj[$i] * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}

		$resto = $soma % 11;

		if ($cnpj[13] != ($resto < 2 ? 0 : 11 - $resto))
			throw new Exception('O CNPJ informado está inválido!', 400);

		return $cnpj;
	}

	/**
	 * @param string $email
	 * @return string
	 * @throws Exception
	 */
	public function isEmail(string $email): string
	{
		if (empty($email))
			throw new Exception('O EMAIL informado está inválido!');
		else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			throw new Exception('O EMAIL informado está inválido!');

		return filter_var($email, FILTER_SANITIZE_EMAIL);
	}
	// /**
	//  * Valida as variaveis de acordo com o tipo solicitado. Consulte @var

	//  * @param string $type Tipo de validação desejada.
	//  * @param string $length Tamanho permitido pelo banco na coluna.
	//  * @param string|int $val Conteudo para ser validado.
	//  * @param string $msg Mensagem em caso de Exception (informe somente o campo).
	//  * @return true or exception
	//  */
	// public static function helpValidator(string $type, string $length = null, string $table = null, string $key = null, string $val, string $msg)
	// {
	// 	$format_string = function (string $string): string {
	// 		return preg_replace(array("/ /", "/á|à|ã|â|ä/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/"), explode(" ", " a A e E i I o O u U n N c"), $string);
	// 	};

	// 	$exception = "O campo " . strtoupper($msg) . " está incorreto!";
	// 	$empty     = "O campo " . strtoupper($msg) . " não foi preenchido!";
	// 	$unique    = "Já existe um usuário cadastrado com este " . strtoupper($msg) . ".
	// 	Verifique o número digitado, efetue a correção necessária e tente novamente.";

	// 	switch (strtoupper($type)) {
	// 			## Validação de banco de dados
	// 			# Flags
	// 		case 'UNIQUE_KEY':
	// 			$response = DB::select($key, $table, "$key = '$val'");
	// 			if (!empty($response)) throw new Exception($unique, 400);
	// 			break;

	// 		case 'NOT_NULL':
	// 		case 'EMPTY':
	// 			if ($val == "") throw new Exception($empty, 400);
	// 			break;

	// 			# Type DATABASE
	// 		case 'BIT':
	// 		case 'TINY':
	// 		case 'TINYINT':
	// 			if (!ctype_digit($val) || $val < 0 || $val > 127) throw new Exception($exception, 400);
	// 			break;

	// 		case 'SHORT':
	// 		case 'SMALLINT':
	// 			if (!ctype_digit($val) || $val < 0 || $val > 32767) throw new Exception($exception, 400);
	// 			break;

	// 		case 'INT24':
	// 		case 'MEDIUMINT':
	// 			if (!ctype_digit($val) || $val < 0 || $val > 8388607) throw new Exception($exception, 400);
	// 			break;

	// 		case 'LONG':
	// 		case 'INT':
	// 		case 'INTEGER':
	// 			if (!ctype_digit($val) || $val < 0 || $val > 2147483647) throw new Exception($exception, 400);
	// 			break;

	// 		case 'LONGLONG':
	// 		case 'BIGINT':
	// 			if (!ctype_digit($val) || $val < 0 || $val > 9223372036854775807) throw new Exception($exception, 400);
	// 			break;

	// 		case 'BOOLEAN':
	// 			$val = $val == '1' ? true : ($val == '0' ? false : $val);
	// 			if (!is_bool($val)) throw new Exception($exception, 400);
	// 			break;

	// 		case 'BLOB':
	// 		case 'STRING':
	// 		case 'VAR_STRING':
	// 			$val = $format_string($val);
	// 			if (!is_null($length) && strlen($val) > $length) throw new Exception($length, 400);
	// 			break;

	// 		case 'DATE':
	// 		case 'DATETIME':
	// 		case 'TIME':
	// 		case 'TIMESTAMP':
	// 			if (!DateTime::createFromFormat('Y-m-d', $val)) throw new Exception($exception, 400);
	// 			// else if ($val > date('Y-m-d H:i:s')) throw new Exception($exception, 400);
	// 			break;
	// 			##

	// 			## Validação de caracteres
	// 		case 'NOME':
	// 		case 'ALPHA':
	// 			$val = $format_string($val);
	// 			if (!preg_replace('/\s+/', '', ctype_alpha($val))) throw new Exception("Não é permitido o uso de caracteres especiais para o campo '$msg'!", 400);
	// 			break;

	// 		case 'ALNUM':
	// 			$val = $format_string($val);
	// 			if (!ctype_alnum($val)) throw new Exception("O campo $msg deve conter somente letras e números!", 400);
	// 			break;

	// 		case 'ONLYFLOAT':
	// 			$val = preg_match("/\./", $val) ? (float) $val : $val;
	// 			if (!is_float($val)) throw new Exception("O campo $msg deve conter somente números flutuantes!", 400);
	// 			break;
	// 			##

	// 			## Validação de Documentos
	// 		case 'CPF':
	// 			if (empty($val) or strlen($val) < 11) throw new Exception('CPF inválido!', 400);
	// 			if (preg_match('/(\d)\1{10}/', $val)) throw new Exception('CPF inválido!', 400);
	// 			$cpf = preg_replace('/\D/', '', $val);


	// 			$validacao = substr($cpf, 0, 9);

	// 			for ($i = 0; $i < 2; $i++) {
	// 				$calculo = strlen($validacao) + 1;
	// 				$soma = 0;

	// 				for ($c = 0; $c < strlen($validacao); $c++) {
	// 					$soma += $validacao[$c] * $calculo;
	// 					$calculo--;
	// 				}
	// 				$validacao .= $soma % 11 > 1 ? 11 - ($soma % 11) : 0;
	// 			}

	// 			if ($validacao != $cpf) throw new Exception('CPF inválido!', 400);
	// 			return $cpf;
	// 			break;

	// 		case 'CNPJ':
	// 			$cnpj = preg_replace('/[^0-9]/', '', (string) $val);
	// 			$invalidos = [
	// 				'00000000000000',
	// 				'11111111111111',
	// 				'22222222222222',
	// 				'33333333333333',
	// 				'44444444444444',
	// 				'55555555555555',
	// 				'66666666666666',
	// 				'77777777777777',
	// 				'88888888888888',
	// 				'99999999999999'
	// 			];
	// 			if (strlen($cnpj) != 14) throw new Exception('O CNPJ informado está inválido!', 400);
	// 			if (in_array($cnpj, $invalidos)) throw new Exception('O CNPJ informado está inválido!', 400);

	// 			// Valida primeiro dígito verificador
	// 			for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
	// 			{
	// 				$soma += $cnpj[$i] * $j;
	// 				$j = ($j == 2) ? 9 : $j - 1;
	// 			}

	// 			$resto = $soma % 11;

	// 			if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
	// 				throw new Exception('O CNPJ informado está inválido!', 400);;

	// 			// Valida segundo dígito verificador
	// 			for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
	// 			{
	// 				$soma += $cnpj[$i] * $j;
	// 				$j = ($j == 2) ? 9 : $j - 1;
	// 			}

	// 			$resto = $soma % 11;

	// 			if($cnpj[13] != ($resto < 2 ? 0 : 11 - $resto))
	// 				throw new Exception('O CNPJ informado está inválido!', 400);

	// 			break;
	// 			// $validacao = substr($cnpj, 0, 12);

	// 			// for ($i = 0; $i < 2; $i++) {
	// 			// 	$calculo = 9;
	// 			// 	$soma = 0;

	// 			// 	for ($c = (strlen($validacao) - 1); $c >= 0; $c--) {
	// 			// 		$soma += $validacao[$c] * $calculo;
	// 			// 		$calculo--;
	// 			// 		$calculo = $calculo < 2 ? 9 : $calculo;
	// 			// 	}
	// 			// 	$validacao .= $soma % 11;
	// 			// }

	// 			// if ($validacao != $cnpj) throw new Exception('CNPJ inválido!', 400);
	// 			// return $cnpj;
	// 			// break;

	// 		case 'EMAIL':
	// 			if (empty($val)) throw new Exception('O campo EMAIL está em branco!', 400);

	// 			$email = filter_var($val, FILTER_SANITIZE_EMAIL);
	// 			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Email inválido!', 400);
	// 			break;

	// 		case 'PASSWORD':
	// 			$forcePassword = ['letras maiúsculas' => '[A-Z]', 'letras minúsculas' => '[a-z]', 'números' => '[0-9]', 'caracteres especiais' => '[-!$%#^&*()@_+{}~=`\[\]:;<>?.,|\'\"]'];
	// 			foreach ($forcePassword as $exception => $force) {
	// 				preg_match("/$force/", $val, $matches);
	// 				if (empty($matches)) throw new Exception("A senha deve conter $exception!");
	// 			}
	// 			if (strlen($val) < $length) throw new Exception("A senha deve conter um mínimo de $length caracteres!");
	// 			break;

	// 		case str_starts_with($type, 'TELEFONE'):
	// 			$val = preg_replace('/\D/', '', $val);
	// 			if (strlen($val) < 10) throw new Exception("O $msg está incompleto!");
	// 			break;
	// 	}
	// }

	// /**
	//  * Recupera a estrutura da tabela
	//  *
	//  * @param array $post Requisição HTTP POST da aplicação. (As chaves devem ser iguais as colunas da tabela no banco)
	//  * @param array $message Mensagem de erro. Ex.: ["nome" => "NOME"].
	//  * @param string $table Tabela requisitada.
	//  * @return true or exception
	//  */
	// public static function dbValidation(array $post, array $exception, string $table)
	// {
	// 	$meta = DB::getColumnMeta($table);
	// 	foreach ($post as $k => $v) {
	// 		foreach ($meta as $m) {
	// 			if ($k == $m['name'] and in_array($k, array_keys($exception))) {
	// 				## Valida os tipos da tabela (ex.: VARCHAR, INT, DATE...)
	// 				self::helpValidator(strtoupper($m['native_type']), $m['len'], null, null, $v, $exception[$k]);

	// 				## Valida de acordo com o nome da tabela (ex.: cpf, email, cnpj...)
	// 				self::helpValidator(strtoupper($m['name']), null, null, null, $v, $exception[$k]);

	// 				## Valida as flags (ex.: not null, unique...)
	// 				foreach ($m['flags'] as $flag) {
	// 					self::helpValidator(strtoupper($flag), null, $table, $k, $v, $exception[$k]);
	// 				}
	// 				break;
	// 			}
	// 		}
	// 	}
	// }

	// /**
	//  * Gera uma senha aleatória junto com o hash da mesma.
	//  *
	//  * Utiliza os seguintes argumentos.
	//  * @var chars 1234567890abcdefghijklmnopqrstuvwxyz
	//  * @var specialChars !@#$&
	//  *
	//  * @return array
	//  */
	public function genPass(int $length = 15): array
	{
		$chars = '1234567890abcdefghijklmnopqrstuvwxyz';
		$specialChars = '!@#$&';

		$lenChars = strlen($chars) - 1;
		$lenSpecialChars = strlen($specialChars) - 1;
		$pass = "";

		for ($i = 0; $i < $length; $i++) {
			$pass .= $i % 5 != 0 ? $chars[rand(0, $lenChars)] : $specialChars[rand(0, $lenSpecialChars)];
		}

		$hash = password_hash($pass, PASSWORD_DEFAULT);
		return array('pass' => $pass, 'hash' => $hash);
	}


	/**
	 * Envia o email
	 *
	 * @param string $message Corpo da mensagem. Aceita tags HTML.
	 * @param array $recipient Email do destinatário.
	 * @param array $cc Email de quem receberá em copia.
	 * @param array $cco Email de quem receberá em copia oculta.
	 * @return true or exception.
	 */
	public function sendMail(
		string $title,
		string $message,
		array $recipient,
		array $cc = [],
		array $cco = [],
		bool $includeAttachment = false,
		array $attachmentPath = [],
		bool $includeImage = false,
		string $imagePath = ""
	) {
		$mail = new PHPMailer(true);

		# SMTP settings
		$mail->CharSet = "utf-8";
		$mail->isSMTP();
		// $mail->SMTPDebug = SMTP::DEBUG_SERVER;

		## Config GMAIL
		$mail->Host = ENV['EMAIL_HOST'];
		$mail->SMTPAuth = ENV['EMAIL_SMTP_AUTH'];
		$mail->Username = ENV['EMAIL_USERNAME'];
		$mail->Password = ENV['EMAIL_PASSWORD'];
		$mail->SMTPAutoTLS = ENV['EMAIL_SMTP_AUTO_TLS'];
		$mail->SMTPSecure = ENV['EMAIL_SMTP_SECURE'];
		$mail->Port = ENV['EMAIL_PORT'];
		$mail->setFrom(ENV['EMAIL_SET_FROM']);

		foreach ($recipient as $r)
			$mail->addAddress(strtolower($r));

		foreach ($cc as $rec)
			$mail->addCC(strtolower($rec));

		foreach ($cco as $rec)
			$mail->addBCC(strtolower($rec));

		//Content
		$mail->isHTML(true);
		$mail->Subject = $title;

		if ($includeAttachment) {
			foreach ($attachmentPath as $attachment)
				$mail->addAttachment($attachment);
		}

		if ($includeImage) {
			$mail->AddEmbeddedImage($imagePath, 'image');
			$mail->Body = "<img src='cid:image'> <br><br>" . $message;
		} else
			$mail->Body = $message;

		try {
			$mail->send();
			return true;
		} catch (PHPMailerException $e) {
			$this->logger->error("[Helper - ID {$this->USER->data->id} IP " . IP . "]", ["message" => $e->getMessage(), "code" => $e->getCode(), "file" => $e->getFile(), "line" => $e->getLine()]);
			throw new PHPMailerException('Erro ao tentar enviar o email! Tente novamente mais tarde.', 400);
		}
	}

	/**
	 * @param string $file
	 * @return StreamInterface
	 */
	private static function streamFile(string $file): StreamInterface
	{
		$stream = fopen($file, 'r+');
		unlink($file);

		if ($stream === false)
			throw new CustomDomainException('[Helper (OUTPUTFILE)] - Erro ao tentar abrir o arquivo.');

		return new Stream($stream);
	}

	// /**
	//  * Retorna o ultimo erro da aplicação.
	//  *
	//  * @param Request $request Request da requisição HTTP.
	//  * @param Response $response Reponse da requisição HTTP.
	//  * @param mix $content Conteudo como resposta da requisição.
	//  */
	// public static function error(Exception $e)
	// {
	// 	$response = new newResponse();
	// 	$response->getBody()->write(json_encode(array('message' => $e->getMessage(), 'code' => $e->getCode(), JSON_UNESCAPED_UNICODE)));
	// 	return $response->withStatus(200);
	// }

	// /**
	//  * Mostra o console do conteudo
	//  *
	//  * @param string/array/int $msg Conteudo do console.
	//  * @return string
	//  */
	// public static function debug($msg)
	// {
	// 	echo "<pre>";
	// 	var_dump($msg);
	// 	exit;
	// }


	// /**
	//  * Formata o conteudo no modelo HTML -
	//  * Função para criar modelo de download em PDF e Excel
	//  *
	//  * @param array $data Conteúdo para formar o HTML
	//  * @return string
	//  */
	// public static function HTMLContent(array $data): string
	// {
	// 	$html = "";

	// 	if (!empty($data)) {
	// 		array_unshift($data, array_keys($data[0]));
	// 		$html .= "<table>";

	// 		$html .= "<tr>";
	// 		// foreach($data[0] as $key => $value){
	// 		// 	@$html .= "<th> $key </th>";
	// 		// }
	// 		// $html .= "</tr>";


	// 		foreach ($data as $d) {
	// 			$html .= "<tr>";

	// 			#VERIFICA SE É ARRAY DENTRO DE ARRAY E CRIA OUTRO LOOP
	// 			if (is_array($d)) {
	// 				foreach ($d as $v) {
	// 					@$html .= "<td> $v </td>";
	// 				}
	// 				$html .= "</tr>";
	// 			}

	// 			#CASO NAO, FAZ UM LOOP NORMAL
	// 			else {
	// 				$html .= "<td> $d </td>";
	// 			}
	// 			$html .= "</tr>";
	// 		}
	// 		$html .= "</table>";
	// 	}

	// 	return $html;
	// }

	// /**
	//  * Formata o conteúdo no modelo de CSV
	//  *
	//  * @param array $data Conteúdo para formar o csv.
	//  * @return string
	//  */
	// public static function CSVContent(array $data): string
	// {
	// 	$csv = "";

	// 	if (!empty($data)) {
	// 		array_unshift($data, array_keys($data[0]));

	// 		if (is_array($data)) {
	// 			foreach ($data as $d) {
	// 				@$csv .= implode(';', $d) . "<br>";
	// 			}
	// 		} else @$csv .= implode(';', $data) . "<br>";
	// 	}

	// 	return $csv;
	// }

	// /**
	//  * Retorna o conteúdo do arquivo.
	//  * Formatos aceitos: XLS, XLSX, CSV
	//  *
	//  * @param string $file Arquivo desejado.
	//  * @return array
	//  */
	// public static function readFile($file)
	// {
	// 	$identity = strtolower(IOFactory::identify($file));
	// 	$read = function ($file) {
	// 		return IOFactory::load($file)->getActiveSheet()->toArray();
	// 	};

	// 	switch ($identity) {
	// 		case 'html':
	// 			return $read($file .= 'x');
	// 		default:
	// 			return $read($file);
	// 	}
	// }

	/**
	 * @param array $data
	 * @param string $filename
	 * @param string $extension
	 * @param Response $response
	 * @param string $output 'F' return File, 'D' return download Browser
	 * @return Response
	 */
	public function BoxSpout(array $data, string $filename, string $extension, Response $response, string $output = 'D'): Response
	{
		switch ($extension) {
			case 'xlsx':
				$writer = WriterEntityFactory::createXLSXWriter();
				$defaultStyle = (new StyleBuilder())
					->setFontName('Arial')
					->setFontSize(11)
					->build();
				$writer->setDefaultRowStyle($defaultStyle);
				break;
			case 'csv':
				$writer = WriterEntityFactory::createCSVWriter();
				$writer->setShouldAddBOM(true);
				$writer->setFieldDelimiter(';');
				$writer->setFieldEnclosure('"');
				break;
		}
		$filename     = pathinfo($filename, PATHINFO_FILENAME) . '.' . $extension;
		$tempFilename = tempnam(sys_get_temp_dir(), pathinfo($filename, PATHINFO_FILENAME)) . '.' . $extension;
		$writer->openToFile($tempFilename);

		foreach ($data as $key => $content) {
			$values = array_values($content);
			$rowFromValues = WriterEntityFactory::createRowFromArray($values);
			$writer->addRow($rowFromValues);
		}

		$writer->close();
		
		$stream = self::streamFile($tempFilename);

		if($output == 'F')
			return $response->withBody($stream);
		
		else if($output == 'D')
			return $response
			->withBody($stream)
			->withHeader('Content-Type', 'application/octet-stream')
			->withHeader('Content-Disposition', 'attachment; filename="' . basename($filename) . '"');
	}

	/**
	 * Retorna um arquivo no formato desejado. Formatos aceitos XLS, XLSX, TSV, CSV, PDF
	 *
	 * @param array $data Conteúdo para popular o arquivo.
	 * @param string $filename Nome do arquivo.
	 * @param string $extension Tipo de extensao do arquivo. Exemplo 'xls'
	 * @param string $path
	 * [optional]
	 * Por padrão iŕa salvar na raiz do projeto.
	 * @return file
	 */
	// public static function createFile(array $data, string $filename, string $extension)
	// {
	// 	#Exemplo de como enviar os dados
	// 	// $data = [['NOME', 'COR', 'IDADE'], ['TESTE', 'PARDO', '20']];

	// 	#OUTPUT DOS ARQUIVOS
	// 	$output = function ($filename) {
	// 		$path = basename($filename);
	// 		return $path;
	// 		// ob_clean();
	// 		// flush();
	// 		// readfile($path);
	// 		// unlink($path);
	// 		// exit;
	// 	};
	// 	$spreadsheet = new Spreadsheet();
	// 	\PhpOffice\PhpSpreadsheet\Settings::setLocale('pt_br');
	// 	$sheet = $spreadsheet->getActiveSheet();
	// 	$sheet->fromArray($data, NULL);

	// 	switch ($extension) {
	// 		case 'xls':
	// 			$writer = new Xls($spreadsheet);
	// 			$writer->setPreCalculateFormulas(false);
	// 			$writer->save("$filename");
	// 			return $output($filename);

	// 		case 'xlsx':
	// 			$writer = new Xlsx($spreadsheet);
	// 			$writer->setPreCalculateFormulas(false);
	// 			$writer->setOffice2003Compatibility(true);
	// 			$writer->save("$filename");
	// 			return $output($filename);

	// 		case 'tsv':
	// 		case 'csv':
	// 			$writer = new Csv($spreadsheet);
	// 			$writer->setUseBOM(true);
	// 			// $writer->setOutputEncoding('ISO-8859-1');
	// 			$writer->setDelimiter(';');
	// 			$writer->setEnclosure('"');
	// 			$writer->setLineEnding("\r\n");
	// 			$writer->save("$filename");
	// 			return $output($filename);

	// 		case 'pdf':
	// 			$pdf = self::HTMLContent($data);

	// 			$dompdf = new Dompdf();
	// 			$dompdf->loadHtml($data);
	// 			$dompdf->setPaper('A4', 'landscape');
	// 			$dompdf->render();
	// 			$dompdf->stream("$filename");
	// 		#DOWNLOAD
	// 		// $dompdf->stream();

	// 		#APARECE NA ABA DO NAVEGADOR
	// 		// header('Content-type: application/pdf');
	// 		// echo $dompdf->output();

	// 	}
	// }

	// /**
	//  * Convert html to excel file
	//  * @param $html HTML content
	//  * @param $filename File name
	//  * @param $img If your file go to have a file
	//  * @param $imgOptions Options to your image [
	//  * 	name => '',
	//  *  description => '',
	//  *  pathImage => '',
	//  *  coordinate => A1,
	//  *  offsetX => 0,
	//  *  offsetY => 0,
	//  *  rotation => 0,
	//  * ]
	//  */
	// public static function HTMLtoExcel(string $html, string $filename, bool $img = false, array $imgOptions = []){
	// 	\PhpOffice\PhpSpreadsheet\Settings::setLocale('pt_br');
	// 	$reader = new Html();
	// 	$spreadsheet = $reader->loadFromString($html);
	// 	$sheet = $spreadsheet->getActiveSheet();
	// 	$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
	// 	foreach(range('A', 'Z') as $column)
	// 		$sheet->getColumnDimension($column)->setAutoSize(true);

	// 	if($img){
	// 		$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
	// 		$drawing->setName(isset($imgOptions['name']) ? $imgOptions['name'] : '');
	// 		$drawing->setDescription(isset($imgOptions['description']) ? $imgOptions['description'] : '');
	// 		$drawing->setPath(isset($imgOptions['pathImage']) ? $imgOptions['pathImage'] : '');
	// 		$drawing->setCoordinates(isset($imgOptions['coordinate']) ? $imgOptions['coordinate'] : '');
	// 		$drawing->setOffsetX(isset($imgOptions['offsetX']) ? $imgOptions['offsetX'] : '');
	// 		$drawing->setOffsetY(isset($imgOptions['offsetY']) ? $imgOptions['offsetY'] : '');
	// 		$drawing->setRotation(isset($imgOptions['rotation']) ? $imgOptions['rotation'] : '');
	// 		$drawing->getShadow()->setVisible(true);
	// 		$drawing->getShadow()->setDirection(45);
	// 		$drawing->setWorksheet($sheet);
	// 	}

	// 	$writer = new Xlsx($spreadsheet);
	// 	$writer->setPreCalculateFormulas(false);
	// 	$writer->setOffice2003Compatibility(true);
	// 	$writer->save($filename);
	// }

	/**
	 * Metodo dinamico responsavel por exportar os dados em arquivos
	 */
	// public static function exportFile(Request $request, Response $response, array $args)
	// {
	// 	try {
	// 		// Auth::refresh_token($request, $response);
	// 		$USER = $request->getAttribute('USER');

	// 		#Pega as configurações
	// 		$config = Query::dataExport(null, $USER)[$args['query']];

	// 		#Roda a query
	// 		$content = DB::runSelect($config['query']);
	// 		if(!empty($content)) array_unshift($content, array_keys($content[0]));

	// 		$filename = $config['filename'] . '.' . $args['filetype'];
	// 		Helper::createFile($content, $filename, $args['filetype']);

	// 		return $response->withBody(self::outputFile($filename))->withStatus(200);
	// 	} catch (Exception $e) {
	// 		return Helper::error($e);
	// 	}
	// }

	// /**
	//  * Envia uma solicitação para a api do google
	//  *
	//  * @param string $token Recebe o token para validar.
	//  * @return true or false.
	//  */
	// public static function reCaptchaV3(string $token){
	// 	#Front manda o token

	// 	$secretKey = ENV['SECRET_KEY_RECAPTCHAV3'];
	// 	$url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$token";

	// 	#Inicia o curl
	// 	$init = curl_init($url);

	// 	#Convert para array
	// 	curl_setopt($init, CURLOPT_RETURNTRANSFER, true);

	// 	#Desabilita o ssl
	// 	curl_setopt($init, CURLOPT_SSL_VERIFYPEER, false);

	// 	#Executa o curl e converte para objeto
	// 	$response = json_decode(curl_exec($init));

	// 	// #Verifica se existe erro na resposta
	// 	if(isset($response)){
	// 		if(!$response->success) throw new Exception('Houve um problema na validação do seu login! Atualize a página e tente novamente (caso o problema persista, entre em contato com o suporte).', 400);
	// 		else if(@$response->score < 0.5) throw new Exception('Houve um problema na validação do seu login! Atualize a página e tente novamente (caso o problema persista, entre em contato com o suporte).', 400);
	// 	}

	// 	return Helper::success('OK');
	// }

	/**
	 * Retorna o pdf para download
	 * @param string $html HTML do conteúdo
	 * @param string $filename Nome do arquivo
	 * @param bool $browser Retorna no navegador ou como download (true or false)
	 * @return Dompdf 
	 */
	// public static function DOMPDF(string $html, string $filename, bool $browser = false, string $orientation = 'landscape', string $font = 'Arial')
	// {
	// 	$filename = pathinfo($filename, PATHINFO_FILENAME) . '.pdf';
	// 	$options = new Options();
	// 	$options->set('defaultFont', $font);
	// 	$options->set('enableRemote', true);
	// 	$options->set('chroot', __DIR__);

	// 	$dompdf = new Dompdf($options);
	// 	$dompdf->setBasePath(__DIR__);
	// 	$dompdf->loadHtml($html);
	// 	$dompdf->setPaper('A4', $orientation);
	// 	$dompdf->render();

	// 	#DOWNLOAD
	// 	if (!$browser) {
	// 		// $dompdf->stream($filename);
	// 		$output = $dompdf->output();
	// 		file_put_contents($filename, $output);
	// 	}

	// 	#APARECE NA ABA DO NAVEGADOR
	// 	else {
	// 		header('Content-type: application/pdf');
	// 		echo $dompdf->output();
	// 	}
	// }


	/**
	 * @param string $html Conteudo html a ser convertido em PDF
	 * @param string $filename Nome do arquivo
	 * @param string $dest ino Destino da conversão ("F" - Salvar diretamente na raiz do projeto, "D" - Download forçado no browser)
	 */
	public function MPDF(string $html, string $filename, string $orientation = 'P', $dest = 'F')
	{
		$filename = pathinfo($filename, PATHINFO_FILENAME) . '.pdf';
		$mpdf = new Mpdf([
			'mode' => 'utf-8',
			'orientation' => $orientation,
			'format' => "A4-$orientation",
			'simpleTables' => true,
			'packTableData' => true
		]);

		$totalLength = strlen($html);
		$limitBacktrace = (int) ini_get('pcre.backtrack_limit');

		if ($totalLength > $limitBacktrace) {
			for ($offset = 0; $offset < $totalLength; $offset += $limitBacktrace) {
				$part = substr($html, $offset, $limitBacktrace);
				$mpdf->WriteHTML($part);
			}
		} else
			$mpdf->WriteHTML($html);
		$mpdf->Output($filename, $dest);
	}

	// /**
	//  * @param string $nameZip Nome do arquivo. (Não necessita colocar a extensão).
	//  * @param string $pathToFile Caminho ate a pasta dos arquivos.
	//  * @param array $files
	//  * [optional]
	//  * Define quais arquivos da pasta ira pegar.
	//  * @param string $dirInZip
	//  * [optional]
	//  * Pasta para salvar os arquivos dentro do ZIP.
	//  * @param bool $unlink
	//  * [optional]
	//  * Caso seja verdadeira, irá remover os arquivos da pasta!
	//  * @return file
	//  */
	// public static function ZIP(string $nameZip, $pathToFile, array $files = null, string $dirInZip = null, bool $unlink = false)
	// {
	// 	$zip = new ZipArchive();
	// 	$nameZip = pathinfo($nameZip, PATHINFO_FILENAME) . '.zip';
	// 	$storageFiles = [];

	// 	if ($zip->open($nameZip, ZipArchive::CREATE) == TRUE) {

	// 		if (!is_null($files)) {
	// 			foreach ($files as $file) {
	// 				$zip->addEmptyDir($dirInZip);
	// 				if(is_array($pathToFile)){
	// 					foreach($pathToFile as $path){
	// 						$dh = opendir($path);
	// 						while ($f = readdir($dh)) {
	// 							if (is_file("$path/$file") && $f === $file) {
	// 								$zip->addFile("$path/$file", "$dirInZip/$file");
	// 								$zip->renameName("$dirInZip/$file", explode('---', $file)[0].'.pdf');
	// 								array_push($storageFiles, "$path/$file");
	// 							}
	// 						}
	// 					}
	// 				}
	// 				else{
	// 					$dh = opendir($pathToFile);
	// 					while ($f = readdir($dh)) {
	// 						if (is_file("$pathToFile/$file") && $f === $file) {
	// 							$zip->addFile("$pathToFile/$file", "$dirInZip/$file");
	// 							$zip->renameName("$dirInZip/$file", explode('---', $file)[0].'.pdf');
	// 							array_push($storageFiles, "$pathToFile/$file");
	// 						}
	// 					}
	// 				}
	// 			}
	// 		} else {
	// 			$zip->addEmptyDir($dirInZip);
	// 			if(is_array($pathToFile)){
	// 				foreach($pathToFile as $path){
	// 					$dh = opendir($path);
	// 					while ($file = readdir($dh)) {
	// 						if (is_file("$path/$file")) {
	// 							$zip->addFile("$path/$file", "$dirInZip/$file");
	// 							array_push($storageFiles, "$path/$file");
	// 						}
	// 					}
	// 				}
	// 			}
	// 		}
	// 		$zip->close();

	// 		## Remover os arquivos
	// 		if ($unlink) {
	// 			foreach ($storageFiles as $f) {
	// 				$f = str_replace('//', '/', $f);
	// 				unlink($f);
	// 			}
	// 		}
	// 	} else throw new Exception('Erro ao abrir o arquivo zip!', 400);
	// }


	// /**
	//  * Armazena os arquivos recebidos
	//  *
	//  * @param Request $request Request da aplicação.
	//  * @param string $path Caminho para salvar o arquivo.
	//  */
	// public static function saveUpload(Request $request, string $path = './'){
	// 	$USER = $request->getAttribute('USER');

	// 	## Faz a validação e move os arquivos
	// 	$upload = function (UploadedFileInterface $file, string $path, object $USER) {
	// 		if ($file->getError() !== UPLOAD_ERR_OK) throw new Exception('Falha ao salvar arquivo! O arquivo pode estar danificado ou corrompido.', 400);

	// 		## Verifica extensao permitida
	// 		$extension = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
	// 		if ($extension != 'doc' and $extension != 'pdf') throw new Exception('O arquivo não é do tipo PDF.', 400);

	// 		## Verifica o tamanho maximo permitido
	// 		if (strlen($file->getSize()) == 5) {
	// 			$mb = ceil($file->getSize() / pow(floatval('1024,4'), 2));
	// 			if ($mb > 5) throw new Exception("O arquivo não pode ter mais de 5mb!", 400);
	// 		}

	// 		// $basename = bin2hex(random_bytes(8));
	// 		// $filename = sprintf('%s.%0.8s', );
	// 		$filename = pathinfo($file->getClientFilename(), PATHINFO_FILENAME) . '---' . date('d-m-Y H:i:s') . '-' .$USER->idLogin . ".$extension";
	// 		$filename = self::format_string($filename);
	// 		$file->moveTo($path . $filename);
	// 		return $filename;
	// 	};

	// 	// $files = $request->getUploadedFiles()['file'];
	// 	// if(is_array($files)){
	// 	// 	foreach($files as $f){
	// 	// 		$total_files[] = $upload($f, $path);
	// 	// 	}
	// 	// }else{
	// 	// 	$total_files[] = $upload($files, $path);
	// 	// /}
	// 	$files = $request->getUploadedFiles()['file'];

	// 	foreach ($files as $name => $file) {
	// 		foreach ($file as $value) {
	// 			if (!in_array($value->getClientFilename(), scandir(ENV['DIR_UPLOAD'])))
	// 				$total_files[$name][] = $upload($value, $path, $USER);

	// 			else
	// 				$total_files[$name][] = $value->getClientFilename();
	// 		}
	// 	}
	// 	return $total_files;
	// }

	// /**
	//  * Retorna o resultado da pesquisa.
	//  *
	//  * @param Request $request Request da requisição HTTP.
	//  * @param Response $response Response da requisição HTTP.
	//  *
	//  * @var column Colunas a serem recuperadas da tabela como resultado.
	//  * @var table  Tabela destinada.
	//  * @var search Coluna usada como filtro da pesquisa.
	//  * @var value  Valor a ser procurado na tabela.
	//  * @var and    Complemento de filtro.
	//  * @var group  Qual coluna sera usada como agrupamento.
	//  * @var order  Coluna e Sentido da ordenação
	//  * @var limit  Limite de resultados
	//  * @return array
	//  */
	// public static function autocomplete(Request $request, Response $response): Response
	// {
	// 	try {
	// 		$form = self::post();

	// 		## Verifica se o autocomplete é autenticado ou nao
	// 		$routeContext = RouteContext::fromRequest($request);
	// 		$route = $routeContext->getRoute()->getName();

	// 		if (!empty($form)) {
	// 			$content = DB::select(
	// 				$form['column'],
	// 				$form['table'],
	// 				"{$form['search']} LIKE '%{$form['value']}%' AND ativo = 1",
	// 				$form['and'],
	// 				$form['group'],
	// 				$form['order'],
	// 				$form['limit']
	// 			);
	// 			$response->getBody()->write(json_encode($content, JSON_UNESCAPED_UNICODE));
	// 		}

	// 		return $response->withStatus(200);
	// 	} catch (Exception $e) {
	// 		$content = $e->getMessage();
	// 		return Helper::error($e);
	// 	}
	// }

	// /**
	//  * @TRANSFORMA IMAGENS NO TIPO BASE64
	//  */
	// public static function image64($path = './')
	// {
	// 	$type = pathinfo($path, PATHINFO_EXTENSION);
	// 	$data = file_get_contents($path);
	// 	return 'data:image/' . $type . ';base64,' . base64_encode($data);
	// }

	// /**
	//  * Utilizado para retornar os dados ao DataTable
	//  *
	//  * @param string|int $id ID para limitar a buscar somente ao usuário logado.
	//  * @param string $query Nome da função que retorna a query.
	//  * @return array
	//  */
	// public static function dataTable($id, string $query): array{

	// 	$page = self::post()['start'];
	// 	$rowsPerPage = self::post()['length'];
	// 	$orderBy = self::post()['columns'][self::post()['order'][0]['column']]['data'];
	// 	$orderWay = self::post()['order'][0]['dir'];
	// 	$search = self::post()['search']['value'];

	// 	## Limite
	// 	// $rowsPerPage += $page;

	// 	$sql        = Query::$query($id, $search);
	// 	$total_rows = DB::runRow($sql);

	// 	$sql_filtered = Query::$query($id, $search, strtolower($orderBy), $orderWay, [$page, $rowsPerPage]);

	// 	$data         = DB::runSelect($sql_filtered);
	// 	$total_rows_filtered = DB::runRow($sql_filtered);

	// 	return ['data' => $data, 'recordsTotal' => $total_rows_filtered, 'recordsFiltered' => $total_rows];
	// }

	/**
	 * @param Request $request
	 * @return array
	 */
	public function post(Request $request): array
	{
		return $this->removeSQLINJECTION($request->getParsedBody());
	}

	// public function exportZip(Request $request, Response $response): Response {

	// 	$export = function(): StreamInterface{
	// 		$form = self::post();

	// 		$response = array_filter(DB::select(
	// 			'ementa_disciplina, autorizacao_aplicavel_curso, apresentacao_responsavel_curso, 
	// 			valores_referenciais, certidao_tributos_mobiliarios, credenciamento_residencia_mec, 
	// 			comprovantes_matricula_mec_ms, comprovante_residencia_cnes',
	// 			'tb_aditamento_solicitacao_vaga_estagio',
	// 			"id = '{$form['id']}'"
	// 		)[0]);

	// 		# separa file multiple
	// 		$files = [];
	// 		if (!empty($response['comprovantes_matricula_mec_ms'])) {
	// 			$files = json_decode($response['comprovantes_matricula_mec_ms']);
	// 			unset($response['comprovantes_matricula_mec_ms']);
	// 		}

	// 		foreach ($response as $value) {
	// 			array_push($files, $value);
	// 		}

	// 		$nameZip = 'Anexos - ' . date('Y-m-d H:i:s');
	// 		Helper::ZIP($nameZip, [ENV['DIR_UPLOAD'], ENV['DIR_UPLOAD']."curso_programa/"], $files);

	// 		return Helper::outputFile("$nameZip.zip");
	// 	};

	// 	try {
	// 		return $response->withBody($export())->withStatus(200);
	// 	} catch (Exception $e) {
	// 		return Helper::error($e);
	// 	}
	// }
}