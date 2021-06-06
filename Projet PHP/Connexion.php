<?php
session_start();
 
$bdd = new PDO('mysql:host=127.0.0.1;dbname=espace_membre', 'root', '');
 
if(isset($_POST['formconnexion'])) {
   $mailconnect = htmlspecialchars($_POST['mailconnect']);
   $mdpconnect = sha1($_POST['mdpconnect']);
   if(!empty($mailconnect) AND !empty($mdpconnect)) {
      $requser = $bdd->prepare("SELECT * FROM membres WHERE mail = ? AND motdepasse = ?");
      $requser->execute(array($mailconnect, $mdpconnect));
      $userexist = $requser->rowCount();
      if($userexist == 1) {
         $userinfo = $requser->fetch();
         $_SESSION['id'] = $userinfo['idMembre'];
         $_SESSION['pseudo'] = $userinfo['pseudo'];
         $_SESSION['mail'] = $userinfo['mail'];
         header("Location: Recherche.php?id=".$_SESSION['id']);
      } else {
         $erreur = "Mauvais mail ou mot de passe !";
      }
   } else {
      $erreur = "Tous les champs doivent être complétés !";
   }
}
?>
<html>
   <head>
      <title>DiagCity</title>
      <meta charset="utf-8">
      <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
   </head>
   <body>
      <div align="center">
         <h2>Connexion</h2>
         <br /><br />
         <form method="POST" action="">
             <table>
                 <tr>
                     <td align="center">
                        <input type="email" name="mailconnect" placeholder="Mail" />
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="password" name="mdpconnect" placeholder="Mot de passe" />
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        <input type="submit" name="formconnexion" value="Se connecter !" />
                    </td>
                </tr>
            </table>
         </form>
         <?php
         if(isset($erreur)) {
            echo '<font color="red">'.$erreur."</font>";
         }
         ?>
         <a type="text" href="Inscription.php">Je n'ai pas encore de compte !</a>
      </div>
   </body>
</html>