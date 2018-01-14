<?php


class Congo {
	private $db = "shhhdb";
	private $conn;

	public function __construct() {
		$this->conn = new MongoDB\Driver\Manager("mongodb://localhost:27017");
	}

	public function query($table, $options) { //Options must be an array
		$q = new MongoDB\Driver\Query($options);
		$rows = $this->conn->executeQuery($this->getTableString($table), $q);
		$data = [];
		foreach($rows as $row) {
			$data[] = $row;
		}
		return new CongoResult($data);
	}

	public function insert($table, $doc) { //The doc must be an array
		$b = new MongoDB\Driver\BulkWrite;
		$b->insert($doc);
		$this->conn->executeBulkWrite($this->getTableString($table), $b);
	}

	public function update($table, $filter, $values) {
		//Same as insert but with:
		//b->update(['name'=>'Audi'], ['$set'=>['price'=>2500]]);
		//update: filter to find, value to set
	}

	public function delete($table, $filter) {
		$b = new MongoDB\Driver\BulkWrite;
		$b->delete($filter);
		$this->conn->executeBulkWrite($this->getTableString($table), $b);
	}

	private function getTableString($table) {
		return $this->db . "." . $table;
	}
}

class CongoResult {
	private $data;
	private $size;

	public function __construct($r) {
		$this->data = $r;
		$this->size = sizeOf($r);
	}

	public function hasResults() {
		return $this->size > 0;
	}

	public function countResults() {
		return $this->size;
	}

	public function getRows() {
		return $this->data;
	}

	public function getFirst() {
		return $this->size > 0 ? $this->data[0] : null;
	}
}
?>