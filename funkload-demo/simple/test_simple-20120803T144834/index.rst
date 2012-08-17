======================
FunkLoad_ bench report
======================


:date: 2012-08-03 14:48:34
:abstract: Simply testing a default static page
           Bench result of ``Dav.test_simple``: 
           Access a DAV server and query the addressbook

.. _FunkLoad: http://funkload.nuxeo.org/
.. sectnum::    :depth: 2
.. contents:: Table of contents
.. |APDEXT| replace:: \ :sub:`1.5`

Bench configuration
-------------------

* Launched: 2012-08-03 14:48:34
* From: belial
* Test: ``test_Dav.py Dav.test_simple``
* Target server: http://10.0.10.116/sabredav/server.php/
* Cycles of concurrent users: [5, 10, 20]
* Cycle duration: 10s
* Sleeptime between request: from 0.0s to 0.5s
* Sleeptime between test case: 0.01s
* Startup delay between thread: 0.01s
* Apdex: |APDEXT|
* FunkLoad_ version: 1.16.1


Test stats
----------

The number of Successful **Tests** Per Second (STPS) over Concurrent Users (CUs).

Sorry no test have finished during a cycle, the cycle duration is too short.


Page stats
----------

The number of Successful **Pages** Per Second (SPPS) over Concurrent Users (CUs).
Note that an XML RPC call count like a page.

 .. image:: pages_spps.png
 .. image:: pages.png

 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                CUs             Apdex*             Rating               SPPS            maxSPPS              TOTAL            SUCCESS              ERROR                MIN                AVG                MAX                P10                MED                P90                P95
 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                  5              0.500               POOR              0.500              5.000                  5                  5             0.00%              5.010              7.016             10.023              5.010              5.013             10.023             10.023
                 10              0.500               POOR              1.000             10.000                 10                 10             0.00%              5.013              9.531             10.041             10.025             10.034             10.041             10.041
                 20              0.443       UNACCEPTABLE              2.000             20.000                 20                 20             0.00%              5.554              9.210             10.050              6.561             10.034             10.045             10.050
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
                  5              0.500               POOR              0.700              5.000                  7                  7             0.00%              5.010              5.011              5.013              5.010              5.010              5.013              5.013
                 10              0.500               POOR              1.900             10.000                 19                 19             0.00%              5.010              5.016              5.030              5.010              5.013              5.028              5.030
                 20              0.443       UNACCEPTABLE              3.500             20.000                 35                 35             0.00%              5.009              5.263              7.527              5.011              5.014              6.539              7.516
 ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================

 \* Apdex |APDEXT|

Slowest requests
----------------

The 5 slowest average response time during the best cycle with **5** CUs:

* In page 001, Apdex rating: POOR, avg response time: 5.01s, get: ``/sabredav/server.php/``
  `Get entry point of the DAV server`
* In page 001, Apdex rating: POOR, avg response time: 5.01s, link: ``/sabredav/server.php/?sabreAction=asset&assetName=favicon.ico``
  ``

Page detail stats
-----------------


PAGE 001: Get entry point of the DAV server
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

* Req: 001, get, url ``/sabredav/server.php/``

     .. image:: request_001.001.png

     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                    CUs             Apdex*             Rating              TOTAL            SUCCESS              ERROR                MIN                AVG                MAX                P10                MED                P90                P95
     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                      5              0.500               POOR                  5                  5             0.00%              5.010              5.011              5.013              5.010              5.010              5.013              5.013
                     10              0.500               POOR                 10                 10             0.00%              5.010              5.012              5.014              5.010              5.012              5.014              5.014
                     20              0.400       UNACCEPTABLE                 20                 20             0.00%              5.010              5.444              7.527              5.011              5.014              7.516              7.527
     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================

     \* Apdex |APDEXT|
* Req: 002, link, url ``/sabredav/server.php/?sabreAction=asset&assetName=favicon.ico``

     .. image:: request_001.002.png

     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                    CUs             Apdex*             Rating              TOTAL            SUCCESS              ERROR                MIN                AVG                MAX                P10                MED                P90                P95
     ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ================== ==================
                      5              0.500               POOR                  2                  2             0.00%              5.010              5.011              5.011              5.010              5.011              5.011              5.011
                     10              0.500               POOR                  9                  9             0.00%              5.011              5.022              5.030              5.011              5.023              5.030              5.030
                     20              0.500               POOR                 15                 15             0.00%              5.009              5.021              5.036              5.011              5.023              5.031              5.036
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