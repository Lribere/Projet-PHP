<!DOCTYPE html>
<html>
   <head>
      <meta charset = "utf-8">
      <title>DiagCity</title>
      <link rel="stylesheet" href="style.css" media="screen" type="text/css" />

   </head>
   <body>
   <div align="center">
   <h2>Voici le résultat de votre recherche</h2>
         <br/>
         <p> Pour votre information, certaines constantes sont données afin de les comparer : </p>
         <br/>
         <table>
            <tr>
               <th scope="row">La superficie moyenne pour une commune en France</th>
               <td> 20,930 </td>
            </tr>
            <tr>
               <th scope="row">La population moyenne pour une commune en France</th>
               <td> 2703 </td>
            </tr>
            <tr>
               <th scope="row">L'Humidité moyenne pour une commune en France </th>
               <td> 73,910 </td>
            </tr>
            <tr>
               <th scope="row">Le niveau moyenn d'élévation pour une commune en France</th>
               <td> 258,56683 </td>
            </tr>
            <tr>
               <th scope="row">La taille moyenne d'une page wikipédia pour une commune en France </th>
               <td> 2591,0638 </td>
            </tr>
            <tr>
               <th scope="row">Le nombre moyen de liens wikipédia pour une commune en France </th>
               <td> 21,2284 </td>
            </tr>
         </table>
         <br/>
      <?php
         session_start();
         require_once 'html_tag_helpers.php';
         require 'vendor/autoload.php';
         require  'sparqllib.php';
   
         require_once( "sparqllib.php" );

         $getid = intval($_GET['id']);
         $id = 1;
         $codePostal = intval($_GET['CP']);
         $db = sparql_connect( "https://dbpedia.org/sparql" );
         $SPARQL_ENDPOINT = 'https://query.wikidata.org/sparql';
         $population = null;

         // Prefixes pour DBPedia

         if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
         sparql_ns( "rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#" );
         sparql_ns( "dbp","http://dbpedia.org/property/" );
         sparql_ns( "dbo","http://dbpedia.org/ontology/" );
         sparql_ns( "xsd","http://www.w3.org/2001/XMLSchema#" );
         sparql_ns("dbr","http://dbpedia.org/resource/");
         sparql_ns("db-owl","http://dbpedia.org/ontology/");

         // Prefixes pour Wikidata
         \EasyRdf\RdfNamespace::set('wd', 'http://www.wikidata.org/entity/');
         \EasyRdf\RdfNamespace::set('wds', 'http://www.wikidata.org/entity/statement/');
         \EasyRdf\RdfNamespace::set('wdt', 'http://www.wikidata.org/prop/direct/');
         \EasyRdf\RdfNamespace::set('p', 'http://www.wikidata.org/prop/');
         \EasyRdf\RdfNamespace::set('wikibase', 'http://wikiba.se/ontology#');
          
         // Requête wikidata 

         $SPARQL_QUERY = "
         SELECT ?commune ?communeLabel ?codePostal ?population
         WHERE {
           ?commune wdt:P31 wd:Q484170 .
           ?commune wdt:P1082 ?population .
           ?commune wdt:P281 ?codePostal .
           
           FILTER(?codePostal = \"$codePostal\")
                   
                  SERVICE wikibase:label
                   {
                     bd:serviceParam wikibase:language \"fr\" .
                   }
         }
         ";
         // Requête DBPedia

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


         //Résultat DBPedia
         $result = sparql_query( $sparql ); 
         if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

         //Résultat WikiData
         $sparqlWiki = new \EasyRdf\Sparql\Client($SPARQL_ENDPOINT);
         $resultsWiki = $sparqlWiki->query($SPARQL_QUERY);
         
         // Tableau Résultat Field DBPedia
         $fields = sparql_field_array( $result );

         //Tableau GENERAL
         $Tableau = [
            [1,2,3,4,5,6,7,8],
         ];

         
         print "<table align=\"center\">";
         print "<tr>";
         $SizeFields = count($fields); // Nombre de résultats trouvés côté DBPedia
             
         for($i = 0; $i < $SizeFields;$i++)
         {
            $Tableau[1][$i] = $fields[$i];
            print "<th>".$Tableau[1][$i]."</th>";
         }
         $Tableau[1][7] = "Population";
         print "<th>".$Tableau[1][7]."</th>";
         print "</tr>";

         foreach ($resultsWiki as $row) {
            if (preg_match("|/(Q\d+)|", $row->commune, $matches)) {
               if(link_to_self($row->population, "id=".$matches[1])){
                  $population = link_to_self($row->population, "id=".$matches[1]);
               }else{
                  $population = "N/A";
               }
            }   
         }

         while($row = sparql_fetch_array( $result ))
         {
            print "<tr>";
            for($i = 0; $i < $SizeFields ;$i++)
            {
               if(isset($row[$fields[$i]]) && $fields[$i] == $Tableau[1][$i])
               {
                  $Tableau[2][$i] = $row[$Tableau[1][$i]];
                  print "<td>".$Tableau[2][$i]."</td>";
               }else{
                  $Tableau[2][$i] = 0;
                  print "<td>"."N/A"."</td>";
               }

            }
            if($population)
               $Tableau[2][7] = $population;
            else
               $Tableau[2][7] = "N/A";
            print "<td>".$Tableau[2][7]."</td>";
            print "</tr>";
         }
         
            
         print "</table>";
      ?>
      <br/>
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
      </div>
   </body>
</html>