<?php
/**
 * Реализовать авторизацию пользователя через HTML-форму.
 * При авторизации использовать шифрование ключами RSA, которые должны генерироваться при каждой попытке авторизации.
 * На стороне сервера использовать функционал OpenSSL, на стороне клиента - WebCryptoAPI или сторонние библиотеки.
 */

// Сомневаюсь, что понял задачу правильно, никогда ранее не делал ничего подобного.
// Набросал ниже как понял:
//
// 1) При каждом обращении к скрипту генерируется пара ключей публичный-приватный
// 2) Публичный передаем в форму как data-атрибут, приватный пишем в сессию
// 3) При POST-запросе от клиента получаем имя пользователя и зашифрованный публичным ключем пароль
// 4) Дешифруем пароль, если не совпадает - генерируем новую пару, далее см. п.2
//
// Задание не закончил на стороне клиента, так как не уверен в правильном выполнении
// и нет желания разбираться с типами шифрования и WebCryptoAPI ради тестового задания

class DB
{
	private static $users = array(
		'user1' => 'password1',
		'user2' => 'password2',
		'user3' => 'password3',
	);

	/**
	 * @param string $username
	 * @return string|false
	 */
	public static function getPassword($username)
	{
		return array_key_exists($username, static::$users) ? static::$users[$username] : false;
	}
}

class Auth
{
	/** @var string */
	private $privateKey;

	/** @var string */
	private $publicKey;

	/** @var string */
	private static $sessionKey = 'privateKey';

	private static $config = array(
		'config' => 'c:/xampp/apache/conf/openssl.cnf',
		"digest_alg" => "sha256",
		"private_key_bits" => 4096,
		"private_key_type" => OPENSSL_KEYTYPE_RSA,
	);

	public function __construct()
	{
		session_start();
	}

	/**
	 * @return string
	 */
	public function getPublicKey()
	{
		return $this->publicKey;
	}

	/**
	 * @return bool
	 */
	public function check()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
			$encryptedPassword = filter_input(INPUT_POST, 'encrypted');
			if ($this->checkPassword($username, $encryptedPassword)) {
				return true;
			}
		}

		// Создать новые ключи
		$this->newKeysPair();

		return false;
	}

	/**
	 * @param string $username
	 * @param string $encryptedPassword
	 *
	 * @return bool
	 */
	private function checkPassword($username, $encryptedPassword)
	{
		$plainPassword = DB::getPassword($username);
		if ($plainPassword === false) {
			return false;
		}

		$privateKey = $_SESSION[static::$sessionKey];
		openssl_private_decrypt($encryptedPassword, $decryptedPassword, $privateKey);

		return ($plainPassword === $decryptedPassword);
	}

	/**
	 * @return void
	 */
	private function newKeysPair()
	{
		$resource = openssl_pkey_new(static::$config);

		// Private key
		openssl_pkey_export($resource, $this->privateKey, null, static::$config);

		// Public key
		$publicKey = openssl_pkey_get_details($resource);
		$this->publicKey = $publicKey["key"];

		$_SESSION['privateKey'] = $this->privateKey;
	}
}

$auth = new Auth();

if ($auth->check()) {
	echo "You are in the Forbidden Zone now!";
} else {
	include 'tpl-form.php';
}
