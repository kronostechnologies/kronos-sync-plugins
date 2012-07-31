<?php

/**
 * PDO CardDAV backend
 *
 * This CardDAV backend uses PDO to store addressbooks
 *
 * @package Sabre
 * @subpackage CardDAV
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class KronosContactBackend extends Sabre_CardDAV_Backend_Abstract {

    /**
     * PDO connection
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The PDO table name used to store addressbooks
     */
    protected $addressBooksTableName;

    /**
     * The PDO table name used to store cards
     */
    protected $cardsTableName;

    /**
     * Sets up the object
     *
     * @param PDO $pdo
     * @param string $addressBooksTableName
     * @param string $cardsTableName
     */
    public function __construct(PDO $pdo, $addressBooksTableName = '', $cardsTableName = 'contact') {

        $this->pdo = $pdo;
        $this->addressBooksTableName = $addressBooksTableName;
        $this->cardsTableName = $cardsTableName;
    }

    /**
     * (Returns the list of addressbooks for a specific user.)
     * Somewhat implemented since there is no addressbook concept in Kronos
     * @param string $principalUri
     * @return array
     */
    public function getAddressBooksForUser($principalUri) {
		
        $addressBooks = array();

		$addressBooks[] = array(
			'id'  => '1',
			'uri' => 'kronos',
			'principaluri' => $principalUri,
			'{DAV:}displayname' => 'kronos',
			'{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}addressbook-description' => 'Kronos list of contacts',
			'{http://calendarserver.org/ns/}getctag' => '1',
			'{' . Sabre_CardDAV_Plugin::NS_CARDDAV . '}supported-address-data' =>
				new Sabre_CardDAV_Property_SupportedAddressData(),
		);

        return $addressBooks;
    }


    /**
     * (Updates an addressbook's properties)
     * Not implemented since there is no addressbook concept in Kronos
	 * 
     * See Sabre_DAV_IProperties for a description of the mutations array, as
     * well as the return value.
     *
     * @param mixed $addressBookId
     * @param array $mutations
     * @see Sabre_DAV_IProperties::updateProperties
     * @return bool|array
     */
    public function updateAddressBook($addressBookId, array $mutations) {

        return true;
    }

    /**
     * (Creates a new address book)
     * Not implemented since there is no addressbook concept in Kronos
     * @param string $principalUri
     * @param string $url Just the 'basename' of the url.
     * @param array $properties
     * @return void
     */
    public function createAddressBook($principalUri, $url, array $properties) {

    }

    /**
     * (Deletes an entire addressbook and all its contents)
     * Not implemented since there is no addressbook concept in Kronos
     * @param int $addressBookId
     * @return void
     */
    public function deleteAddressBook($addressBookId) {

    }

    /**
     * Returns all cards for a specific addressbook id.
     *
     * This method should return the following properties for each card:
     *   * carddata - raw vcard data
     *   * uri - Some unique url
     *   * lastmodified - A unix timestamp
     *
     * It's recommended to also return the following properties:
     *   * etag - A unique etag. This must change every time the card changes.
     *   * size - The size of the card in bytes.
     *
     * If these last two properties are provided, less time will be spent
     * calculating them. If they are specified, you can also ommit carddata.
     * This may speed up certain requests, especially with large cards.
     *
     * @param mixed $addressbookId
     * @return array
     */
    public function getCards($addressbookId) {

        $stmt = $this->pdo->prepare('SELECT * FROM contact');
        $stmt->execute();
		
		$cards = array();
		
		//Better be careful with those tabs and whitespace
		while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$data = array();

			$data['carddata'] = $this->generateVCard($result);
			$data['uri'] = $result['id'];
			$data['lastmodified'] = $result['modified_at'];
			
			$cards[] = $data;
		}
		
		return $cards;
    }

    /**
     * Returns a specfic card.
     *
     * The same set of properties must be returned as with getCards. The only
     * exception is that 'carddata' is absolutely required.
     *
     * @param mixed $addressBookId
     * @param string $cardUri
     * @return array
     */
    public function getCard($addressBookId, $cardUri) {

        $stmt = $this->pdo->prepare('SELECT * FROM contact WHERE id = ? LIMIT 1');
        $stmt->execute(array($cardUri));
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$data = array();
		
		//Better be careful with those tabs and whitespace
		if($result) {
			$data['carddata'] = $this->generateVCard($result);
			$data['uri'] = $result['id'];
			$data['lastmodified'] = $result['modified_at'];
		}

		return $data;
    }

    /**
     * Creates a new card.
     *
     * The addressbook id will be passed as the first argument. This is the
     * same id as it is returned from the getAddressbooksForUser method.
     *
     * The cardUri is a base uri, and doesn't include the full path. The
     * cardData argument is the vcard body, and is passed as a string.
     *
     * It is possible to return an ETag from this method. This ETag is for the
     * newly created resource, and must be enclosed with double quotes (that
     * is, the string itself must contain the double quotes).
     *
     * You should only return the ETag if you store the carddata as-is. If a
     * subsequent GET request on the same card does not have the same body,
     * byte-by-byte and you did return an ETag here, clients tend to get
     * confused.
     *
     * If you don't return an ETag, you can just return null.
     *
     * @param mixed $addressBookId
     * @param string $cardUri
     * @param string $cardData
     * @return string|null
     */
    public function createCard($addressBookId, $cardUri, $cardData) {

		$lines = explode(PHP_EOL, $cardData);
		$contact = array();
		
		foreach($lines as $line) {
			$parts = explode(':', $line);
			//Retarded iPhone adds itemX. in front of some tags (like ADR or EMAIL)
			$parts[0] = preg_replace('/item[\d]+\./', '', $parts[0]);
			
			if(strpos($parts[0], ';') !== false) {
				$subparts = explode(';', $parts[0]);
				$parts[0] = $subparts[0];
			}
			
			switch($parts[0]) {			
				case 'N':
					list($contact['last_name'], $contact['first_name']) = explode(';', $parts[1]);
					break;
				case 'ADR':
					$subparts = explode(';', $parts[1]);
					
					$contact['address'] = $subparts[2];
					$contact['postal_code'] = $subparts[5];
					$contact['city'] = $subparts[3];
					
					break;
				case 'EMAIL':
					$contact['email'] = $parts[1];
					break;
			}
		}
		
		if($contact['first_name'] && $contact['last_name']) {
			$sql = 'SELECT id FROM contact WHERE first_name LIKE ? AND last_name LIKE ?';
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute(array($contact['first_name'], $contact['last_name']));
			
			$results = $stmt->fetchAll();
			
			if($results)
				$this->updateCard($addressBookId, $cardUri, $cardData);
			else {
				$sql = 'INSERT INTO contact(email, first_name, last_name, address, postal_code, city, modified_at) 
					VALUES(?, ?, ?, ?, ?, ?, NOW())';
				$stmt = $this->pdo->prepare($sql);

				$stmt->execute(array($contact['email'], $contact['first_name'], $contact['last_name'], 
					$contact['address'], $contact['postal_code'], $contact['city']));

				return '"' . md5($cardData) . '"';
			}		
		}
		else
			return false;
    }

    /**
     * Updates a card.
     *
     * The addressbook id will be passed as the first argument. This is the
     * same id as it is returned from the getAddressbooksForUser method.
     *
     * The cardUri is a base uri, and doesn't include the full path. The
     * cardData argument is the vcard body, and is passed as a string.
     *
     * It is possible to return an ETag from this method. This ETag should
     * match that of the updated resource, and must be enclosed with double
     * quotes (that is: the string itself must contain the actual quotes).
     *
     * You should only return the ETag if you store the carddata as-is. If a
     * subsequent GET request on the same card does not have the same body,
     * byte-by-byte and you did return an ETag here, clients tend to get
     * confused.
     *
     * If you don't return an ETag, you can just return null.
     *
     * @param mixed $addressBookId
     * @param string $cardUri
     * @param string $cardData
     * @return string|null
     */
    public function updateCard($addressBookId, $cardUri, $cardData) {
		$lines = explode(PHP_EOL, $cardData);
		$contact = array();
		//Debug::log($cardData);
		foreach($lines as $line) {
			$parts = explode(':', $line);
			//Retarded iPhone adds itemX. in front of some tags (like ADR or EMAIL)
			$parts[0] = preg_replace('/item[\d]+\./', '', $parts[0]);
			
			if(strpos($parts[0], ';') !== false) {
				$subparts = explode(';', $parts[0]);
				$parts[0] = $subparts[0];
			}
			
			switch($parts[0]) {			
				case 'N':
					list($contact['last_name'], $contact['first_name']) = explode(';', $parts[1]);
					break;
				case 'ADR':
					$subparts = explode(';', $parts[1]);
					
					$contact['address'] = $subparts[2];
					$contact['postal_code'] = $subparts[5];
					$contact['city'] = $subparts[3];
					
					break;
				case 'EMAIL':
					$contact['email'] = $parts[1];
					break;
			}
		}
		
//		$sql = 'UPDATE contact SET email = :email, first_name = :first_name, last_name = :last_name, address = :address, 
//			postal_code = :postal_code, city = :city, modified_at = NOW() WHERE id = :id';
//		$stmt = $this->pdo->prepare($sql);
//		
//		$stmt->bindParam(':id', $cardUri);
//		$stmt->bindParam(':email', $contact['email']);
//		$stmt->bindParam(':first_name', $contact['first_name']);
//		$stmt->bindParam(':last_name', $contact['last_name']);
//		$stmt->bindParam(':address', $contact['address']);
//		$stmt->bindParam(':postal_code', $contact['postal_code']);
//		$stmt->bindParam(':city', $contact['city']);
		$sql = 'UPDATE contact SET modified_at = NOW() WHERE id = :id';
		$stmt = $this->pdo->prepare($sql);
//		
		$stmt->bindParam(':id', $cardUri);
//		$stmt->bindParam(':email', $contact['email']);
//		$stmt->bindParam(':first_name', $contact['first_name']);
//		$stmt->bindParam(':last_name', $contact['last_name']);
//		$stmt->bindParam(':address', $contact['address']);
//		$stmt->bindParam(':postal_code', $contact['postal_code']);
//		$stmt->bindParam(':city', $contact['city']);
		
		$stmt->execute();
		ééDebug::log($stmt->queryString);
		return '"' . md5($cardData) . '"';
    }

    /**
     * Deletes a card
     *
     * @param mixed $addressBookId
     * @param string $cardUri
     * @return bool
     */
    public function deleteCard($addressBookId, $cardUri) {

//        $stmt = $this->pdo->prepare('DELETE FROM ' . $this->cardsTableName . ' WHERE addressbookid = ? AND uri = ?');
//        $stmt->execute(array($addressBookId, $cardUri));
//
//        $stmt2 = $this->pdo->prepare('UPDATE ' . $this->addressBooksTableName . ' SET ctag = ctag + 1 WHERE id = ?');
//        $stmt2->execute(array($addressBookId));
//
//        return $stmt->rowCount()===1;

    }
	
	protected function generateVCard($result) {
		return "BEGIN:VCARD
VERSION:3.0
N:{$result['last_name']};{$result['first_name']}
FN:{$result['first_name']}
ORG:Bubba Gump Shrimp Co.
TITLE:Shrimp Man
TEL;TYPE=HOME:(404) 555-1212
ADR;TYPE=WORK:;;{$result['address']};{$result['city']};;{$result['postal_code']};Canada
EMAIL;TYPE=PREF,INTERNET:{$result['email']}
REV:2008-04-24T19:52:43Z
END:VCARD;";
	}
}
