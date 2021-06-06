<?php
session_start();
$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre', 'root', '');
 
if(isset($_GET['id']) AND $_GET['id'] > 0) {
   $getid = intval($_GET['id']);
   $requser = $bdd->prepare('SELECT * FROM membres WHERE idMembre = ?');
   $requser->execute(array($getid));
   $userinfo = $requser->fetch();

   
   
?>
<html>
   <head>
        <title>DiagCity</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
        
   </head>
   <body>
      <div align="center">
         <h2>Utilisateur :  <?php echo $userinfo['pseudo']; ?></h2>
         <br />
         <br />

         <form method="POST" action="">
            <table>
               <tr>
                  <td align="right">
                     <label for="Code Postal"> Code Postal :</label>
                  </td>
                  <td>
                     <input type="text" placeholder="Code Postal" id="CP" name="CP" value="<?php if(isset($CP)) { echo $CP; } ?>" />
                  </td>
                  <td>
                     <input type="submit" name="requête" value=" Diagnostiquer" />
                  </td>
               </tr>
            <table>
         </form>
         <?php
            if(isset($_POST['requête'])) {
               $CodeVille = htmlspecialchars($_POST["CP"]);
               header("Location: Resultat.php?CP=".$CodeVille."&id=".$getid);
            }
  

            if(isset($_SESSION['id']) AND $userinfo['idMembre'] == $_SESSION['id']) {
         ?>
         <br />
            <a href="Deconnexion.php">Se déconnecter</a>
         <?php
         }
         ?>
      </div>
   </body>
</html>
<?php   
}
?>