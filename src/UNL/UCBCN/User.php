<?php
namespace UNL\UCBCN;

use UNL\UCBCN\ActiveRecord\Record;
use UNL\UCBCN\Calendars;
use UNL\UCBCN\Account;
use UNL\UCBCN\User\Permission;
/**
 * Table Definition for user
 *
 * PHP version 5
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */

/**
 * ORM for a record within the database.
 *
 * @category  Events
 * @package   UNL_UCBCN
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2009 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/unl-event-publisher/
 */
class User extends Record
{

    public $uid;                             // string(100)  not_null primary_key
    public $account_id;                      // int(10)  not_null unsigned
    public $calendar_id;                     // int(10)  unsigned
    public $accountstatus;                   // string(100)
    public $datecreated;                     // datetime(19)  binary
    public $uidcreated;                      // string(100)
    public $datelastupdated;                 // datetime(19)  binary
    public $uidlastupdated;                  // string(100)

    public static function getTable()
    {
        return 'user';
    }

    function table()
    {
        return array(
            'uid'=>130,
            'account_id'=>129,
            'calendar_id'=>1,
            'accountstatus'=>2,
            'datecreated'=>14,
            'uidcreated'=>2,
            'datelastupdated'=>14,
            'uidlastupdated'=>2,
        );
    }

    function keys()
    {
        return array(
            'uid',
        );
    }
    
    function sequenceKey()
    {
        return array(false, false);
    }
    
    function links()
    {
        return array('account_id'     => 'account:id',
                     'calendar_id'    => 'calendar:id',
                     'uidcreated'     => 'users:uid',
                     'uidlastupdated' => 'users:uid');
    }

    public function update()
    {
        $this->datelastupdated = date('Y-m-d H:i:s');
        return parent::update();
    }
    
    public function insert()
    {
        $this->datecreated     = date('Y-m-d H:i:s');
        $this->datelastupdated = date('Y-m-d H:i:s');
        return parent::insert();
    }
    
    public function __toString()
    {
        return $this->uid;
    }

    public function getCalendars() 
    {
        # create options for calendar listing class
        $options = array('user_id' => $this->uid);
        $calendars = new Calendars($options);
        return $calendars;
    }

    public function getAccount() 
    {
        return Account::getByID($this->account_id);
    }

    public function hasPermission($permission_id, $calendar_id)
    {
        return (bool)Permission::getByUser_UID($this->uid, 'calendar_id = ' . 
            (int)($calendar_id) . ' AND permission_id = ' . (int)($permission_id));
    }

    public function grantPermission($permission_id, $calendar_id)
    {
        $permission = new Permission();

        $permission->permission_id = $permission_id;
        $permission->user_uid = $this->uid;
        $permission->calendar_id = $calendar_id;

        $permission->insert();
    }
}