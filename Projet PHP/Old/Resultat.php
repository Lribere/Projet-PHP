<!DOCTYPE html>
<html>
   <head>
      <meta charset = "utf-8">
      <title>DiagCity</title>
      <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
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
         session_start();
         require_once( "sparqllib.php" );
         $getid = intval($_GET['id']);
         $id = 1;
         $codePostal = intval($_GET['CP']);
         $db = sparql_connect( "https://dbpedia.org/sparql" );
         if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
         sparql_ns( "rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
         sparql_ns( "dbp","http://dbpedia.org/property/" );
         sparql_ns( "dbo","http://dbpedia.org/ontology/" );
         sparql_ns( "xsd","http://www.w3.org/2001/XMLSchema#" );
         sparql_ns("dbr","http://dbpedia.org/resource/");
         sparql_ns("db-owl","http://dbpedia.org/ontology/");
          
         $sparql = "SELECT  ?Nom, ?codePostal_INSEE, ?Superficie,?Humidite, ?NiveauDElevation, ?TaillePageWikipedia,  COUNT(?wiki) as ?NombreDeLiensWIKI
         WHERE {
         ?s rdf:type dbo:Place .
         ?s dbp:name ?Nom .
         ?s dbo:postalCode ?codePostal_INSEE .
         ?s dbo:country ?country .
         ?s dbo:wikiPageWikiLink ?wiki .
         OPTIONAL{?s dbp:place ?isPlaceOf}
         OPTIONAL{ ?s dbp:areaKm ?Superficie }
         OPTIONAL{ ?s dbo:elevation ?NiveauDElevation}
         OPTIONAL{?s dbo:wikiPageLength ?TaillePageWikipedia }
         OPTIONAL{?s dbp:aprHumidity ?Humidite}
         
          FILTER(?codePostal_INSEE = \"$codePostal\")
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
               if(isset($row[$field]))
               {
                  print "<td>$row[$field]</td>";
               }

            }
            print "</tr>";
         }
         print "</table>";
      ?>

      <form method="POST" action="">
         <table>
            <tr>
               <td>
                  <input type="submit" name="New" value=" Effectuer une nouvelle requête" />
               </td>
            </tr>
         <table>
      </form>
      <?php
         if(isset($_POST['New'])) {
            header("Location: Recherche.php?id=".$_SESSION['id']);
         }
      ?>
   </body>
</html>