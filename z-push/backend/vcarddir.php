<?php
/***********************************************
* File      :   vcarddir.php
* Project   :   Z-Push
* Descr     :   This backend is for vcard directories.
*
* Created   :   01.10.2007
*
* Copyright 2007 - 2011 Zarafa Deutschland GmbH
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License, version 3,
* as published by the Free Software Foundation with the following additional
* term according to sec. 7:
*
* According to sec. 7 of the GNU Affero General Public License, version 3,
* the terms of the AGPL are supplemented with the following terms:
*
* "Zarafa" is a registered trademark of Zarafa B.V.
* "Z-Push" is a registered trademark of Zarafa Deutschland GmbH
* The licensing of the Program under the AGPL does not imply a trademark license.
* Therefore any rights, title and interest in our trademarks remain entirely with us.
*
* However, if you propagate an unmodified version of the Program you are
* allowed to use the term "Z-Push" to indicate that you distribute the Program.
* Furthermore you may use our trademarks where it is necessary to indicate
* the intended purpose of a product or service provided you use it in accordance
* with honest practices in industrial or commercial matters.
* If you want to propagate modified versions of the Program under the name "Z-Push",
* you may only do so if you have a written permission by Zarafa Deutschland GmbH
* (to acquire a permission please contact Zarafa at trademark@zarafa.com).
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* Consult LICENSE file for details
************************************************/
include_once('lib/default/diffbackend/diffbackend.php');

class BackendVCardDir extends BackendDiff {
    /**----------------------------------------------------------------------------------------------------------
     * default backend methods
     */
    private $_conn = null;    

    public function __construct() {
        $this->_conn = mysql_connect(MYSQL_DBHOST, MYSQL_DBUSER, MYSQL_DBPASSWORD);

        if (!$this->_conn) {
            die('Not connected : ' . mysql_error());
        }

        $res = mysql_select_db(MYSQL_DBNAME, $this->_conn);

        if (!$res) {
            die ('Can\'t use testdav : ' . mysql_error());
        }
    }
    /**
     * Authenticates the user
     * Normally some kind of password check would be done here.
     * Alternatively, the password could be ignored and an Apache
     * authentication via mod_auth_* could be done
     *
     * @param string        $username
     * @param string        $domain
     * @param string        $password
     *
     * @access public
     * @return boolean
     */
    public function Logon($username, $domain, $password) {
	    $result = mysql_query("SELECT * FROM kronos_users WHERE email LIKE '$username'", $this->_conn);
        $user = mysql_fetch_assoc($result);

	    ZLog::Write(LOGLEVEL_DEBUG, 'VCDir::Logon - For : ' . $username . ' with : ');

        if(strtolower($user['password']) == sha1($password))
            return true;
        else
            return false;
    }

    /**
     * Logs off
     *
     * @access public
     * @return boolean
     */
    public function Logoff() {
        return true;
    }

    /**
     * Sends an e-mail
     * Not implemented here
     *
     * @param SyncSendMail  $sm     SyncSendMail object
     *
     * @access public
     * @return boolean
     * @throws StatusException
     */
    public function SendMail($sm) {
        return false;
    }

    /**
     * Returns the waste basket
     *
     * @access public
     * @return string
     */
    public function GetWasteBasket() {
        return false;
    }

    /**
     * Returns the content of the named attachment as stream
     * not implemented
     *
     * @param string        $attname
     *
     * @access public
     * @return SyncItemOperationsAttachment
     * @throws StatusException
     */
    public function GetAttachmentData($attname) {
        return false;
    }

    /**----------------------------------------------------------------------------------------------------------
     * implemented DiffBackend methods
     */

    /**
     * Returns a list (array) of folders.
     * In simple implementations like this one, probably just one folder is returned.
     *
     * @access public
     * @return array
     */
    public function GetFolderList() {
        ZLog::Write(LOGLEVEL_DEBUG, 'VCDir::GetFolderList()');
        $contacts = array();
        $folder = $this->StatFolder("root");
        $contacts[] = $folder;

        return $contacts;
    }

    /**
     * Returns an actual SyncFolder object
     *
     * @param string        $id           id of the folder
     *
     * @access public
     * @return object       SyncFolder with information
     */
    public function GetFolder($id) {
        ZLog::Write(LOGLEVEL_DEBUG, 'VCDir::GetFolder('.$id.')');
        if($id == "root") {
            $folder = new SyncFolder();
            $folder->serverid = $id;
            $folder->parentid = "0";
            $folder->displayname = "Contacts";
            $folder->type = SYNC_FOLDER_TYPE_CONTACT;

            return $folder;
        } else return false;
    }

    /**
     * Returns folder stats. An associative array with properties is expected.
     *
     * @param string        $id             id of the folder
     *
     * @access public
     * @return array
     */
    public function StatFolder($id) {
        ZLog::Write(LOGLEVEL_INFO, 'VCDir::StatFolder('.$id.')');
        $folder = $this->GetFolder($id);
		
//		$result = mysql_query("SELECT TOP(synced_at) as last_mod FROM cache_contact", $this->_conn);
//
//        $cache_contact = mysql_fetch_assoc($result);

        $stat = array();
        $stat["id"] = $id;
        $stat["parent"] = $folder->parentid;
        $stat["mod"] = $folder->displayname;
		//$stat["mod"] = strtotime($cache_contact['last_mod']);

        return $stat;
    }

    /**
     * Creates or modifies a folder
     * not implemented
     *
     * @param string        $folderid       id of the parent folder
     * @param string        $oldid          if empty -> new folder created, else folder is to be renamed
     * @param string        $displayname    new folder name (to be created, or to be renamed to)
     * @param int           $type           folder type
     *
     * @access public
     * @return boolean                      status
     * @throws StatusException              could throw specific SYNC_FSSTATUS_* exceptions
     *
     */
    public function ChangeFolder($folderid, $oldid, $displayname, $type){
        return false;
    }

    /**
     * Deletes a folder
     *
     * @param string        $id
     * @param string        $parent         is normally false
     *
     * @access public
     * @return boolean                      status - false if e.g. does not exist
     * @throws StatusException              could throw specific SYNC_FSSTATUS_* exceptions
     *
     */
    public function DeleteFolder($id, $parentid){
        return false;
    }

    /**
     * Returns a list (array) of messages
     *
     * @param string        $folderid       id of the parent folder
     * @param long          $cutoffdate     timestamp in the past from which on messages should be returned
     *
     * @access public
     * @return array/false  array with messages or false if folder is not available
     */
    public function GetMessageList($folderid, $cutoffdate) {
		//TODO read from cache_contact
		ZLog::Write(LOGLEVEL_DEBUG, "VCDir::GetMessageList($folderid, $cutoffdate)");
        $contacts = array();
        $result = mysql_query("SELECT * FROM contact", $this->_conn);

        while($contact = mysql_fetch_assoc($result)) {
			$vcard = array();

			$vcard['id'] = $contact['id'];
			$vcard['mod'] = strtotime($contact["modified_at"]);
			$vcard['flags'] = 1;

			$contacts[] = $vcard;
        }
        
        return $contacts;
    }

    /**
     * Returns the actual SyncXXX object type.
     *
     * @param string            $folderid           id of the parent folder
     * @param string            $id                 id of the message
     * @param ContentParameters $contentparameters  parameters of the requested message (truncation, mimesupport etc)
     *
     * @access public
     * @return object/false     false if the message could not be retrieved
     */
    public function GetMessage($folderid, $id, $contentparameters) {
        ZLog::Write(LOGLEVEL_DEBUG, 'VCDir::GetMessage('.$folderid.', '.$id.', ..)');
        if($folderid != "root")
            return;
        // Parse the vcard
        $result = mysql_query("SELECT * FROM contact WHERE id = " . $id, $this->_conn);
        
        if($result) {
            $contact = mysql_fetch_assoc($result);
            $message = new SyncContact();
            
            $message->lastname = $contact['last_name'];
            $message->firstname = $contact['first_name'];

            $message->email1address = $contact['email'];
            $message->homephonenumber = '415 123-1111';
            $message->homestreet = $contact['address'];
            $message->homecity = $contact['city'];
            $message->homestate = $contact['province'];
            $message->homepostalcode = $contact['postal_code'];
            $message->homecountry = 'canada';

            return $message;
        }
        else {
            return false;
        }
    }

    /**
     * Returns message stats, analogous to the folder stats from StatFolder().
     *
     * @param string        $folderid       id of the folder
     * @param string        $id             id of the message
     *
     * @access public
     * @return array
     */
    public function StatMessage($folderid, $id) {
		//TODO read from cache_contact
        //ZLog::Write(LOGLEVEL_DEBUG, 'VCDir::StatMessage('.$folderid.', '.$id.')');
        $result = mysql_query("SELECT * FROM contact WHERE id = " . $id, $this->_conn);
        
        if($result) {
            $contact = mysql_fetch_assoc($result);
            $message = array();

            $message["mod"] = strtotime($contact["modified_at"]);
            $message["id"] = $id;
            $message["flags"] = 1;

            return $message;
        }
        else {
            return false;
        }
    }

    /**
     * Called when a message has been changed on the mobile.
     * This functionality is not available for emails.
     *
     * @param string        $folderid       id of the folder
     * @param string        $id             id of the message
     * @param SyncXXX       $message        the SyncObject containing a message
     *
     * @access public
     * @return array                        same return value as StatMessage()
     * @throws StatusException              could throw specific SYNC_STATUS_* exceptions
     */
    public function ChangeMessage($folderid, $id, $message) {
		//TODO update this method
        ZLog::Write(LOGLEVEL_DEBUG, 'VCDir::ChangeMessage('.$folderid.', '.$id.', ..)');
		//ZLog::Write(LOGLEVEL_DEBUG, print_r($message, true) . PHP_EOL);
		
		foreach($message as $index => $var) {
			$message->$index = preg_replace('/\r|\n|\r\n|\n\r/', ' ', $var);
		}
		
		if($id) {
			$now = time();
			
			$sql = "UPDATE contact SET email = '" . $message->email1address . "', 
				first_name = '{$message->firstname}', last_name = '{$message->lastname}',
				address = '{$message->homestreet}', postal_code = '{$message->homepostalcode}',
				city = '{$message->homecity}', province = '{$message->homestate}',
				modified_at = '" . date('Y-m-d H:i:s', $now) . "'
				WHERE id = " . $id;

			$result = mysql_query($sql, $this->_conn);
			
//			$sql = "UPDATE cache_contact SET synced_at = '" . date('Y-m-d H:i:s', $now) . "',
//				modified_at = '" . date('Y-m-d H:i:s', $now) . "'
//				WHERE contact_id = " . $id;
//
//			$result = mysql_query($sql, $this->_conn);

			if($result) {
				$message = array();
				$message["mod"] = $now;
				$message["id"] = $id;
				$message["flags"] = 1;

				return $message;
			}
			else {
				return false;
			}
		}
		else {
			return false;
			$now = time();
			
			$sql = "INSERT INTO contact(email, first_name, last_name, address, postal_code, 
				city, province, modified_at) VALUES('{$message->email1address}', 
				'{$message->firstname}', '{$message->lastname}',
				'{$message->homestreet}', '{$message->homepostalcode}',
				'{$message->homecity}', '{$message->homestate}',
				'" . date('Y-m-d H:i:s', $now) . "')";
				
			$result = mysql_query($sql, $this->_conn);
			$id = mysql_insert_id();
			
//			$sql = "INSERT INTO cache_contact(contact_id, action, modified_at, synced_at)
//				VALUES($id, '', '" . date('Y-m-d H:i:s', $now) . "', 
//				'" . date('Y-m-d H:i:s', $now) . "')";
//
//			$result = mysql_query($sql, $this->_conn);

			if($result) {
				$message = array();
				$message["mod"] = $now;
				$message["id"] = $id;
				$message["flags"] = 1;

				return $message;
			}
			else {
				return false;
			}
		}
    }

    /**
     * Changes the 'read' flag of a message on disk
     *
     * @param string        $folderid       id of the folder
     * @param string        $id             id of the message
     * @param int           $flags          read flag of the message
     *
     * @access public
     * @return boolean                      status of the operation
     * @throws StatusException              could throw specific SYNC_STATUS_* exceptions
     */
    public function SetReadFlag($folderid, $id, $flags) {
        return false;
    }

    /**
     * Called when the user has requested to delete (really delete) a message
     *
     * @param string        $folderid       id of the folder
     * @param string        $id             id of the message
     *
     * @access public
     * @return boolean                      status of the operation
     * @throws StatusException              could throw specific SYNC_STATUS_* exceptions
     */
    public function DeleteMessage($folderid, $id) {
        $sql = 'DELETE FROM contact WHERE id = ' . $id;
	    mysql_query($sql, $this->_conn);
		
		return true;
    }

    /**
     * Called when the user moves an item on the PDA from one folder to another
     * not implemented
     *
     * @param string        $folderid       id of the source folder
     * @param string        $id             id of the message
     * @param string        $newfolderid    id of the destination folder
     *
     * @access public
     * @return boolean                      status of the operation
     * @throws StatusException              could throw specific SYNC_MOVEITEMSSTATUS_* exceptions
     */
    public function MoveMessage($folderid, $id, $newfolderid) {
        return false;
    }


    /**----------------------------------------------------------------------------------------------------------
     * private vcard-specific internals
     */

    /**
     * The path we're working on
     *
     * @access private
     * @return string
     */
    private function getPath() {
        return str_replace('%u', $this->store, VCARDDIR_DIR);
    }

    /**
     * Escapes a string
     *
     * @param string        $data           string to be escaped
     *
     * @access private
     * @return string
     */
    function escape($data){
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = $this->escape($val);
            }
            return $data;
        }
        $data = str_replace("\r\n", "\n", $data);
        $data = str_replace("\r", "\n", $data);
        $data = str_replace(array('\\', ';', ',', "\n"), array('\\\\', '\\;', '\\,', '\\n'), $data);
        return u2wi($data);
    }

    /**
     * Un-escapes a string
     *
     * @param string        $data           string to be un-escaped
     *
     * @access private
     * @return string
     */
    function unescape($data){
        $data = str_replace(array('\\\\', '\\;', '\\,', '\\n','\\N'),array('\\', ';', ',', "\n", "\n"),$data);
        return $data;
    }
};
?>
