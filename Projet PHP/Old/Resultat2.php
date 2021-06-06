<?php
    /**
     * Consuming Linked Data from Wikidata.
     *
     * This example demonstrates fetching information about villages in Fife
     * from Wikidata. The list of villages is fetched by running a SPARQL query.
     *
     * If you click on an village, then it fetched by getting the Turtle formatted
     * RDF from Wikidata for that village. It then parses the result and
     * displays a page about that village with a title, synopsis and Open Street Map.
     *
     * @package    EasyRdf
     * @copyright  Copyright (c) 2009-2020 Nicholas J Humfrey
     * @license    http://unlicense.org/
     */

    require_once 'html_tag_helpers.php';
    require 'vendor/autoload.php';
    require  'sparqllib.php';

    // Setup some additional prefixes for Wikidata
    \EasyRdf\RdfNamespace::set('wd', 'http://www.wikidata.org/entity/');
    \EasyRdf\RdfNamespace::set('wds', 'http://www.wikidata.org/entity/statement/');
    \EasyRdf\RdfNamespace::set('wdt', 'http://www.wikidata.org/prop/direct/');
    \EasyRdf\RdfNamespace::set('p', 'http://www.wikidata.org/prop/');
    \EasyRdf\RdfNamespace::set('wikibase', 'http://wikiba.se/ontology#');

    // SPARQL Query to get a list of villages in Fife
    $SPARQL_QUERY = '
    SELECT ?commune ?communeLabel ?codePostal ?population
    WHERE {
      ?commune wdt:P31 wd:Q484170 .
      ?commune wdt:P1082 ?population .
      ?commune wdt:P281 ?codePostal .
      
      FILTER(?codePostal = "97220")
              
             SERVICE wikibase:label
              {
                bd:serviceParam wikibase:language "fr" .
              }
    }
    ';
    $SPARQL_ENDPOINT = 'https://query.wikidata.org/sparql';

    $WIKIDATA_IMAGE = 'wdt:P18';
    $WIKIDATA_POINT = 'wdt:P625';
?>
<html>
<head><title>EasyRdf Village Info Example</title></head>
<body>
<h1>EasyRdf Village Info Example</h1>

<?php
    
        print "<p>List of villages in Fife.</p>";
        $sparql = new \EasyRdf\Sparql\Client($SPARQL_ENDPOINT);
        $results = $sparql->query($SPARQL_QUERY);
       
      print "<table>";

      print "<tr>";
      print "<th scope=\"col\">Nom</th>";
      print "<th scope=\"col\">Population</th>";
      print "<th scope=\"col\">Za'taak</th>";
      print "</tr>";
       
        print "<tr>";
        foreach ($results as $row) {
          if (preg_match("|/(Q\d+)|", $row->commune, $matches)) {
            $nom = link_to_self($row->communeLabel, "id=".$matches[1]);
            print '<td>'.$nom."</td>";
            print '<td>'.link_to_self($row->population, "id=".$matches[1])."</td>";
            
          }     
        }
        print "<tr>\n";

        print "</table>";
      



?>
</body>
</html>
