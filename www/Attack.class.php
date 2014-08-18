<?php
/*
status
0 = null
1 = cracking
2 = success
*/
require_once("Wicker.php");

Class Attack {
	private $id;
	private $cap_id;
	private $attack;
	private $status;
	private $password;
	private $pwfile;
	private $tmpfile;
	private $runtime;
	private $current;
	private $rate;
	private $auth;
	private $pid;
	private $name = array("10k", "small", "medium", "rockyou", "big", "superbig");
	private $size = array(10000, 292847, 9904974, 14344391, 185866729, 982963903);

	public $db;

	public function __construct() {
		$this->connectToDatabase();
	}

    public static function fromDB($id, $attack) {
    	global $wicker;
        $instance = new self();

        $instance->connectToDatabase();

		$statement = $wicker->db->con()->prepare("SELECT * FROM attacks WHERE cap_id = ? AND attack = ?");
        $statement->execute(array($id, $attack));
        $info = $statement->fetchObject();

        $instance->id       = $info->id;
        $instance->cap_id   = $info->cap_id;
        $instance->attack   = $info->attack;
        $instance->status   = $info->status;
        $instance->password = $info->password;
        $instance->pwfile   = $info->pwfile;
        $instance->tmpfile  = $info->tmpfile;
        $instance->runtime  = $info->runtime;
        $instance->current  = $info->current;
        $instance->rate     = $info->rate;
        $instance->auth     = $info->auth;
        $instance->pid      = $info->pid;

        return $instance;
    }

	private function connectToDatabase() {
		$database = new Database;
		$this->db = $database;
	}

	private function setVal($field, $val) {
		$statement = $this->db->con()->prepare("UPDATE `attacks` SET `$field` = ? WHERE `id` = ?");
		$statement->execute(array($val, $this->getID()));
	}

	public function getID() { return $this->id; }
	public function getCapID() { return $this->cap_id; }
    public function getAttack() { return $this->attack; }
    public function getStatus() { return $this->status; }
    public function getPassword() { return $this->password; }
    public function getPwFile() { return $this->pwfile; }
    public function getTmpfile() { return $this->tmpfile; }
    public function getRuntime() { return $this->runtime; }
    public function getAttackName() { return $this->name[$this->attack-1]; }
    public function getDictionarySize() { return $this->size[$this->attack-1]; }
    public function getCurrent() { return $this->current; }
    public function getRate() { return $this->rate; }
    public function getAuth() { return $this->auth; }
    public function getPID() { return $this->pid; }

    public function setAuth($val) { $this->setVal("auth", $val); $this->auth = $val; }
    public function setRate($val) { $this->setVal("rate", $val); $this->rate = $val; }
    public function setCurrent($val) { $this->setVal("current", $val); $this->current = $val; }
    public function setRuntime($val) { $this->setVal("runtime", $val); $this->runtime = $val; }
    public function setTmpfile($val) { $this->setVal("tmpfile", $val); $this->tmpfile = $val; }
    public function setStatus($val) { $this->setVal("status", $val); $this->status = $val; }
    public function setPassword($val) { $this->setVal("password", $val); $this->password = $val; }
    public function setPwFile($val) { $this->setVal("pwfile", $val); $this->pwfile = $val; }
    public function setPID($val) { $this->setVal("pid", $val); $this->pid = $val; }
}
