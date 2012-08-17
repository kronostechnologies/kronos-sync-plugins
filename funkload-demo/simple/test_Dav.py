# -*- coding: iso-8859-15 -*-
"""Simple FunkLoad test

$Id$
"""
import unittest
from random import random
from funkload.FunkLoadTestCase import FunkLoadTestCase

class Dav(FunkLoadTestCase):
    """This test use a configuration file Simple.conf."""

    def setUp(self):
        """Setting up test."""
        self.server_url = self.conf_get('main', 'url')

    def test_simple(self):
        server_url = self.server_url
        addressbook_url = server_url + 'addressbooks/schenard%40kronos-web.com/kronos'
        self.setBasicAuth('schenard@kronos-web.com', 'asdF1234')
        
        res = self.options(server_url, description='Get entry point of the DAV server')
        dav = res.headers.get('DAV')
        self.assert_(dav is not None)

        self.propfind(addressbook_url, description='Querying the addressbook')

if __name__ in ('main', '__main__'):
    unittest.main()
