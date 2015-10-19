<?php


interface MlmSystemPathsInterface
{
	/** Request to the DB and fetch the array */
	public function query($sql);

	/** Path to the element */
	public function getPath($id);

	/** Get all children */
	public function getChildren($id);

	/** Get count children */
	public function getChildrenCount($id);

	/** Get level */
	public function getLevel($id);

	/** Get tree */
	public function getTree();

	/** Build tree id => parent */
	public function buildTree($arr, $pid = 0);

	/** Generate paths */
	public function generatePaths($ids = true);

	/** Walkthru tree in DB */
	public function walkthruTree($arr, &$pdo_stmt, $ids = true, $prev = NULL, $lvl = 0);

	/** Put path in DB */
	public function putPathItem(&$pdo_stmt, $id, $pid, $order, $level);

	/** Remove path for $item in DB */
	public function removePathItem($item);

}

class SystemPaths implements MlmSystemPathsInterface
{
	/** @var modX $modx */
	protected $modx;
	/** @var MlmSystem $MlmSystem */
	protected $MlmSystem;

	protected $PathTable;
	protected $ClientTable;

	/** @var array $config */
	protected $config = array();


	public function __construct($MlmSystem, $config)
	{
		$this->MlmSystem = &$MlmSystem;
		$this->modx = &$MlmSystem->modx;
		$this->config =& $config;

		$this->PathTable = $this->modx->getTableName('MlmSystemPath');
		$this->ClientTable = $this->modx->getTableName('MlmSystemClient');
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
	public function query($sql)
	{
		$stmt = $this->modx->query($sql);
		return ($stmt) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $this->modx->errorInfo();
	}

	/** Path to the element */
	public function getPath($id)
	{
		$sql = "SELECT t.*, p.level as p_level, p.order as p_order "
			. "FROM {$this->PathTable} p "
			. "JOIN {$this->ClientTable} t ON `id`=p.`parent` "
			. "WHERE id={$id} ORDER BY `order`";
		return $this->query($sql);
	}

	/** Get all children */
	public function getChildren($id)
	{
		$sql = "SELECT t.*, p.level as p_level, p.order as p_order "
			. "FROM {$this->PathTable} p "
			. "JOIN {$this->ClientTable} t ON `id`=`id` "
			. "WHERE p.`parent`={$id} ORDER BY p.`level`, p.`order`";
		return $this->query($sql);
	}

	/** Get count children */
	public function getChildrenCount($id)
	{
		$sql = "SELECT count(*) FROM {$this->PathTable} WHERE parent={$id}";
		$stmt = $this->query($sql);
		return ($stmt) ? $stmt[0]['count(*)'] : false;
	}

	/** Get level */
	public function getLevel($id)
	{
		$level = 0;
		$sql = "SELECT level FROM {$this->PathTable} WHERE id={$id} LIMIT 1";
		$stmt = $this->query($sql);
		return ($stmt) ? $stmt[0]['level'] : $level;
	}


	/** Get tree */
	public function getTree()
	{
		$sql = "SELECT id,parent FROM {$this->ClientTable}";
		$stmt = $this->query($sql);
		return ($stmt) ? $this->buildTree($stmt) : false;
	}

	/** Build tree id => parent */
	public function buildTree($arr, $pid = 0)
	{
		$tmp = array();
		foreach ($arr as $row) {
			if (($row['id'] == (int)$pid)) {
				$id = $row['id'];
				$sql = "SELECT id,parent FROM {$this->ClientTable} WHERE (id={$id}) ";
				$res = $this->query($sql);
				$this->buildTree($res, $row['parent']);
			}
			if (($row['parent'] == (int)$pid)) {
				$id = $row['id'];
				$sql = "SELECT id,parent FROM {$this->ClientTable} WHERE (parent={$id}) ";
				$res = $this->query($sql);
				$tmp[$row['id']] = $this->buildTree($res, $row['id']);
			}
		}
		return count($tmp) ? $tmp : true;
	}

	/** Generate paths */
	public function generatePaths($ids = true)
	{


		$this->modx->log(1, print_r('generatePaths', 1));
		$this->modx->log(1, print_r($ids ,1));

		if (!$tree = $this->getTree($ids)) {
			return false;
		}
		$this->modx->beginTransaction();
		if ($ids === true) {
			$this->modx->query("TRUNCATE {$this->PathTable}");
		}

		$sql = "INSERT INTO {$this->PathTable} (`id`, `parent`, `level`, `order`) VALUES (:id, :parent, :level, :order)";
		$stmt = $this->modx->prepare($sql);

		try {
			$this->walkthruTree($tree, $stmt, $ids);
			$this->modx->commit();
		} catch (Exception $e) {
			echo $e->getMessage();
			$this->modx->rollBack();
		}
		return true;
	}

	/** Walkthru tree in DB */
	public function walkthruTree($arr, &$pdo_stmt, $ids = true, $prev = NULL, $lvl = 0)
	{
		if (is_numeric($ids)) {
			$ids = array($ids);
		}
		if (is_array($arr)) {
			foreach ($arr as $id => $a) {
				if (is_array($prev)) foreach ($prev as $pid => $order) {
					try {
						if ($ids === true) {
							$this->putPathItem($pdo_stmt, $id, $pid, $order, $lvl);
						} elseif (is_array($ids) && in_array($id, $ids)) {
							$this->putPathItem($pdo_stmt, $id, $pid, $order, $lvl);
							if (is_array($a)) $ids += array_merge($ids, array_keys($a));
						}
					} catch (Exception $e) {
						echo '<p>' . $e->getMessage() . '</p>';
					}
				}
				if (is_array($a)) {
					$prev_new = $prev;
					$prev_new[$id] = $lvl;
					$this->walkthruTree($a, $pdo_stmt, $ids, $prev_new, ($lvl + 1));
				}
			}
		}
	}

	/** Put path in DB */
	public function putPathItem(&$pdo_stmt, $id, $pid, $order, $level)
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
	public function removePathItem($item)
	{
		$sql = "DELETE FROM {$this->PathTable} WHERE id={$item} OR parent={$item};";
		$stmt = $this->modx->prepare($sql);
		$stmt->execute();
		$stmt->closeCursor();
	}

	/** Create tree */
	public function createTree($total, $lvls = 3)
	{
		$this->modx->query("DELETE FROM {$this->ClientTable}");
		$this->modx->query("ALTER TABLE {$this->ClientTable} AUTO_INCREMENT=1");
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
		$sql = "INSERT INTO {$this->ClientTable} (`id`, `parent`) VALUES (:id, :parent)";
		$stmt = $this->modx->prepare($sql);
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
					$stmt->bindValue(':parent_id', $prev);
					if ($stmt->execute()) {
						$pArr[$i][] = $k;//$this->modx->lastInsertId();
					} else throw new Exception ('Error add');
				}
			}
		}
	}

}
