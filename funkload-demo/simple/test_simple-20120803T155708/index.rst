======================
FunkLoad_ bench report
======================


:date: 2012-08-03 15:57:08
:abstract: Simply testing a default static page
           Bench result of ``Dav.test_simple``: 
           Access a DAV server and query the addressbook

.. _FunkLoad: http://funkload.nuxeo.org/
.. sectnum::    :depth: 2
.. contents:: Table of contents
.. |APDEXT| replace:: \ :sub:`1.5`

Bench configuration
-------------------

* Launched: 2012-08-03 15:57:08
* From: belial
* Test: ``test_Dav.py Dav.test_simple``
* Target server: http://10.0.10.116/sabredav/server.php/
* Cycles of concurrent users: [25, 50, 100]
* Cycle duration: 30s
* Sleeptime between request: from 0.0s to 0.5s
* Sleeptime between test case: 0.01s
* Startup delay between thread: 0.01s
* Apdex: |APDEXT|
* FunkLoad_ version: 1.16.1


Bench content
-------------

The test ``Dav.test_simple`` contains: 

* 2 page(s)
* 0 redirect(s)
* 0 link(s)
* 0 image(s)
* 0 XML RPC call(s)

The bench contains:

* 622 tests
* 0 pages
* 1294 requests


Test stats
----------

The number of Successful **Tests** Per Second (STPS) over Concurrent Users (CUs).

 .. image:: tests.png

 ================== ================== ================== ================== ==================
                CUs               STPS              TOTAL            SUCCESS              ERROR
 ================== ================== ================== ================== ==================
                 25              3.367                101                101             0.00%
                 50              6.767                203                203             0.00%
                100             10.600                318                318             0.00%
 ================== ================== ================== ================== ==================



Page stats
----------

The number of Successful **Pages** Per Second (SPPS) over Concurrent Users (CUs).
Note that an XML RPC call count like a page.

 .. image:: pages_spps.png
 .. image:: pages.png

 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                CUs             Apdex*             Rating               SPPS            maxSPPS              TOTAL            SUCCESS              ERROR                MIN                AVG                MAX                P10                MED                P90                P95
 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                 25              0.000       UNACCEPTABLE              0.000              0.000                  0                  0             0.00%              0.000              0.000              0.000             -1.000             -1.000             -1.000             -1.000
                 50              0.000       UNACCEPTABLE              0.000              0.000                  0                  0             0.00%              0.000              0.000              0.000             -1.000             -1.000             -1.000             -1.000
                100              0.000       UNACCEPTABLE              0.000              0.000                  0                  0             0.00%              0.000              0.000              0.000             -1.000             -1.000             -1.000             -1.000
 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================

 \* Apdex |APDEXT|

Request stats
-------------

The number of **Requests** Per Second (RPS) successful or not over Concurrent Users (CUs).

 .. image:: requests_rps.png
 .. image:: requests.png

 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                CUs             Apdex*            Rating*                RPS             maxRPS              TOTAL            SUCCESS              ERROR                MIN                AVG                MAX                P10                MED                P90                P95
 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                 25              0.636               POOR              6.967             46.000                209                209             0.00%              0.007              2.967              7.669              0.008              0.069              7.361              7.435
                 50              0.618               POOR             13.800             47.000                414                414             0.00%              0.007              3.067              9.943              0.008              0.875              6.486              7.768
                100              0.515               POOR             22.367             67.000                671                671             0.00%              0.007              4.052             10.993              0.008              2.857              8.900              9.168
 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================

 \* Apdex |APDEXT|

Slowest requests
----------------

The 5 slowest average response time during the best cycle with **25** CUs:

* In page 002, Apdex rating: UNACCEPTABLE, avg response time: 6.12s, PROPFIND: ``/sabredav/server.php/addressbooks/schenard%40kronos-web.com/kronos``
  `Querying the addressbook`
* In page 001, Apdex rating: Excellent, avg response time: 0.02s, options: ``/sabredav/server.php/``
  `Get entry point of the DAV server`

Page detail stats
-----------------


PAGE 001: Get entry point of the DAV server
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

* Req: 001, options, url ``/sabredav/server.php/``

     .. image:: request_001.001.png

     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                    CUs             Apdex*             Rating              TOTAL            SUCCESS              ERROR                MIN                AVG                MAX                P10                MED                P90                P95
     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                     25              1.000          Excellent                108                108             0.00%              0.007              0.018              0.135              0.008              0.011              0.040              0.058
                     50              0.995          Excellent                211                211             0.00%              0.007              0.043              1.556              0.008              0.009              0.027              0.037
                    100              0.963          Excellent                351                351             0.00%              0.007              0.267              3.586              0.008              0.012              0.146              2.827
     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================

     \* Apdex |APDEXT|

PAGE 002: Querying the addressbook
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

* Req: 001, PROPFIND, url ``/sabredav/server.php/addressbooks/schenard%40kronos-web.com/kronos``

     .. image:: request_002.001.png

     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                    CUs             Apdex*             Rating              TOTAL            SUCCESS              ERROR                MIN                AVG                MAX                P10                MED                P90                P95
     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                     25              0.248       UNACCEPTABLE                101                101             0.00%              5.115              6.119              7.669              5.177              6.007              7.435              7.469
                     50              0.227       UNACCEPTABLE                203                203             0.00%              5.114              6.209              9.943              5.165              6.072              7.768              8.938
                    100              0.023       UNACCEPTABLE                320                320             0.00%              5.111              8.204             10.993              6.866              8.406              9.170              9.401
     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================

     \* Apdex |APDEXT|

Definitions
-----------

* CUs: Concurrent users or number of concurrent threads executing tests.
* Request: a single GET/POST/redirect/xmlrpc request.
* Page: a request with redirects and resource links (image, css, js) for an html page.
* STPS: Successful tests per second.
* SPPS: Successful pages per second.
* RPS: Requests per second, successful or not.
* maxSPPS: Maximum SPPS during the cycle.
* maxRPS: Maximum RPS during the cycle.
* MIN: Minimum response time for a page or request.
* AVG: Average response time for a page or request.
* MAX: Maximmum response time for a page or request.
* P10: 10th percentile, response time where 10 percent of pages or requests are delivered.
* MED: Median or 50th percentile, response time where half of pages or requests are delivered.
* P90: 90th percentile, response time where 90 percent of pages or requests are delivered.
* P95: 95th percentile, response time where 95 percent of pages or requests are delivered.
* Apdex T: Application Performance Index, 
  this is a numerical measure of user satisfaction, it is based
  on three zones of application responsiveness:

  - Satisfied: The user is fully productive. This represents the
    time value (T seconds) below which users are not impeded by
    application response time.

  - Tolerating: The user notices performance lagging within
    responses greater than T, but continues the process.

  - Frustrated: Performance with a response time greater than 4*T
    seconds is unacceptable, and users may abandon the process.

    By default T is set to 1.5s this means that response time between 0
    and 1.5s the user is fully productive, between 1.5 and 6s the
    responsivness is tolerating and above 6s the user is frustrated.

    The Apdex score converts many measurements into one number on a
    uniform scale of 0-to-1 (0 = no users satisfied, 1 = all users
    satisfied).

    Visit http://www.apdex.org/ for more information.
* Rating: To ease interpretation the Apdex
  score is also represented as a rating:

  - U for UNACCEPTABLE represented in gray for a score between 0 and 0.5 

  - P for POOR represented in red for a score between 0.5 and 0.7

  - F for FAIR represented in yellow for a score between 0.7 and 0.85

  - G for Good represented in green for a score between 0.85 and 0.94

  - E for Excellent represented in blue for a score between 0.94 and 1.

Report generated with FunkLoad_ 1.16.1, more information available on the `FunkLoad site <http://funkload.nuxeo.org/#benching>`_.