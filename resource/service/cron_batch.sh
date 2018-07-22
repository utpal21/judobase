#!/bin/bash
baseurl="http://59.106.213.162/judobase/"
scrapmatch="${baseurl}matchinfor/competition"
scrapplayer="${baseurl}playerinfor/scrap_player"

curl --request GET "${scrapmatch}" > /dev/null 2>&1
curl --request GET "${scrapplayer}" > /dev/null 2>&1