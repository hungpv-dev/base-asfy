<?php

define('db_host', $_ENV['DB_HOST']);
define('db_port', $_ENV['DB_PORT']);
define('db_user', $_ENV['DB_USERNAME']);
define('db_pass', $_ENV['DB_PASSWORD']);
define('db_name', $_ENV['DB_NAME']);
define('charset', $_ENV['DB_CHARSET']);

class DB
{
	private static $host = db_host;
	private static $port = db_port;
	private static $user = db_user;
	private static $pass = db_pass;
	private static $dbname = db_name;
	private static $charset = charset;
	protected static $conn = null;

	protected static $sql  = null;
	protected static $table = null;
	protected static $select = "*";
	protected static $join = null;
	protected static $where = null;
	protected static $group_by = null;
	protected static $having = null;
	protected static $order = null;
	protected static $limit = null;

	protected static $data_execute = [];
	protected static $comparison_operator_array = ['=', '>', '<', '>=', '<=', '<>', '!=', 'like', 'LIKE'];

	protected static $result = null;
	protected static $last_sql = null;
	protected static $last_data_execute = [];
	protected static $last_result = null;

	function __construct()
	{
		// static::$connect();	
	}

	public static function connect($config = [])
	{
		if ($config) {
			static::$host = $config['db_host'];
			static::$port = $config['db_port'];
			static::$user = $config['db_user'];
			static::$pass = $config['db_pass'];
			static::$dbname = $config['db_name'];
			static::$charset = $config['charset'];
		}
		if (!static::$conn) {
			$dsn = "mysql:host=" . static::$host . ";port=" . static::$port . ";dbname=" . static::$dbname . ";charset=" . static::$charset;
			$options = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_EMULATE_PREPARES   => false,
			];
			try {
				static::$conn = new PDO($dsn, static::$user, static::$pass, $options);
			} catch (Exception $e) {
				echo "connect failed" . $e->getMessage();
			}
		}
	}

	public static function execute()
	{
		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		static::$result = true;
		static::flushData();
		return true;
	}

	public static function flushData()
	{
		static::$last_sql = static::$sql;
		static::$last_data_execute = static::$data_execute;
		static::$last_result = static::$result;

		static::$data_execute = [];
		static::$sql = null;
		static::$table = null;
		static::$select = "*";
		static::$join = null;
		static::$where = null;
		static::$group_by = null;
		static::$having = null;
		static::$order = null;
		static::$limit = null;
	}

	public static function getSql()
	{
		return static::$last_sql;
	}

	public static function debug()
	{
		echo '<br>';
		echo 'SQL: ' . '<br>' . static::$last_sql . '<br>' . '<br>';
		echo 'Data execute: <br>';
		echo '<pre>';
		var_dump(static::$last_data_execute);
		echo '</pre>';

		echo 'Result: ';
		echo '<pre>';
		var_dump(static::$last_result);
		echo '</pre>';
	}

	public static function error($text = "")
	{
		if ($text) {
			trigger_error($text, E_USER_WARNING);
		}
		$error = mysqli_error(static::$conn);
		if ($error) {
			trigger_error($error, E_USER_WARNING);
		}
	}

	public static function table($table)
	{
		static::$table = $table;
		return new static();
	}

	public static function insert($data = [])
	{
		static::connect();
		$field = "";
		$value = "";
		foreach ($data as $k => $v) {
			$field .= ", $k";
			$value .= ", :$k";
		}
		$field = ltrim($field, ", ");
		$field = "($field)";
		$value = ltrim($value, ", ");
		$value = "($value)";
		static::$sql = "INSERT INTO " . static::$table . " $field VALUES $value";
		static::$data_execute = $data;
		return static::execute();
	}

	public static function lastInsertId()
	{
		return static::$conn->lastInsertId();
	}

	public static function update($data = [])
	{
		static::connect();
		if (static::$where == '') {
			exit('Error syntax method update, please add method where before update');
		}
		$var = "";
		$data_execute = [];
		foreach ($data as $key => $value) {
			$var .= ", $key=?";
			$data_execute[] = $value;
		}
		$var = ltrim($var, ", ");
		foreach (static::$data_execute as $k => $v) {
			$data_execute[] = $v;
		}
		static::$sql = "UPDATE " . static::$table . " SET $var " . static::$where;
		static::$data_execute = $data_execute;
		return static::execute();
	}

	public static function increment($col, $count = 1)
	{
		static::connect();
		if (static::$where == '') {
			exit('Error syntax method increment, please add method where before increment');
		}
		if (!preg_match("/^[a-z_]+$/i", $col)) {
			exit('Error syntax method increment, var $col only allow contains character a-z_');
		}
		$count = (int)$count;
		static::$sql = "UPDATE " . static::$table . " SET $col=$col + $count " . static::$where;
		return static::execute();
	}

	public static function decrement($col, $count = 1)
	{
		static::connect();
		if (static::$where == '') {
			exit('Error syntax method increment, please add method where before increment');
		}
		if (!preg_match("/^[a-z_]+$/i", $col)) {
			exit('Error syntax method increment, var $col only allow contains character a-z_');
		}
		$count = (int)$count;
		static::$sql = "UPDATE " . static::$table . " SET $col=$col - $count " . static::$where;
		return static::execute();
	}

	public static function delete()
	{
		static::connect();
		if (static::$where == '') {
			exit('Error syntax method delete, please add method where before delete');
		}
		static::$sql = "DELETE FROM " . static::$table . ' ' . static::$where;
		return static::execute();
	}

	public static function raw($string)
	{
		return $string;
	}

	public static function select(...$selects)
	{
		foreach ($selects as $item) {
			if (static::$select == '*') {
				static::$select = $item;
			} else {
				static::$select .= ", $item";
			}
		}
		return new static();
	}

	public static function join($table, $col1, $operator, $col2)
	{
		$operator = trim($operator);
		if (in_array($operator, static::$comparison_operator_array)) {
			static::$join .= " JOIN $table ON $col1 $operator $col2";
		} else {
			$text = 'Error syntax method join use comparison operator: ' . implode(', ', static::$comparison_operator_array);
			trigger_error($text, E_USER_WARNING);
			return false;
		}
		return new static();
	}

	public static function leftJoin($table, $col1, $operator, $col2)
	{
		$operator = trim($operator);
		if (in_array($operator, static::$comparison_operator_array)) {
			static::$join .= " LEFT JOIN $table ON $col1 $operator $col2";
		} else {
			$text = 'Error syntax method leftJoin use comparison operator: ' . implode(', ', static::$comparison_operator_array);
			trigger_error($text, E_USER_WARNING);
			return false;
		}
		return new static();
	}

	public static function rightJoin($table, $col1, $operator, $col2)
	{
		$operator = trim($operator);
		if (in_array($operator, static::$comparison_operator_array)) {
			static::$join .= " RIGHT JOIN $table ON $col1 $operator $col2";
		} else {
			$text = 'Error syntax method rightJoin use comparison operator: ' . implode(', ', static::$comparison_operator_array);
			trigger_error($text, E_USER_WARNING);
			return false;
		}
		return new static();
	}

	public static function where($filed, $comparison_operator, $value = false)
	{
		if ($value === false) {
			$value = $comparison_operator;
			$comparison_operator = '=';
		}
		$comparison_operator = strtoupper($comparison_operator);
		if (in_array($comparison_operator, static::$comparison_operator_array)) {
			if (static::$where === null) {
				static::$where = "WHERE";
			} else {
				static::$where .= " AND";
			}
			static::$where .= " $filed $comparison_operator ?";
			static::$data_execute[] = $value;
		} else {
			$text = 'Error syntax method where use comparison operator: ' . implode(', ', static::$comparison_operator_array);
			static::error($text);
			return false;
		}
		return new static();
	}

	public static function orWhere($filed, $comparison_operator, $value = false)
	{
		if ($value === false) {
			$value = $comparison_operator;
			$comparison_operator = '=';
		}
		$comparison_operator = strtoupper($comparison_operator);
		if (in_array($comparison_operator, static::$comparison_operator_array)) {
			if (static::$where === null) {
				static::$where = "WHERE";
			} else {
				static::$where .= " OR";
			}
			static::$where .= " $filed $comparison_operator ?";
			static::$data_execute[] = $value;
		} else {
			$text = 'Error syntax method where use comparison operator: ' . implode(', ', static::$comparison_operator_array);
			static::error($text);
			return false;
		}
		return new static();
	}

	public static function whereIn($filed, $arr = [])
	{
		$in = '';
		foreach ($arr as $value) {
			if ($in == '') {
				$in = '?';
			} else {
				$in .= ', ?';
			}
			static::$data_execute[] = $value;
		}
		$in = '(' . $in . ')';
		if (static::$where === null) {
			static::$where = "WHERE";
		} else {
			static::$where .= " AND";
		}
		static::$where .= " $filed IN $in";

		return new static();
	}

	public static function whereNotIn($filed, $arr = [])
	{
		$in = '';
		foreach ($arr as $value) {
			if ($in == '') {
				$in = '?';
			} else {
				$in .= ', ?';
			}
			static::$data_execute[] = $value;
		}
		$in = '(' . $in . ')';
		if (static::$where === null) {
			static::$where = "WHERE";
		} else {
			static::$where .= " AND";
		}
		static::$where .= " $filed NOT IN $in";
		return new static();
	}

	public static function whereRaw($where, $data = [])
	{
		$where_explode = explode('?', $where);
		if (count($where_explode) != (count($data) + 1)) {
			$text = "Error syntax method where: count data != count ? in argument where: $where <br> data: " . json_encode($data);
			static::error($text);
			return false;
		}
		foreach ($data as $v) {
			static::$data_execute[] = $v;
		}
		if (static::$where === null) {
			static::$where = "WHERE";
		} else {
			static::$where .= " AND";
		}
		static::$where .= " $where";
		return new static();
	}

	public static function groupBy($filed)
	{
		if (static::$group_by === null) {
			static::$group_by = "GROUP BY $filed";
		} else {
			static::$group_by .= ", $filed";
		}
		return new static();
	}

	public static function having($filed, $operator, $value = false)
	{
		if ($value === false) {
			$value = $operator;
			$operator = '=';
		}
		if (in_array($operator, static::$comparison_operator_array)) {
			if (static::$having === null) {
				static::$having = "HAVING";
			} else {
				static::$having .= " AND";
			}
			static::$having .= " $filed $operator ?";
			static::$data_execute[] = $value;
		} else {
			echo 'Method having use comparison operator: ' . implode(', ', static::$comparison_operator_array);
			die();
		}
		return new static();
	}

	public static function orHaving($filed, $operator, $value = false)
	{
		if ($value === false) {
			$value = $operator;
			$operator = '=';
		}
		if (in_array($operator, static::$comparison_operator_array)) {
			if (static::$having === null) {
				static::$having = "HAVING";
			} else {
				static::$having .= " OR";
			}
			static::$having .= " $filed $operator ?";
			static::$data_execute[] = $value;
		} else {
			echo 'Method having use comparison operator: ' . implode(', ', static::$comparison_operator_array);
			die();
		}
		return new static();
	}

	public static function orderBy($filed, $direction = '')
	{
		$direction = strtoupper($direction);
		if ($direction == 'ASC' || $direction == 'DESC') {
			if (static::$order === null) {
				static::$order = "ORDER BY $filed $direction";
			} else {
				static::$order .= ", $filed $direction";
			}
		} else {
			$text = "Error syntax method orderBy argument direction must is asc, desc ";
			trigger_error($text, E_USER_WARNING);
			return false;
		}
		return new static();
	}

	public static function orderByRand()
	{
		static::$order = 'ORDER BY RAND()';
		return new static();
	}

	public static function limit(?int $offset, ?int $limit = 0)
	{
		if ($limit == 0) {
			$limit = $offset;
			static::$limit = "LIMIT $limit";
		} else {
			static::$limit = "LIMIT $offset, $limit";
		}
		return new static();
	}

	public static function get($type = 'object')
	{
		static::connect();
		static::$sql = "SELECT " . static::$select . " FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit;
		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		if ($type == 'object') {
			static::$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		} else {
			static::$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		static::flushData();
		return static::$result;
	}

	public static function first($type = 'object')
	{
		static::connect();
		static::$sql = "SELECT " . static::$select . " FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit;
		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		if ($type == 'object') {
			static::$result = $stmt->fetch(PDO::FETCH_OBJ);
		} else {
			static::$result = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		static::flushData();
		return static::$result;
	}

	public static function count($filed = 'id')
	{
		static::connect();
		static::$sql = "SELECT COUNT($filed) as total FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit;
		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		static::$result = !empty($result) ? (int)$result->total : 0;
		static::flushData();
		return static::$result;
	}

	public static function max($filed = 'id')
	{
		static::connect();
		static::$sql = "SELECT MAX($filed) as max FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit;
		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		static::$result = (float)$result->max;
		static::flushData();
		return static::$result;
	}

	public static function sum($filed = 'id')
	{
		static::connect();
		static::$sql = "SELECT SUM($filed) as total FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit;
		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		static::$result = $result->total === null ? 0 : $result->total;
		static::flushData();
		return static::$result;
	}

	public static function find(?int $id, $type = 'object')
	{
		static::connect();
		static::$sql = "SELECT " . static::$select . " FROM " . static::$table . " WHERE id=$id";
		$stmt = static::$conn->query(static::$sql);
		if ($type == 'object') {
			static::$result = $stmt->fetch(PDO::FETCH_OBJ);
		} else {
			static::$result = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		static::flushData();
		return static::$result;
	}

	public static function fetchOne($sql, $data = [], $type = 'object')
	{
		static::connect();
		static::flushData();
		$sql_explode = explode('?', $sql);
		if (count($sql_explode) != (count($data) + 1)) {
			$text = "Error syntax method fetchOne: count data != count ? in argument fetchOne: $sql <br> data: " . json_encode($data);
			static::error($text);
			return false;
		}
		static::$sql = $sql;
		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute($data);
		if ($type == 'object') {
			static::$result = $stmt->fetch(PDO::FETCH_OBJ);
		} else {
			static::$result = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		static::flushData();
		return static::$result;
	}

	public static function fetchAll($sql, $data = [], $type = 'object')
	{
		static::connect();
		static::flushData();
		$sql_explode = explode('?', $sql);
		if (count($sql_explode) != (count($data) + 1)) {
			$text = "Error syntax method fetchAll: count data != count ? in argument fetchAll: $sql <br> data: " . json_encode($data);
			static::error($text);
			return false;
		}
		static::$sql = $sql;
		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute($data);
		if ($type == 'object') {
			static::$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		} else {
			static::$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		static::flushData();
		return static::$result;
	}

	public static function truncate()
	{
		static::flushData();
		static::connect();
		static::$sql = "TRUNCATE TABLE " . static::$table;
		return static::execute();
	}

	//custom
	public static function call($procedure)
	{
		static::connect();
		$stmt = static::$conn->prepare("CALL " . $procedure);
		$stmt->execute();
		static::$result = $stmt->fetchall(PDO::FETCH_OBJ);
		static::flushData();
		return static::$result;
	}
	public static function startTransaction()
	{
		static::connect();
		static::$conn->beginTransaction();
	}
	public static function rollBack()
	{
		static::$conn->rollBack();
	}
	public static function commit()
	{
		static::$conn->commit();
	}
}
