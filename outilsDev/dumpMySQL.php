<?php
function dumpMySQL($serveur, $login, $password, $base, $mode)
{
    $connexion = mysqli_connect($serveur, $login, $password, $base);
    mysqli_set_charset($connexion, "utf8");
    
    $entete = "-- ----------------------\n";
    $entete .= "-- dump de la base ".$base." au ".date("d-M-Y")."\n";
    $entete .= "-- ----------------------\n\n\n";
    $creations = "";
    $insertions = "\n\n";
    
    $listeTables = mysqli_query($connexion, "show tables");
    while($table = mysqli_fetch_array($listeTables))
    {
        // si l'utilisateur a demandé la structure ou la totale
        if($mode == 1 || $mode == 3)
        {
            $creations .= "-- -----------------------------\n";
            $creations .= "-- creation de la table ".$table[0]."\n";
            $creations .= "-- -----------------------------\n";
            $listeCreationsTables = mysqli_query($connexion, "show create table ".$table[0]);
            while($creationTable = mysqli_fetch_array($listeCreationsTables))
            {
              $creations .= $creationTable[1].";\n\n";
            }
        }
        // si l'utilisateur a demandé les données ou la totale
        if($mode > 1)
        {
            $donnees = mysqli_query($connexion, "SELECT * FROM ".$table[0]);
            $insertions .= "-- -----------------------------\n";
            $insertions .= "-- insertions dans la table ".$table[0]."\n";
            $insertions .= "-- -----------------------------\n";
            while($nuplet = mysqli_fetch_array($donnees))
            {
                $insertions .= "INSERT INTO ".$table[0]." VALUES(";
                for($i=0; $i < mysqli_num_fields($donnees); $i++)
                {
                  if($i != 0)
                     $insertions .=  ", ";
                  $insertions .=  "'";
                  $insertions .= addslashes($nuplet[$i]);
                  $insertions .=  "'";
                }
                $insertions .=  ");\n";
            }
            $insertions .= "\n";
        }
    }
 
    mysqli_close($connexion);
 
    $fichierDump = fopen("dump.sql", "w");
    fwrite($fichierDump, $entete);
    fwrite($fichierDump, $creations);
    fwrite($fichierDump, $insertions);
    fclose($fichierDump);
    echo "Sauvegarde réalisée avec succès !!";
}
dumpMySQL("127.0.0.1", "zeybu", "zeybu", "zeybu", 3);
?>
