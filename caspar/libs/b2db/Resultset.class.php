<?php

	namespace b2db;
	
	/**
	 * Resultset class
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * Resultset class
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Resultset implements \Iterator, \Countable
	{
		protected $rows = array();
		
		/**
		 * @var Criteria
		 */
		protected $crit;
		protected $int_ptr;
		protected $max_ptr;
		protected $insert_id;
		protected $id_col;

		public function __construct(Statement $statement)
		{
			try
			{
				$this->crit = $statement->getCriteria();
				if ($this->crit instanceof Criteria)
				{
					if ($this->crit->action == 'insert')
					{
						$this->insert_id = $statement->getInsertID();
					}
					elseif ($this->crit->action == 'select')
					{
						while ($row = $statement->fetch())
						{
							$this->rows[] = new Row($row, $statement);
						}
						$this->max_ptr = count($this->rows);
						$this->int_ptr = 0;
					}
					elseif ($this->crit->action = 'count')
					{
						$value = $statement->fetch();
						$this->max_ptr = $value['num_col'];
					}
				}
			}
			catch (\Exception $e)
			{
				throw $e;
			}
		}
		
		protected function _next()
		{
			if ($this->int_ptr == $this->max_ptr)
			{
				return false;
			}
			else
			{
				$this->int_ptr++;
				return true;
			}
		}

		public function getCount()
		{
			return $this->max_ptr;
		}

		/**
		 * Returns the current row
		 *
		 * @return Row
		 */
		public function getCurrentRow()
		{
			if ($this->int_ptr == 0)
			{
				\caspar\core\Logging::log('This is not a valid row');
			}
			if (isset($this->rows[($this->int_ptr - 1)]))
			{
				return $this->rows[($this->int_ptr - 1)];
			}
			return null;
		}
		
		/**
		 * Advances through the resultset and returns the current row
		 * Returns false when there are no more rows
		 *
		 * @return Row
		 */
		public function getNextRow()
		{
			if ($this->_next())
			{
				$theRow = $this->getCurrentRow();
				if ($theRow instanceof Row)
				{
					return $theRow;
				}
				throw new Exception('This should never happen. Please file a bug report');
			}
			else
			{
				return false;
			}
		}
		
		public function get($column, $foreign_key = null)
		{
			$theRow = $this->getCurrentRow();
			if ($theRow instanceof Row)
			{
				return $theRow->get($column, $foreign_key);
			}
			else
			{
				throw new Exception('Cannot return value of ' . $column . ' on a row that doesn\' exist');
			}
		}

		public function getAllRows()
		{
			return $this->rows;
		}

		public function resetPtr()
		{
			$this->int_ptr = 0;
		}

		public function getSQL()
		{
			return ($this->crit instanceof Criteria) ? $this->crit->getSQL() : '';
		}
		
		public function printSQL()
		{
			$str = '';
			if ($this->crit instanceof Criteria)
			{
				$str .= $this->crit->getSQL();
				foreach ($this->crit->getValues() as $val)
				{
					if (!is_int($val))
					{
						$val = '\'' . $val . '\'';
					}
					$str = substr_replace($str, $val, mb_strpos($str, '?'), 1);
				}
			}
			return $str;
		}
	
		public function getInsertID()
		{
			return $this->insert_id;
		}

		public function rewind()
		{
			$this->resetPtr();
		}

		public function current()
		{
			return $this->getCurrentRow();
		}

		public function key()
		{
			if ($this->id_col === null)
				$this->id_col = $this->crit->getTable()->getIdColumn();

			return $this->getCurrentRow()->get($this->id_col);
		}

		public function next()
		{
			return $this->getNextRow();
		}

		public function valid()
		{
			return (boolean) $this->count();
		}

		public function count()
		{
			return (integer) $this->max_ptr;
		}

	}
