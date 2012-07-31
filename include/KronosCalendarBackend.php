<?php


class KronosCalendarBackend extends Sabre_CalDAV_Backend_Abstract {

    /**
     * pdo
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The table name that will be used for calendars
     *
     * @var string
     */
    protected $calendarTableName;

    /**
     * The table name that will be used for calendar objects
     *
     * @var string
     */
    protected $calendarObjectTableName;

    /**
     * List of CalDAV properties, and how they map to database fieldnames
     *
     * Add your own properties by simply adding on to this array
     *
     * @var array
     */
    public $propertyMap = array(
        '{DAV:}displayname'                          => 'displayname',
        '{urn:ietf:params:xml:ns:caldav}calendar-description' => 'description',
        '{urn:ietf:params:xml:ns:caldav}calendar-timezone'    => 'timezone',
        '{http://apple.com/ns/ical/}calendar-order'  => 'calendarorder',
        '{http://apple.com/ns/ical/}calendar-color'  => 'calendarcolor',
    );

    /**
     * Creates the backend
     *
     * @param PDO $pdo
     * @param string $calendarTableName
     * @param string $calendarObjectTableName
     */
    public function __construct(PDO $pdo) {

        $this->pdo = $pdo;

    }

    /**
     * Returns a list of calendars for a principal.
     *
     * Every project is an array with the following keys:
     *  * id, a unique id that will be used by other functions to modify the
     *    calendar. This can be the same as the uri or a database key.
     *  * uri, which the basename of the uri with which the calendar is
     *    accessed.
     *  * principaluri. The owner of the calendar. Almost always the same as
     *    principalUri passed to this method.
     *
     * Furthermore it can contain webdav properties in clark notation. A very
     * common one is '{DAV:}displayname'.
     *
     * @param string $principalUri
     * @return array
     */
    public function getCalendarsForUser($principalUri) {

	$parts = explode('/', $principalUri);
	$email = $parts[count($parts) - 1];
	$sql = 'SELECT MAX(modified) AS max_modified, MAX(created) as max_created
	        FROM kronos_agenda a 
	        INNER JOIN kronos_users u ON a.fk_kronos_users = a.id
	        WHERE u.email = ?';
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute(array($email));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$row['max_modified'] = strtotime($row['max_modified']);
	$row['max_created'] = strtotime($row['max_created']);
	
	$ctag = ( $row['max_created'] > $row['max_modified'] ? $row['max_created'] : $row['max_modified'] );

	
	$calendar = array(
		'id' => $email,
		'uri' => 'kronos',
		'principaluri' => $principalUri,
		'{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $ctag,
		'{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet(array('VEVENT')),
		'{DAV:}displayname' => 'kronos',
		'{urn:ietf:params:xml:ns:caldav}calendar-description' => 'Kronos Calendar'
	);

	$calendars[] = $calendar;

        return $calendars;

    }

    public function createCalendar($principalUri, $calendarUri, array $properties) {

        throw new Sabre_DAV_Exception_Forbidden('Creating calendar is forbidden');

    }

    public function updateCalendar($calendarId, array $mutations) {

        throw new Sabre_DAV_Exception_Forbidden('Updating calendar is forbidden');

    }

    public function deleteCalendar($calendarId) {

        throw new Sabre_DAV_Exception_Forbidden('Deleting calendar is forbidden');

    }

    /**
     * Returns all calendar objects within a calendar.
     *
     * Every item contains an array with the following keys:
     *   * id - unique identifier which will be used for subsequent updates
     *   * calendardata - The iCalendar-compatible calendar data
     *   * uri - a unique key which will be used to construct the uri. This can be any arbitrary string.
     *   * lastmodified - a timestamp of the last modification time
     *   * etag - An arbitrary string, surrounded by double-quotes. (e.g.:
     *   '  "abcdef"')
     *   * calendarid - The calendarid as it was passed to this function.
     *   * size - The size of the calendar objects, in bytes.
     *
     * Note that the etag is optional, but it's highly encouraged to return for
     * speed reasons.
     *
     * The calendardata is also optional. If it's not returned
     * 'getCalendarObject' will be called later, which *is* expected to return
     * calendardata.
     *
     * If neither etag or size are specified, the calendardata will be
     * used/fetched to determine these numbers. If both are specified the
     * amount of times this is needed is reduced by a great degree.
     *
     * @param string $calendarId
     * @return array
     */
    public function getCalendarObjects($calendarId) {

	    
	$sql = 'SELECT a.*
	        FROM kronos_agenda a 
	        INNER JOIN kronos_users u ON a.fk_kronos_users = u.id
	        WHERE u.email = ?';
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute(array($calendarId));
	$ret = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$uri = $row['uri'];
		if(empty($uri)) $uri = $row['id'].'-'.strtotime($row['created']).'-'.$calendarId.'.ics';
		$created = strtotime($row['created']);
		$modified = strtotime($row['modified']);
		
		$obj['id'] = $row['id'];
		$obj['uri'] = $uri;
		$obj['lastmodified'] = ($modified ? $modified : $created);
		$obj['etag'] = '"'.$obj['lastmodified'].'"';
		$obj['calendarid'] = $calendarId;
		$obj['calendardata'] = 'BEGIN:VCALENDAR
CALSCALE:GREGORIAN
VERSION:1.0
BEGIN:VTIMEZONE
TZID:/freeassociation.sourceforge.net/Tzfile/America/Montreal
X-LIC-LOCATION:America/Montreal
BEGIN:STANDARD
TZNAME:EST
DTSTART:19701104T020000
RRULE:FREQ=YEARLY;BYDAY=1SU;BYMONTH=11
TZOFFSETFROM:-0400
TZOFFSETTO:-0500
END:STANDARD
BEGIN:DAYLIGHT
TZNAME:EDT
DTSTART:19700311T020000
RRULE:FREQ=YEARLY;BYDAY=2SU;BYMONTH=3
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VEVENT
UID:'.$row['uri'].'
CREATED:'.$this->CompactDateTime($row['created']).'
DTSTAMP:'.$this->CompactDateTime($row['created']).'
CATEGORIES:'.$row['type'].'
SUMMARY:'.$row['subject'].'
DESCRIPTION:'.$row['notes'].'
DTSTART;TZID=/freeassociation.sourceforge.net/Tzfile/America/Montreal:'.$this->CompactDateTime($row['time_start']).'
DTEND;TZID=/freeassociation.sourceforge.net/Tzfile/America/Montreal:'.$this->CompactDateTime($row['time_end']).'
CLASS:'.( $row['private'] == 'Y' ? 'PRIVATE' : 'PUBLIC' ).'
END:VEVENT
END:VCALENDAR
';
		$ret[] = $obj;
	}
	return $ret;
    }

    /**
     * Returns information from a single calendar object, based on it's object
     * uri.
     *
     * The returned array must have the same keys as getCalendarObjects. The
     * 'calendardata' object is required here though, while it's not required
     * for getCalendarObjects.
     *
     * @param string $calendarId
     * @param string $objectUri
     * @return array
     */
    public function getCalendarObject($calendarId,$objectUri) {	
	$sql = 'SELECT a.*
	        FROM kronos_agenda a 
	        WHERE a.uri = ?';
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute(array($objectUri));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if(!$row) return array();
	
	$created = strtotime($row['created']);
	$modified = strtotime($row['modified']);
	
	$ret['id'] = $row['id'];
	$ret['uri'] = $objectUri;
	$ret['lastmodified'] = ($modified ? $modified : $created);
	$ret['etag'] = '"'.$ret['lastmodified'].'"';
	$ret['calendarid'] = $calendarId;
	$ret['calendardata'] = 'BEGIN:VCALENDAR
CALSCALE:GREGORIAN
VERSION:2.0
BEGIN:VTIMEZONE
TZID:/freeassociation.sourceforge.net/Tzfile/America/Montreal
X-LIC-LOCATION:America/Montreal
BEGIN:STANDARD
TZNAME:EST
DTSTART:19701104T020000
RRULE:FREQ=YEARLY;BYDAY=1SU;BYMONTH=11
TZOFFSETFROM:-0400
TZOFFSETTO:-0500
END:STANDARD
BEGIN:DAYLIGHT
TZNAME:EDT
DTSTART:19700311T020000
RRULE:FREQ=YEARLY;BYDAY=2SU;BYMONTH=3
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VEVENT
UID:'.$row['uri'].'
CATEGORIES:'.$row['type'].'
SUMMARY:'.$row['subject'].'
DESCRIPTION:'.$row['notes'].'
CREATED:'.$this->CompactDateTime($row['created']).'
DTSTAMP:'.$this->CompactDateTime($row['created']).'
DTSTART;TZID=/freeassociation.sourceforge.net/Tzfile/America/Montreal:'.$this->CompactDateTime($row['time_start']).'
DTEND;TZID=/freeassociation.sourceforge.net/Tzfile/America/Montreal:'.$this->CompactDateTime($row['time_end']).'
CLASS:'.( $row['private'] == 'Y' ? 'PRIVATE' : 'PUBLIC' ).'
END:VEVENT
END:VCALENDAR
';
	
	return $ret;
    }

    /**
     * Creates a new calendar object.
     *
     * @param string $calendarId
     * @param string $objectUri
     * @param string $calendarData
     * @return void
     */
    public function createCalendarObject($calendarId,$objectUri,$calendarData) {
	$sql = 'SELECT id FROM kronos_users WHERE email = ?';
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute(array($calendarId));
	if(!$row = $stmt->fetch(PDO::FETCH_ASSOC)) throw new Sabre_DAV_Exception_Forbidden('Not authorized if not authenticated.');
	$user_id = $row['id'];
	
	$parts = explode("\r\n", $calendarData);
	
	$description = null;
	$categories = null;
	$summary = null;
	$class = null;
	$time_start = null;
	$time_end = null;
	foreach($parts as $key => $part){
		if(empty($part)) continue;
		
		$value = explode(':', $part);
		if($value[0] === 'DESCRIPTION')
			$description = $value[1];
		elseif($value[0] === 'CATEGORIES')
			$categories = $value[1];
		elseif($value[0] === 'SUMMARY')
			$summary = $value[1];
		elseif($value[0] === 'CLASS')
			$class = ($value[1] == 'PUBLIC' ? 'Y' : 'N');
		elseif(strpos($value[0], 'DTSTART') !== false){
			if(empty($value[1])) $value[1] = $parts[$key + 1];
			$time_start = date('Y-m-d H:i:s', strtotime($value[1]));
		}
		elseif(strpos($value[0], 'DTEND') !== false){
			if(empty($value[1])) $value[1] = $parts[$key + 1];
			$time_end = date('Y-m-d H:i:s', strtotime($value[1]));
		}
		
	}
	$created = date('Y-m-d H:i:s');
	
	$sql = 'INSERT INTO kronos_agenda(fk_kronos_users, created, modified, notes, type, subject, private, time_start, time_end, uri) 
	        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
	$stmt = $this->pdo->prepare($sql);
	$stmt->execute(array($user_id, $created, null, $description, $categories, $summary, $class, $time_start, $time_end, $objectUri ));
	$agenda_id = $this->pdo->lastInsertId();
	
	return '"'.strtotime($created).'"';

    }

    /**
     * Updates an existing calendarobject, based on it's uri.
     *
     * @param string $calendarId
     * @param string $objectUri
     * @param string $calendarData
     * @return void
     */
    public function updateCalendarObject($calendarId,$objectUri,$calendarData) {
	
	$parts = explode("\r\n", $calendarData);
	
	$description = null;
	$categories = null;
	$summary = null;
	$class = null;
	$time_start = null;
	$time_end = null;
	foreach($parts as $key => $part){
		if(empty($part)) continue;
		
		$value = explode(':', $part);
		if($value[0] === 'DESCRIPTION')
			$description = $value[1];
		elseif($value[0] === 'CATEGORIES')
			$categories = $value[1];
		elseif($value[0] === 'SUMMARY')
			$summary = $value[1];
		elseif($value[0] === 'CLASS')
			$class = ($value[1] == 'PUBLIC' ? 'Y' : 'N');
		elseif(strpos($value[0], 'DTSTART') !== false){
			if(empty($value[1])) $value[1] = $parts[$key + 1];
			$time_start = date('Y-m-d H:i:s', strtotime($value[1]));
		}
		elseif(strpos($value[0], 'DTEND') !== false){
			if(empty($value[1])) $value[1] = $parts[$key + 1];
			$time_end = date('Y-m-d H:i:s', strtotime($value[1]));
		}
		
	}
	$modified = date('Y-m-d H:i:s');
	
	$sql = 'UPDATE kronos_agenda SET modified = ?, notes = ?, type = ?, subject = ?, private = ?, time_start = ?, time_end = ? WHERE uri = ?';
	$stmt = $this->pdo($sql);
	$stmt->execute(array($modified, $description, $categories, $summary, $class, $time_start, $time_end, $objectUri));
	
	return '"'.strtotime($modified).'"';
    }

    /**
     * Deletes an existing calendar object.
     *
     * @param string $calendarId
     * @param string $objectUri
     * @return void
     */
    public function deleteCalendarObject($calendarId,$objectUri) {
	    
	    $sql = 'DELETE FROM kronos_agenda WHERE uri = ?';
	    $stmt = $this->pdo->prepare($sql);
	    $stmt->execute(array($objectUri));
	    
	    return true;

    }
    
	private function CompactDateTime($dt)
	{
		$len = strlen($dt);
		$result = '';
		$d = 0;
		$T = true;
		for ($i = 0; $i < $len; ++$i) {
			if ($dt{$i} == '-' || $dt{$i} == ':' ) continue;
			if ($dt{$i} == ' ') {
				if ($T) $result.='T';
				$T = false;
			}
			else $result .= $dt{$i};
		}

		return $result;
	}
	
	private function QuotedPrintableEncode($str){
		//TODO: repair line folding. it is broken to comply with synthesis client which seems to not recognize soft line breaks
		//TODO: enforce 6.7.3 of rfc 2045
		$nl = "\r\n";
		$result = '';

		$lineLen = 0;
		$wasSpace = false;

		$len = strlen($str);
		for ($i = 0; $i < $len; ++$i ) {

			$chr = $str{$i};
			$ord = ord($chr);

			if ($ord >= 33 && $ord < 60 && $ord != 38 || $ord > 62 && $ord <= 126) {//we specially encode < and >
				$result .= $chr;
				++$lineLen;
				if ($lineLen >= 33) {
					//$result .= '='.$nl;
					$lineLen = 0;
				}
				//} else if () {

			} else {
				$result .= '=';
				if ($ord <= 15) {
					$result .= '0';
				}
				$lineLen += 3;
				$result.= strtoupper(dechex($ord));
				if ($lineLen >= 33) {
					//$result .= '='.$nl;
					$lineLen = 0;
				}
			}
		}
		return $result;
	}


}
