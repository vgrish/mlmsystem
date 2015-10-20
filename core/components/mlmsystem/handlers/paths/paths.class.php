<?php


interface MlmSystemPathsInterface
{
	/** Request to the DB and fetch the array */
	public static function query($sql);

	/** Path to the element */
	public static function getPath($id);

	/** Get all children */
	public static function getChildren($id);

	/** Get count children */
	public static function getChildrenCount($id);

	/** Get level */
	public static function getLevel($id);

	/** Get tree */
	public static function getTree();

	/** Get check parent */
	public static function checkParent($id = 0, $parent = 0);

	/** Build tree id => parent */
	public static function buildTree($arr, $pid = 0);

	/** Generate paths */
	public static function generatePaths($ids = true);

	/** Walkthru tree in DB */
	public static function walkthruTree($arr, &$pdo_stmt, $ids = true, $prev = NULL, $lvl = 0);

	/** Put path in DB */
	public static function putPathItem(&$pdo_stmt, $id, $pid, $order, $level);

	/** Remove path for $item in DB */
	public static function removePathItem($item);

}

class SystemPaths implements MlmSystemPathsInterface
{
	/** @var modX $modx */
	static protected $modx;
	static protected $PathTable;
	static protected $ClientTable;

	public function __construct($MlmSystem, $config)
	{
		self::$modx = &$MlmSystem->modx;
		self::$PathTable = $MlmSystem->modx->getTableName('MlmSystemPath');
		self::$ClientTable = $MlmSystem->modx->getTableName('MlmSystemClient');
	}

	/**
	 * @param $n
	 * @param array $p
	 */
	public function __call($n, array$p)
	{
		echo __METHOD__ . ' says: ' . $n;
	}

	/** Request to the DB and fetch the array */
	public static function query($sql)
	{
		$stmt = self::$modx->query($sql);
		return ($stmt) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : self::$modx->errorInfo();
	}

	/** Path to the element */
	public static function getPath($id)
	{
		$sql = "SELECT c.*, p.level, p.order "
			. "FROM " . self::$PathTable . " p "
			. "JOIN " . self::$ClientTable . " c ON c.`id`=p.`parent` "
			. "WHERE p.`id`={$id} ORDER BY p.`order`";
		return self::query($sql);
	}

	/** Get all children */
	public static function getChildren($id)
	{
		$sql = "SELECT c.*, p.level, p.order "
			. "FROM " . self::$PathTable . " p "
			. "JOIN " . self::$ClientTable . " c ON c.`id`=p.`id` "
			. "WHERE p.`parent`={$id} ORDER BY p.`level`, p.`order`";
		return self::query($sql);
	}

	/** Get count children */
	public static function getChildrenCount($id)
	{
		$sql = "SELECT count(*) FROM " . self::$PathTable . " WHERE parent={$id}";
		$stmt = self::query($sql);
		return ($stmt) ? $stmt[0]['count(*)'] : false;
	}

	/** Get level */
	public static function getLevel($id)
	{
		$level = 0;
		$sql = "SELECT level FROM " . self::$PathTable . " WHERE id={$id} LIMIT 1";
		$stmt = self::query($sql);
		return ($stmt) ? $stmt[0]['level'] : $level;
	}

	/** Get tree */
	public static function getTree()
	{
		$sql = "SELECT id,parent FROM " . self::$ClientTable;
		$stmt = self::query($sql);
		return ($stmt) ? self::buildTree($stmt) : false;
	}

	/** Get check parent */
	public static function checkParent($id = 0, $parent = 0)
	{
		if ($id == $parent) {
			return false;
		}
		$sql = "SELECT c.*, p.level, p.order "
			. "FROM " . self::$PathTable . " p "
			. "JOIN " . self::$ClientTable . " c ON c.`id`=p.`id` "
			. "WHERE p.`parent`={$id} AND c.`parent`={$parent} ORDER BY p.`level`, p.`order`";
		return self::query($sql) ? false : true;
	}

	/** Build tree id => parent */
	public static function buildTree($arr, $pid = 0)
	{
		$tmp = array();
		foreach ($arr as $row) {
			if (($row['id'] == (int)$pid)) {
				$id = $row['id'];
				$sql = "SELECT id,parent FROM " . self::$ClientTable . " WHERE (id={$id}) ";
				$res = self::query($sql);
				self::buildTree($res, $row['parent']);
			}
			if (($row['parent'] == (int)$pid)) {
				$id = $row['id'];
				$sql = "SELECT id,parent FROM " . self::$ClientTable . " WHERE (parent={$id}) ";
				$res = self::query($sql);
				$tmp[$row['id']] = self::buildTree($res, $row['id']);
			}
		}
		return count($tmp) ? $tmp : true;
	}

	/** Generate paths */
	public static function generatePaths($ids = true)
	{
		if (!$tree = self::getTree($ids)) {
			return false;
		}
		self::$modx->beginTransaction();
		if ($ids === true) {
			self::$modx->query("TRUNCATE " . self::$PathTable);
		}

		$sql = "INSERT INTO " . self::$PathTable . " (`id`, `parent`, `level`, `order`) VALUES (:id, :parent, :level, :order)";
		$stmt = self::$modx->prepare($sql);

		try {
			self::walkthruTree($tree, $stmt, $ids);
			self::$modx->commit();
		} catch (Exception $e) {
			echo $e->getMessage();
			self::$modx->rollBack();
		}
		return true;
	}

	/** Walkthru tree in DB */
	public static function walkthruTree($arr, &$pdo_stmt, $ids = true, $prev = NULL, $lvl = 0)
	{
		if (is_numeric($ids)) {
			$ids = array($ids);
		}
		if (is_array($arr)) {
			foreach ($arr as $id => $a) {
				if (is_array($prev)) foreach ($prev as $pid => $order) {
					try {
						if ($ids === true) {
							self::putPathItem($pdo_stmt, $id, $pid, $order, $lvl);
						} elseif (is_array($ids) && in_array($id, $ids)) {
							self::putPathItem($pdo_stmt, $id, $pid, $order, $lvl);
							if (is_array($a)) $ids += array_merge($ids, array_keys($a));
						}
					} catch (Exception $e) {
						echo '<p>' . $e->getMessage() . '</p>';
					}
				}
				if (is_array($a)) {
					$prev_new = $prev;
					$prev_new[$id] = $lvl;
					self::walkthruTree($a, $pdo_stmt, $ids, $prev_new, ($lvl + 1));
				}
			}
		}
	}

	/** Put path in DB */
	public static function putPathItem(&$pdo_stmt, $id, $pid, $order, $level)
	{
		if ($pdo_stmt instanceof PDOStatement) {
			$pdo_stmt->bindValue(':id', $id);
			$pdo_stmt->bindValue(':parent', $pid);
			$pdo_stmt->bindValue(':order', $order);
			$pdo_stmt->bindValue(':level', $level);
			if (!$pdo_stmt->execute()) {
				/*$str = 'id: ' . $id . ', parent: ' . $pid . ', order: ' . $order . ', level: ' . $level;
				throw new Exception ('Error when adding paths: ' . $str);*/
			}
		}
	}

	/** Remove path for $item in DB */
	public static function removePathItem($item)
	{
		$sql = "DELETE FROM " . self::$PathTable . " WHERE id={$item} OR parent={$item};";
		$stmt = self::$modx->prepare($sql);
		$stmt->execute();
		$stmt->closeCursor();
	}

	/** Create tree */
	public static function createTree($total, $lvls = 3)
	{
		self::$modx->query("DELETE FROM " . self::$ClientTable);
		self::$modx->query("ALTER TABLE " . self::$ClientTable . " AUTO_INCREMENT=1");
		// distribution of elements on the levels of nesting
		$num = $total;
		$lvlsArr = array();
		for ($i = $lvls; $i > 0; $i--) {
			if ($i == 1) $limit = $num;
			else {
				$limit = ceil($num * 0.6);
				$num -= $limit;
			}
			$lvlsArr[$i] = $limit;
		}
		$sql = "INSERT INTO " . self::$ClientTable . " (`id`, `parent`) VALUES (:id, :parent)";
		$stmt = self::$modx->prepare($sql);
		$pArr = array();
		$k = 0;
		for ($i = 1; $i <= $lvls; $i++) {
			$limit = $lvlsArr[$i];
			echo $i . ' level: ' . $limit . ' pcs.<br>';
			while ($limit--) {
				$k = $k + 1;
				$prev = NULL;
				if ($i > 1) {
					$c = count($pArr[($i - 1)]) - 1;
					$prev = $pArr[($i - 1)][mt_rand(0, $c)];
				} else {
					$c = 0;
					$prev = 0;
				}
				if ($stmt instanceof PDOStatement) {
					$stmt->bindValue(':id', $k);
					$stmt->bindValue(':parent', $prev);
					if ($stmt->execute()) {
						$pArr[$i][] = $k;//$this->modx->lastInsertId();
					} else throw new Exception ('Error add');
				}
			}
		}
	}

}
