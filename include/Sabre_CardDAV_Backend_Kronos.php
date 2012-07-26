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
class Sabre_CardDAV_Backend_Kronos extends Sabre_CardDAV_Backend_Abstract {

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

        //$stmt = $this->pdo->prepare('SELECT id, carddata, uri, lastmodified FROM ' . $this->cardsTableName . ' WHERE addressbookid = ?');
        //$stmt->execute(array($addressbookId));

        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
		$data = array(array('carddata' => 'BEGIN:VCARD
VERSION:3.0
N:Gump;Forrest
FN:Forrest Gump
ORG:Bubba Gump Shrimp Co.
TITLE:Shrimp Man
PHOTO;VALUE=URL;TYPE=GIF:http://www.example.com/dir_photos/my_photo.gif
TEL;TYPE=WORK,VOICE:(111) 555-1212
TEL;TYPE=HOME,VOICE:(404) 555-1212
ADR;TYPE=WORK:;;100 Waters Edge;Baytown;LA;30314;United States of America
LABEL;TYPE=WORK:100 Waters Edge\nBaytown, LA 30314\nUnited States of America
ADR;TYPE=HOME:;;42 Plantation St.;Baytown;LA;30314;United States of America
LABEL;TYPE=HOME:42 Plantation St.\nBaytown, LA 30314\nUnited States of America
EMAIL;TYPE=PREF,INTERNET:forrestgump@example.com
REV:2008-04-24T19:52:43Z
END:VCARD',
				'uri' => '1',
				'lastmodified' => time()));
		
		return $data;
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

//        $stmt = $this->pdo->prepare('SELECT id, carddata, uri, lastmodified FROM ' . $this->cardsTableName . ' WHERE addressbookid = ? AND uri = ? LIMIT 1');
//        $stmt->execute(array($addressBookId, $cardUri));
//
//        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//        return (count($result)>0?$result[0]:false);
		$data = array('carddata' => 'BEGIN:VCARD
VERSION:3.0
N:Gump;Forrest
FN:Forrest Gump
ORG:Bubba Gump Shrimp Co.
TITLE:Shrimp Man
PHOTO;VALUE=URL;TYPE=GIF:http://www.example.com/dir_photos/my_photo.gif
TEL;TYPE=WORK,VOICE:(111) 555-1212
TEL;TYPE=HOME,VOICE:(404) 555-1212
ADR;TYPE=WORK:;;100 Waters Edge;Baytown;LA;30314;United States of America
LABEL;TYPE=WORK:100 Waters Edge\nBaytown, LA 30314\nUnited States of America
ADR;TYPE=HOME:;;42 Plantation St.;Baytown;LA;30314;United States of America
LABEL;TYPE=HOME:42 Plantation St.\nBaytown, LA 30314\nUnited States of America
EMAIL;TYPE=PREF,INTERNET:forrestgump@example.com
REV:2008-04-24T19:52:43Z
END:VCARD',
				'uri' => '1',
				'lastmodified' => time());
		//var_dump(debug_backtrace());
		//debug_print_backtrace();
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

        $stmt = $this->pdo->prepare('INSERT INTO ' . $this->cardsTableName . ' (carddata, uri, lastmodified, addressbookid) VALUES (?, ?, ?, ?)');

        $result = $stmt->execute(array($cardData, $cardUri, time(), $addressBookId));

        $stmt2 = $this->pdo->prepare('UPDATE ' . $this->addressBooksTableName . ' SET ctag = ctag + 1 WHERE id = ?');
        $stmt2->execute(array($addressBookId));

        return '"' . md5($cardData) . '"';

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

        $stmt = $this->pdo->prepare('UPDATE ' . $this->cardsTableName . ' SET carddata = ?, lastmodified = ? WHERE uri = ? AND addressbookid =?');
        $stmt->execute(array($cardData, time(), $cardUri, $addressBookId));

        $stmt2 = $this->pdo->prepare('UPDATE ' . $this->addressBooksTableName . ' SET ctag = ctag + 1 WHERE id = ?');
        $stmt2->execute(array($addressBookId));

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

        $stmt = $this->pdo->prepare('DELETE FROM ' . $this->cardsTableName . ' WHERE addressbookid = ? AND uri = ?');
        $stmt->execute(array($addressBookId, $cardUri));

        $stmt2 = $this->pdo->prepare('UPDATE ' . $this->addressBooksTableName . ' SET ctag = ctag + 1 WHERE id = ?');
        $stmt2->execute(array($addressBookId));

        return $stmt->rowCount()===1;

    }
}
