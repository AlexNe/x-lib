<?php
/**
 * new PDOWhereConstructor(array $data)
 *
 * 	Where Examples:
 * 	["name1" => 1, "name2" => "data2"] 					--- WHERE name1 = 1 AND name2 = 'data2'
 * 	["[and]" => ["name1" => 1, "name2" => "data2"]] 	--- WHERE name1 = 1 AND name2 = 'data2'
 * 	["[or]" => ["name||1" => 1, "name||2" => "data2"]] 	--- WHERE name = 1 OR name = 'data2'
 * 	["[or]" => ["name1" => 1, "name2" => "data2"]] 		--- WHERE name1 = 1 OR name2 = 'data2'
 * 	["name" => 1] 					--- WHERE name = 1)
 * 	["name" => [1,2,3,5]] 			--- WHERE name IN ( 1, 2, 3, 5)
 * 	["name|in" => [1,2,3,5]] 		--- WHERE name IN ( 1, 2, 3, 5)
 * 	["name|!" => [1,2,3,5]] 		--- WHERE name NOT IN ( 1, 2, 3, 5)
 * 	["name|!=" => [1,2,3,5]] 		--- WHERE name NOT IN ( 1, 2, 3, 5)
 * 	["name|not" => [1,2,3,5]] 		--- WHERE name NOT IN ( 1, 2, 3, 5)
 * 	["name|not" => 1] 				--- WHERE name != 1)
 * 	["name|<>" => 1] 				--- WHERE name <> 1)
 * 	["name|>" => 1] 				--- WHERE name > 1)
 * 	["name|>=" => 1] 				--- WHERE name >= 1)
 * 	["name|like" => 1] 				--- WHERE name like 1)
 *
 */
namespace X\Database\Driver;
use \X\ETrace\System as ESys;

class PDOWhereConstructor_InputDataTypeError extends ESys {}
class PDOWhereConstructor_OperatorTypeError extends ESys {}
class PDOWhereConstructor {
	/**
	 * @var mixed
	 */
	private $SQL;
	/**
	 * @var mixed
	 */
	private $data_set;
	/**
	 * @param $data
	 */
	public function __construct($data = []) {
		$this->data_set = [];
		$this->Parse($data);
	}

	/**
	 * @return mixed
	 */
	public function get_sql() {
		return $this->SQL;
	}

	/**
	 * @return mixed
	 */
	public function get_dataset() {
		return $this->data_set;
	}

	/**
	 * @param array $data
	 */
	public function Parse($data = []) {
		if (is_array($data)) {
			if (count($data) > 0) {
				$this->SQL = "WHERE ";
				$this->SQL .= $this->build_where_string($data);
			} else {
				$this->SQL = "";
			}
		} else {
			throw new PDOWhereConstructor_InputDataTypeError("Where data must have array type", 1, [
				"input_data" => $data,
			]);
		}
	}

	/**
	 * @param $data
	 */
	private function build_where_string($data, $inner_level = 0, $operator = "AND") {
		$SQL_Fragment_Items = "";
		$key_index          = 0;
		foreach ($data as $key => $value) {
			$item_info = $this->check_data_item($key, $value, $inner_level, $operator);
			if ($item_info["type"] == "column") {
				if (is_array($value)) {
					$key_id    = 0;
					$data_keys = [];
					foreach ($value as $val) {
						$data_keys[] = $data_key = ":wh_{$item_info["name"]}_{$inner_level}_{$key_index}_{$key_id}";
						$key_id++;
						$this->data_set[$data_key] = $val;
					}
					$data_keys_pat        = implode(", ", $data_keys);
					$SQL_Fragment_Items[] = "`{$item_info["name"]}` {$item_info["operator"]} ({$data_keys_pat})";
				} else {
					$data_key                  = ":wh_{$item_info["name"]}_{$inner_level}_{$key_index}";
					$this->data_set[$data_key] = $value;
					$SQL_Fragment_Items[]      = "`{$item_info["name"]}` {$item_info["operator"]} {$data_key}";
				}
			} else {
				$level_data = $this->build_where_string($value, ($inner_level + 1), $item_info["operator"]);
				if ($inner_level > 0) {
					$SQL_Fragment_Items[] = "({$level_data})";
				} else {
					$SQL_Fragment_Items[] = $level_data;
				}
			}
			$key_index++;
		}
		return implode(" {$operator} ", $SQL_Fragment_Items);
	}

	/**
	 * @param $key
	 */
	private function check_data_item($key, $data, $level, $operator) {
		if (is_integer($key)) {
			$operator = strtolower($operator);
			$key      = "[{$operator}]";
		}
		if (in_array(strtolower($key), ["[or]", "[and]"])) {
			$column_type = "set";
			$operator    = str_replace(["[", "]"], "", strtoupper($key));
			$column_name = null;
		} else {
			$column_type = "column";

			$key_info    = explode("|", $key);
			$column_name = $key_info[0];
			if (isset($key_info[1])) {
				switch (strtolower($key_info[1])) {
					case '=':
					case 'in':
						$operator = $this->default_column_operator($data);
						break;

					case 'not':
					case 'not in':
					case '!':
					case '!=':
					case '<>':
						$operator = $this->not_column_operator($data);
						break;

					case '>':
					case '<':
					case '>=':
					case '<=':
					case '<=>':
					//case 'isnull':
					case 'is null':
					case 'is not null':
					case 'between':
					case 'not between':
					case 'like':
						//case 'coalesce':
						if (is_array($data)) {
							throw new PDOWhereConstructor_OperatorTypeError("For operator '{$key_info[1]} 'data' must have int or string value'", 1, [
								"key"  => $key,
								"data" => $data,
							]);
						}

						$operator = strtoupper($key_info[1]);
						break;

					default:
						$operator = $this->default_column_operator($data);
						break;
				}
			} else {
				$operator = $this->default_column_operator($data);
			}
		}
		return [
			"type"     => $column_type, // column,set
			"operator" => $operator,    // column = {=,>,<,IN},set= { OR, AND }
			"name"     => $column_name,
		];
	}

	/**
	 * @param $data
	 */
	private function default_column_operator($data) {
		if (is_array($data)) {return "IN";} else {return "=";}
	}

	/**
	 * @param $data
	 */
	private function not_column_operator($data) {
		if (is_array($data)) {return "NOT IN";} else {return "!=";}
	}

	/**
	 * @param $statement
	 */
	public function bind(&$statement) {
		foreach ($this->data_set as $key => $data_item) {
			if (is_integer($data_item)) {
				$statement->bindValue($key, $data_item, \PDO::PARAM_INT);
			} else {
				$statement->bindValue($key, $data_item, \PDO::PARAM_STR);
			}
		}
	}
}
?>