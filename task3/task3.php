<?php
/**
 * Задание №3
 * Реализовать выполнение запросов в БД MySQL через HTML-форму, используя технологию AJAX.
 * Формат обмена данными с серверной частью - JSON.
 * Результаты выполнения запроса должны выводиться на страницу ниже формы.
 * Можно использовать произвольные фреймворки и библиотеки.
 *
 */

class DatabaseException extends \Exception {}

class DB
{
	private static $user = 'sandbox';
	private static $pass = 'sandbox';
	private static $database = 'sandbox_task3db';

	/** @var DB */
	private static $instance;

	/** @var \mysqli */
	private static $connection;

	/**
	 * @return DB
	 * @throws DatabaseException
	 */
	public static function instance()
	{
		if (null === static::$instance) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * @throws DatabaseException
	 */
	private function __construct()
	{
		static::$connection = new mysqli("localhost", static::$user, static::$pass, static::$database);
		if (static::$connection->connect_errno) {
			throw new DatabaseException("Can't connect to DB!");
		}
	}

	private function __clone() {}

	private function __wakeup() {}

	/**
	 * @param string $search_user
	 * @return array
	 */
	public function getCustomers($search_user)
	{

		if (empty($search_user)) {
			$query = static::$connection->query("SELECT id, username, description, debt FROM customers");
			return $query->fetch_all(MYSQLI_ASSOC);
		}

		$query = static::$connection->prepare("SELECT id, username, description, debt FROM customers WHERE username LIKE CONCAT('%',?,'%')");
		$query->bind_param('s', $search_user);
		$query->execute();

		$result = array();
		foreach ($query->get_result() as $item) {
			$result[] = array_map('strval', $item);
		}

		return $result;
	}
}

class ApiException extends \Exception {}

class Api
{
	/**
	 * @return array
	 * @throws ApiException
	 * @throws DatabaseException
	 */
	public function controller()
	{
		if($_SERVER['REQUEST_METHOD'] !== 'POST') {
			throw new ApiException("Only ajax call is allowed!");
		}

		$search_user = filter_input(INPUT_POST,'username', FILTER_SANITIZE_STRING);
		$data = DB::instance()->getCustomers($search_user);

		return $data;
	}
}

try
{
	$api = new Api();
	$response = $api->controller();
	$status = 200;
}
catch (\Exception $e) {
	$response = array(
		'message' => $e->getMessage()
	);
	$status = 500;
}

header('Content-Type: application/json');
http_response_code($status);
echo json_encode($response);
