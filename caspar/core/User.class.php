<?php

	namespace caspar\core;

	/**
	 * Simple sample user class
	 *
	 * @author Daniel Andre Eikeland <zegenie@gmail.com>
	 * @version 1.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 * @subpackage core
	 */

	/**
	 * This is a barebone user class with some common properties
	 *
	 * @package caspar
	 * @subpackage core
	 */
	class User
	{
		
		/**
		 * Unique identifier
		 *
		 * @Id
		 * @Column(type="integer", auto_increment=true, length=10)
		 * @var integer
		 */
		protected $_id;
		
		/**
		 * Unique username (login name)
		 *
		 * @Column(type="string", length=200)
		 * @var string
		 */
		protected $_username = '';

		/**
		 * Whether or not the user has authenticated
		 * 
		 * @var boolean
		 */
		protected $authenticated = false;
		
		/**
		 * Hashed password
		 *
		 * @Column(type="string", length=200)
		 * @var string
		 */
		protected $_password = '';
		
		/**
		 * User real name
		 *
		 * @Column(type="string", length=200)
		 * @var string
		 */
		protected $_realname = '';
		
		/**
		 * User email
		 *
		 * @Column(type="string", length=250)
		 * @var string
		 */
		protected $_email = '';
		
		/**
		 * Users language
		 *
		 * @var string
		 */
		protected $_language = '';
		
		/**
		 * The users group
		 *
		 * @Column(type="integer", length=10)
		 */
		protected $_group_id = null;
	
		/**
		 * Timestamp of when the user was last seen
		 *
		 * @Column(type="integer", length=10)
		 * @var integer
		 */
		protected $_lastseen = 0;

		/**
		 * The timezone this user is in
		 *
		 * @var integer
		 */
		protected $_timezone = null;

		/**
		 * Whether the user is enabled
		 * 
		 * @Column(type="boolean", deafult=true)
		 * @var boolean
		 */
		protected $_enabled = false;
		
		/**
		 * Whether the user is activated
		 * 
		 * @Column(type="boolean", deafult=false)
		 * @var boolean
		 */
		protected $_activated = false;
		
		/**
		 * Whether the user is deleted
		 * 
		 * @Column(type="boolean", deafult=false)
		 * @var boolean
		 */
		protected $_deleted = false;

		/**
		 * Take a raw password and convert it to the hashed format
		 * 
		 * @param string $password
		 * 
		 * @return hashed password
		 */
		public static function hashPassword($password, $salt = null)
		{
			$salt = ($salt !== null) ? $salt : Caspar::getSalt();
			return crypt($password, '$2a$07$'.$salt.'$');
		}
		
		public static function loginCheck()
		{
			return new User();
		}
		
		/**
		 * Create and return a temporary password
		 * 
		 * @return string
		 */
		public static function createPassword($len = 8)
		{
			$pass = '';
			$lchar = 0;
			$char = 0;
			for($i = 0; $i < $len; $i++) {
				while($char == $lchar) {
					$char = mt_rand(48, 109);
					if($char > 57) $char += 7;
					if($char > 90) $char += 6;
				}
				$pass .= chr($char);
				$lchar = $char;
			}
			return $pass;
		}

		/**
		 * Retrieve the users real name
		 * 
		 * @return string
		 */
		public function getName()
		{
			return ($this->_realname) ? $this->_realname : $this->_username;
		}
		
		/**
		 * Retrieve the users id
		 * 
		 * @return integer
		 */
		public function getID()
		{
			return $this->_id;
		}
		
		/**
		 * Retrieve this users realname and username combined 
		 * 
		 * @return string "Real Name (username)"
		 */
		public function getNameWithUsername()
		{
			return ($this->_realname) ? $this->_realname . ' (' . $this->_username . ')' : $this->_username;
		}
		
		public function __toString()
		{
			return $this->getNameWithUsername();
		}

		/**
		 * Whether this user is authenticated or just an authenticated guest
		 * 
		 * @return boolean
		 */
		public function isAuthenticated()
		{
			return (bool) ($this->getID());
		}
		
		/**
		 * Set users "last seen" property to NOW
		 */
		public function updateLastSeen()
		{
			$this->_lastseen = NOW;
		}
		
		/**
		 * Return timestamp for when this user was last online
		 * 
		 * @return integer
		 */
		public function getLastSeen()
		{
			return $this->_lastseen;
		}
		
		/**
		 * Checks whether or not the user is logged in
		 *
		 * @return boolean
		 */
		public function isLoggedIn()
		{
			return (bool) $this->_id;
		}
		
		/**
		 * Change the password to a new password
		 *
		 * @param string $newpassword
		 */
		public function changePassword($newpassword)
		{
			$this->_password = self::hashPassword($newpassword);
		}
		
		/**
		 * Alias for changePassword
		 * 
		 * @param string $newpassword
		 * 
		 * @see self::changePassword
		 */
		public function setPassword($newpassword)
		{
			return $this->changePassword($newpassword);
		}
		
		/**
		 * Whether this user is currently active on the site
		 * 
		 * @return boolean
		 */
		public function isActive()
		{
			return (bool) ($this->_lastseen > (NOW - (60 * 10)));
		}
		
		/**
		 * Whether this user is currently inactive (but not logged out) on the site
		 * 
		 * @return boolean
		 */
		public function isAway()
		{
			return (bool) (($this->_lastseen < (NOW - (60 * 10))) && ($this->_lastseen > (NOW - (60 * 30))));
		}
		
		/**
		 * Whether this user is currently offline (timed out or explicitly logged out)
		 * 
		 * @return boolean
		 */
		public function isOffline()
		{
			return (bool) ($this->_lastseen < (NOW - (60 * 30)));
		}
		
		/**
		 * Whether this user is enabled or not
		 * 
		 * @return boolean
		 */
		public function isEnabled()
		{
			return $this->_enabled;
		}

		/**
		 * Set whether this user is activated or not
		 * 
		 * @param boolean $val[optional] 
		 */
		public function setActivated($val = true)
		{
			$this->_activated = (boolean) $val;
		}

		/**
		 * Whether this user is activated or not
		 * 
		 * @return boolean
		 */
		public function isActivated()
		{
			return $this->_activated;
		}
		
		/**
		 * Whether this user is deleted or not
		 * 
		 * @return boolean
		 */
		public function isDeleted()
		{
			return $this->_deleted;
		}

		public function markAsDeleted()
		{
			$this->_deleted = true;
		}
		
		/**
		 * Set the username
		 *
		 * @param string $username
		 */
		public function setUsername($username)
		{
			$this->_username = $username;
		}

		/**
		 * Return this users' username
		 * 
		 * @return string
		 */
		public function getUsername()
		{
			return $this->_username;
		}
		
		/**
		 * Returns a hash of the user password
		 *
		 * @return string
		 */
		public function getHashPassword()
		{
			return $this->_password;
		}
		
		/**
		 * Returns a hash of the user password
		 *
		 * @see TBGUser::getHashPassword
		 * @return string
		 */
		public function getPassword()
		{
			return $this->getHashPassword();
		}

		/**
		 * Return whether or not the users password is this
		 *
		 * @param string $password Unhashed password
		 *
		 * @return boolean
		 */
		public function hasPassword($password)
		{
			return $this->hasPasswordHash(self::hashPassword($password));
		}

		/**
		 * Return whether or not the users password is this
		 *
		 * @param string $password Hashed password
		 *
		 * @return boolean
		 */
		public function hasPasswordHash($password)
		{
			return (bool) ($password == $this->getHashPassword());
		}

		/**
		 * Returns the real name (full name) of the user
		 *
		 * @return string
		 */
		public function getRealname()
		{
			return $this->_realname;
		}
		
		/**
		 * Returns the email of the user
		 *
		 * @return string
		 */
		public function getEmail()
		{
			return $this->_email;
		}
		
		/**
		 * Set the users email address
		 *
		 * @param string $email A valid email address
		 */
		public function setEmail($email)
		{
			$this->_email = $email;
		}

		/**
		 * Set the users realname
		 *
		 * @param string $realname
		 */
		public function setRealname($realname)
		{
			$this->_realname = $realname;
		}

		/**
		 * Set whether this user is enabled or not
		 * 
		 * @param boolean $val[optional]
		 */
		public function setEnabled($val = true)
		{
			$this->_enabled = $val;
		}
		
		/**
		 * Set whether this user is validated or not
		 * 
		 * @param boolean $val[optional]
		 */
		public function setValidated($val = true)
		{
			$this->_activated = $val;
		}
		
		/**
		 * Get this users timezone
		 *
		 * @return mixed
		 */
		public function getTimezone()
		{
			return $this->_timezone;
		}

		/**
		 * Set this users timezone
		 *
		 * @param integer $timezone
		 */
		public function setTimezone($timezone)
		{
			$this->_timezone = $timezone;
		}

		public function setLanguage($language)
		{
			$this->_language = $language;
		}

		public function getLanguage()
		{
			return ($this->_language != '') ? $this->_language : TBGSettings::getLanguage();
		}

	}
