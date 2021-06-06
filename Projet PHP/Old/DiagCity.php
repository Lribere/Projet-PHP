<!DOCTYPE html>
<html>
   <head>
      <meta charset = "utf-8">
      <title>SPARQL Test</title>
      <style type = "text/css">
         p { margin: 0; }
         table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
         }
      </style>
   </head>
   <body>
      <?php
         require_once( "sparqllib.php" );
          $codePostal = 87000;
         $db = sparql_connect( "https://dbpedia.org/sparql" );
         if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
         sparql_ns( "rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
         sparql_ns( "dbp","http://dbpedia.org/property/" );
         sparql_ns( "dbo","http://dbpedia.org/ontology/" );
         sparql_ns( "xsd","http://www.w3.org/2001/XMLSchema#" );
         sparql_ns("dbr","http://dbpedia.org/resource/");
         sparql_ns("db-owl","http://dbpedia.org/ontology/");
          
         $sparql = "SELECT * WHERE {
                  ?s rdf:type dbo:Place .
                  ?s dbo:country ?country .
                  ?s db-owl:postalCode ?codePostal .
                  ?s dbp:areaKm ?area .
                  FILTER(?codePostal = \"$codePostal\")
                  FILTER(?country = dbr:France)
                  }";

         $result = sparql_query( $sparql ); 
         if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
          
         $fields = sparql_field_array( $result );
          
         print "<p>Number of rows: ".sparql_num_rows( $result )." results.</p>";
         print "<table>";
         print "<tr>";
         foreach( $fields as $field )
         {
            print "<th>$field</th>";
         }
         print "</tr>";
         while( $row = sparql_fetch_array( $result ) )
         {
            print "<tr>";
            foreach( $fields as $field )
            {
               print "<td>$row[$field]</td>";
            }
            print "</tr>";
         }
         print "</table>";
      ?>
   </body>
</html>