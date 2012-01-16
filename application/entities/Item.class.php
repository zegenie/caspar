<?php

	namespace application\entities;

	/**
	 * @Table(name="\application\entities\tables\Items")
	 */
	class Item extends \b2db\Saveable
	{

		/**
		 * @Id
		 * @Column(type="integer", length=10, auto_increment=true)
		 * @var integer
		 */
		protected $_id;
		
	}