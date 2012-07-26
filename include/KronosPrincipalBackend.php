<?php

class KronosPrincipalBackend implements Sabre_DAVACL_IPrincipalBackend{

	/**
	 * @var PDO
	 */
	protected $pdo;
	
	public function __construct(PDO $pdo){
		$this->pdo = $pdo;
	}

	/**
	* A list of additional fields to support
	*
	* @var array
	*/
	protected $fieldMap = array(

		/**
		 * This property can be used to display the users' real name.
		 */
		'{DAV:}displayname' => array(
		    'dbField' => 'displayname',
		),

		/**
		 * This property is actually used by the CardDAV plugin, where it gets
		 * mapped to {http://calendarserver.orgi/ns/}me-card.
		 *
		 * The reason we don't straight-up use that property, is because
		 * me-card is defined as a property on the users' addressbook
		 * collection.
		 */
		'{http://sabredav.org/ns}vcard-url' => array(
		    'dbField' => 'vcardurl',
		),
		/**
		 * This is the users' primary email-address.
		 */
		'{http://sabredav.org/ns}email-address' => array(
		    'dbField' => 'email',
		),
	);


	public function getPrincipalsByPrefix($prefixPath) {
		Debug::log('KronosPrincipalBackend.php::getPrincipalsByPrefix');
		Debug::log($prefixPath);
		
		$result = $this->pdo->query('SELECT email, screen_name FROM kronos_users');

		$principals = array();
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$principal = array();
			$principal['{http://sabredav.org/ns}email-address'] = $row['email'];
			$principal['{DAV:}displayname'] = $row['screen_name'];
			$principal['uri'] = $prefixPath.'/'.$row['email'];
			
			$principals[] = $principal;
		}
		Debug::log($principals, true);
		return $principals;

	}

	public function getPrincipalByPath($path) {
		Debug::log('KronosPrincipalBackend.php::getPrincipalByPath');
		Debug::log($path);
		$parts = explode('/', $path);
		$email = $parts[1];

		$stmt = $this->pdo->prepare('SELECT id, email, screen_name FROM kronos_users WHERE email = ?');
		$stmt->execute(array($email));

	        if(!$row = $stmt->fetch(PDO::FETCH_ASSOC)) return;

		$principal = array(
			'id'  => $row['id'],
			'uri' => $path,
			'{http://sabredav.org/ns}email-address' => $row['email'],
			'{DAV:}displayname' => $row['screen_name']
		);
		
		return $principal;
	}

    /**
     * Updates one ore more webdav properties on a principal.
     *
     * The list of mutations is supplied as an array. Each key in the array is
     * a propertyname, such as {DAV:}displayname.
     *
     * Each value is the actual value to be updated. If a value is null, it
     * must be deleted.
     *
     * This method should be atomic. It must either completely succeed, or
     * completely fail. Success and failure can simply be returned as 'true' or
     * 'false'.
     *
     * It is also possible to return detailed failure information. In that case
     * an array such as this should be returned:
     *
     * array(
     *   200 => array(
     *      '{DAV:}prop1' => null,
     *   ),
     *   201 => array(
     *      '{DAV:}prop2' => null,
     *   ),
     *   403 => array(
     *      '{DAV:}prop3' => null,
     *   ),
     *   424 => array(
     *      '{DAV:}prop4' => null,
     *   ),
     * );
     *
     * In this previous example prop1 was successfully updated or deleted, and
     * prop2 was succesfully created.
     *
     * prop3 failed to update due to '403 Forbidden' and because of this prop4
     * also could not be updated with '424 Failed dependency'.
     *
     * This last example was actually incorrect. While 200 and 201 could appear
     * in 1 response, if there's any error (403) the other properties should
     * always fail with 423 (failed dependency).
     *
     * But anyway, if you don't want to scratch your head over this, just
     * return true or false.
     *
     * @param string $path
     * @param array $mutations
     * @return array|bool
     */
    public function updatePrincipal($path, $mutations) {
	    Debug::log('KronosPrincipalBackend.php::updatePrincipal');
	    Debug::log($mutations);
	    Debug::log($path);
//        $updateAble = array();
//        foreach($mutations as $key=>$value) {
//
//            // We are not aware of this field, we must fail.
//            if (!isset($this->fieldMap[$key])) {
//
//                $response = array(
//                    403 => array(
//                        $key => null,
//                    ),
//                    424 => array(),
//                );
//
//                // Adding the rest to the response as a 424
//                foreach($mutations as $subKey=>$subValue) {
//                    if ($subKey !== $key) {
//                        $response[424][$subKey] = null;
//                    }
//                }
//                return $response;
//            }
//
//            $updateAble[$this->fieldMap[$key]['dbField']] = $value;
//
//        }
//
//        // No fields to update
//        $query = "UPDATE " . $this->tableName . " SET ";
//
//        $first = true;
//        foreach($updateAble as $key => $value) {
//            if (!$first) {
//                $query.= ', ';
//            }
//            $first = false;
//            $query.= "$key = :$key ";
//        }
//        $query.='WHERE uri = :uri';
//        $stmt = $this->pdo->prepare($query);
//        $updateAble['uri'] =  $path;
//        $stmt->execute($updateAble);

        return true;

    }

	/**
	* This method is used to search for principals matching a set of
	* properties.
	*
	* This search is specifically used by RFC3744's principal-property-search
	* REPORT. You should at least allow searching on
	* http://sabredav.org/ns}email-address.
	*
	* The actual search should be a unicode-non-case-sensitive search. The
	* keys in searchProperties are the WebDAV property names, while the values
	* are the property values to search on.
	*
	* If multiple properties are being searched on, the search should be
	* AND'ed.
	*
	* This method should simply return an array with full principal uri's.
	*
	* If somebody attempted to search on a property the backend does not
	* support, you should simply return 0 results.
	*
	* You can also just return 0 results if you choose to not support
	* searching at all, but keep in mind that this may stop certain features
	* from working.
	*
	* @param string $prefixPath
	* @param array $searchProperties
	* @return array
	*/
	public function searchPrincipals($prefixPath, array $searchProperties) {
		Debug::log('KronosPrincipalBackend.php::searchPrincipals');
		Debug::log($searchProperties);
		Debug::log($prefixPath);
		$query = 'SELECT email FROM kronos_users WHERE 1=1 ';
		$values = array();
		foreach($searchProperties as $property => $value) {
			switch($property) {
				case '{DAV:}displayname' :
					$query.=' AND screen_name LIKE ?';
					$values[] = '%' . $value . '%';
					break;
				case '{http://sabredav.org/ns}email-address' :
					$query.=' AND email LIKE ?';
					$values[] = '%' . $value . '%';
					break;
				default :
					return array();
			}

		}
		$stmt = $this->pdo->prepare($query);
		$stmt->execute($values);

		$principals = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$principals[] = 'principals/'.$row['email'];
		}

		return $principals;

	}

	/**
	* Returns the list of members for a group-principal
	*
	* @param string $principal
	* @return array
	*/
	public function getGroupMemberSet($principal) {
		Debug::log('KronosPrincipalBackend.php::getGroupMemberSet');
		Debug::log($principal);
		return array();

	}

	/**
	* Returns the list of groups a principal is a member of
	*
	* @param string $principal
	* @return array
	*/
	public function getGroupMembership($principal) {
		Debug::log('KronosPrincipalBackend.php::getGroupMembership');
		Debug::log($principal);
		return array();

	}

	/**
	* Updates the list of group members for a group principal.
	*
	* The principals should be passed as a list of uri's.
	*
	* @param string $principal
	* @param array $members
	* @return void
	*/
	public function setGroupMemberSet($principal, array $members) {
		Debug::log('KronosPrincipalBackend.php::setGroupMemberSet');
		Debug::log($principal);
		Debug::log($members);
	}
}