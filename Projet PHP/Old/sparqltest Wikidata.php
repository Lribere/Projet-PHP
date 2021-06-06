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
          
         $db = sparql_connect( "https://query.wikidata.org/bigdata/namespace/wdq/sparql?query={SPARQL}" );
         if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

          
         $sparql = "
         PREFIX wd: <http://www.wikidata.org/entity/>
         PREFIX wdt: <http://www.wikidata.org/prop/direct/>

         SELECT ?commune ?communeLabel ?codePostal ?population
         WHERE {
           ?commune wdt:P31 wd:Q484170 .
           ?commune wdt:P1082 ?population .
           ?commune wdt:P281 ?codePostal .

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