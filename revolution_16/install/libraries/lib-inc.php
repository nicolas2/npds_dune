<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2021 by Philippe Brunier                     */
/* IZ-Xinstall version : 1.2                                            */
/*                                                                      */
/* Auteurs : v.0.1.0 EBH (plan.net@free.fr)                             */
/*         : v.1.1.1 jpb, phr                                           */
/*         : v.1.1.2 jpb, phr, dev, boris                               */
/*         : v.1.1.3 dev - 2013                                         */
/*         : v.1.2 phr, jpb - 2016                                      */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (version_compare(PHP_VERSION, '5.3.0') >= 0 and extension_loaded('mysqli')) {
   $file = file("config.php");
   $file[33] ="\$mysql_p = 1;\n";
   $file[34] ="\$mysql_i = 1;\n";
   $fic = fopen("config.php", "w");
   foreach($file as $n => $ligne) {
      fwrite($fic, $ligne);
   }
   fclose($fic);
   include_once('lib/mysqli.php');
} else
   include_once('lib/mysql.php');

settype($langue,'string');
if($langue) {
   $lang_symb = substr($langue, 0, 3);
   if(file_exists($fichier_lang = 'install/languages/'.$langue.'/install-'.$lang_symb.'.php')) {
      include_once $fichier_lang;
   }
   else
      include_once('install/languages/french/install-fre.php');
}

#autodoc FixQuotes($what) : Quote une chaîne contenant des '
function FixQuotes($what = '') {
   $what = str_replace("&#39;","'",$what);
   $what = str_replace("'","''",$what);
   while (preg_match("#\\\\'#", $what)) {
      $what = str_replace("\\\\'","'",$what);
   }
   return $what;
}

function verif_php() {
   global $stopphp, $phpver;
   $stopphp = 0;
   if(phpversion() < "5.3.0") { 
      $phpver = phpversion();
      $stopphp = 1;
   }
   else
   $phpver = phpversion();
   return ($phpver);
}

function verif_chmod() {
   global $stopngo, $listfich;
   $file_to_check = array('abla.log.php','cache.config.php','config.php','filemanager.conf','slogs/security.log','meta/meta.php','static/edito.txt','modules/upload/upload.conf.php');
   $i=0; $listfich='';
   foreach ($file_to_check as $v) {
      if(file_exists($v)) {
         if(is_writeable($v))
            $listfich .= '<li class="list-group-item">'.ins_translate("Droits d'accès du fichier ").'<code class="code">'.$v.'</code> :<span class="ml-1 text-success">'.ins_translate("corrects").' !</span></li>';
         else {
            $listfich .=  '<li class="list-group-item list-group-item-danger">'.ins_translate("Droits d'accès du fichier ").'<code class="code">'.$v.'</code> :<span class="ml-1">'.ins_translate("incorrects").' !</span><br />
            <span class="">'.ins_translate("Vous devez modifier les droits d'accès (lecture/écriture) du fichier ") .$v. ' (chmod 666)</li>';
            $stopngo = 1;
         }
      }
      else {
         $listfich .=  '
         <li class="list-group-item list-group-item-danger">'.ins_translate("Le fichier").' '.$v.' '.ins_translate("est introuvable !").'</li>';
         $stopngo = 1;
      }
      $i++;
   }
   return $listfich;
}

function write_parameters($new_dbhost, $new_dbuname, $new_dbpass, $new_dbname, $new_NPDS_Prefix, $new_mysql_p, $new_system, $new_system_md5, $new_adminmail) {
   global $stage4_ok;
   $stage4_ok = 0;

   $file = file("config.php");
   $file[29] ="\$dbhost = \"$new_dbhost\";\n";
   $file[30] ="\$dbuname = \"$new_dbuname\";\n";
   $file[31] ="\$dbpass = \"$new_dbpass\";\n";
   $file[32] ="\$dbname = \"$new_dbname\";\n";
   $file[33] ="\$mysql_p = \"$new_mysql_p\";\n";
   $file[36] ="\$system = $new_system;\n";
   $file[37] ="\$system_md5 = $new_system_md5;\n";
   $file[214]="\$adminmail = \"$new_adminmail\";\n";
   $file[319]="\$NPDS_Prefix = \"$new_NPDS_Prefix\";\n";
   $NPDS_Key=uniqid("");
   $file[320]="\$NPDS_Key = \"$NPDS_Key\";\n";

   $fic = fopen("config.php", "w");
   foreach($file as $n => $ligne) {
      fwrite($fic, $ligne);
   }
   fclose($fic);

   $stage4_ok = 1;
   return($stage4_ok);
}

function write_others($new_nuke_url, $new_sitename, $new_Titlesitename, $new_slogan, $new_Default_Theme, $new_startdate) {
   global $stage5_ok;
   $stage5_ok = 0;

   // Par défaut $parse=1 dans le config.php
   $new_sitename =  htmlentities(stripslashes($new_sitename));
   $new_Titlesitename = htmlentities(stripslashes($new_Titlesitename));
   $new_slogan = htmlentities(stripslashes($new_slogan));
   $new_startdate = stripslashes($new_startdate);
   $new_nuke_url = FixQuotes($new_nuke_url);

   $file = file("config.php");
   $file[90] ="\$sitename = \"$new_sitename\";\n";
   $file[91] ="\$Titlesitename = \"$new_Titlesitename\";\n";
   $file[92] ="\$nuke_url = \"$new_nuke_url\";\n";
   $file[94] ="\$slogan = \"$new_slogan\";\n";
   $file[95] ="\$startdate = \"$new_startdate\";\n";
   $file[101] ="\$Default_Theme = \"$new_Default_Theme\";\n";

   $fic = fopen("config.php", "w");
   foreach($file as $n => $ligne) {
      fwrite($fic, $ligne);
   }
   fclose($fic);

   $stage5_ok = 1;
   return($stage5_ok);
}

function msg_erreur($message) {
   echo '<html>
   <body bgcolor="white"><br />
      <div style="text-align: center; font-weight: bold">
         <div style="font-face: arial; font-size: 22px; color: #ff0000">'.ins_translate($message).'</div>
      </div>
      </body>
</html>';
   die();
}

function write_users($adminlogin, $adminpass1, $adminpass2, $NPDS_Prefix) {
   include_once('config.php');
   global $system, $system_md5, $minpass, $stage7_ok, $NPDS_Prefix;
   if ($adminlogin != '') {
      if($adminpass1 != $adminpass2)
         $stage7_ok = 2;
      else {
         if(strlen($adminpass1) < $minpass)
            $stage7_ok = 2;
         else {
            $stage7_ok = 1;
            if ($system_md5 == 1) {
               $AlgoCrypt = PASSWORD_BCRYPT;
               $min_ms = 250;
               $options = ['cost' => getOptimalBcryptCostParameter($adminpass1, $AlgoCrypt, $min_ms)];
               $hashpass = password_hash($adminpass1, $AlgoCrypt, $options);
               $adminpwd=crypt($adminpass1, $hashpass);
               $hashkey = 1;
            } else
               $hashkey = 0;

            sql_connect();
            $result1 = sql_query("UPDATE ".$NPDS_Prefix."authors SET aid='$adminlogin', pwd='$adminpwd', hashkey='$hashkey' WHERE radminsuper='1'");
            copy("modules/f-manager/users/modele.admin.conf.php","modules/f-manager/users/".strtolower($adminlogin).".conf.php");

            if(!$result1)
               $stage7_ok = 0;
         }
      }
   }
   else
      $stage7_ok = 2;
   return($stage7_ok);
}


function write_upload($new_max_size, $new_DOCUMENTROOT, $new_autorise_upload_p, $new_racine, $new_rep_upload, $new_rep_cache, $new_rep_log, $new_url_upload)
{
   global $langue, $nuke_url, $stage8_ok;
   $stage8_ok = 0;

   $file = file("modules/upload/upload.conf.php");
   $file[16] = "\$max_size = $new_max_size;\n";
   $file[21] = "\$DOCUMENTROOT = \"$new_DOCUMENTROOT\";\n";
   $file[24] = "\$autorise_upload_p = \"$new_autorise_upload_p\";\n";
   $file[28] = "\$racine = \"$new_racine\";\n";
   $file[31] = "\$rep_upload = \$racine.\"$new_rep_upload\";\n";
   $file[34] = "\$rep_cache = \$racine.\"$new_rep_cache\";\n";
   $file[37] = "\$rep_log = \$racine.\"$new_rep_log\";\n";
   $file[40] = "\$url_upload = \"$new_url_upload\";\n";

   $fic = fopen("modules/upload/upload.conf.php", "w");
   foreach($file as $n => $ligne) {
      fwrite($fic, $ligne);
   }
   fclose($fic);
   $stage8_ok = 1;
   return($stage8_ok);
}

#autodoc language_iso($l,$s,$c) : renvoi le code language iso 639-1 et code pays ISO 3166-2  $l=> 0 ou 1(requis), $s, $c=> 0 ou 1 (requis)
function language_iso($l,$s,$c) {
    global $langue;
    $iso_lang='';$iso_country='';$ietf='';
    switch ($langue) {
        case "french": $iso_lang ='fr';$iso_country='FR'; break;
        case "english":$iso_lang ='en';$iso_country='US'; break;
        case "spanish":$iso_lang ='es';$iso_country='ES'; break;
        case "german":$iso_lang ='de';$iso_country='DE'; break;
        case "chinese":$iso_lang ='zh';$iso_country='CN'; break;
        default:
        break;
    }
    if ($c!==1) $ietf= $iso_lang;
    if (($l==1) and ($c==1)) $ietf=$iso_lang.$s.$iso_country;
    if (($l!==1) and ($c==1)) $ietf=$iso_country;
    if (($l!==1) and ($c!==1)) $ietf='';
    if (($l==1) and ($c!==1)) $ietf=$iso_lang;
    return ($ietf);
}

function formval($fv,$fv_parametres,$arg1,$foo) {
   if ($fv=='fv') {
      if($fv_parametres!='') $fv_parametres = explode('!###!',$fv_parametres);
      echo '
   <script type="text/javascript" src="lib/js/es6-shim.min.js"></script>
   <script type="text/javascript" src="lib/formvalidation/dist/js/FormValidation.full.min.js"></script>
   <script type="text/javascript" src="lib/formvalidation/dist/js/locales/'.language_iso(1,"_",1).'.min.js"></script>
   <script type="text/javascript" src="lib/formvalidation/dist/js/plugins/Bootstrap.min.js"></script>
   <script type="text/javascript" src="lib/formvalidation/dist/js/plugins/L10n.min.js"></script>
   <script type="text/javascript" src="lib/js/checkfieldinp.js"></script>
   <script type="text/javascript">
   //<![CDATA[
   '.$arg1.'
   var diff;
   document.addEventListener("DOMContentLoaded", function(e) {
      const strongPassword = function() {
        return {
            validate: function(input) {
               var score=0;
               const value = input.value;
               if (value === "") {
                  return {
                     valid: true,
                     score:null,
                  };
               }
               if (value === value.toLowerCase()) {
                  return {
                     valid: false,
                     message: "Le mot de passe doit contenir au moins un caractère en majuscule.",
                     score: score-1,
                  };
               }
               if (value === value.toUpperCase()) {
                  return {
                     valid: false,
                     message: "Le mot de passe doit contenir au moins un caractère en minuscule.",
                     score: score-1,
                  };
               }
               if (value.search(/[0-9]/) < 0) {
                  return {
                     valid: false,
                     message: "Le mot de passe doit contenir au moins un chiffre.",
                     score: score-1,
                  };
               }
               if (value.search(/[!#$%&^~*_]/) < 0) {
                  return {
                     valid: false,
                     message: "Le mot de passe doit contenir au moins un caractère non numérique et non alphabétique.",
                     score: score-1,
                  };
               }
               if (value.length < 8) {
                  return {
                     valid: false,
                     message: "Le mot de passe doit contenir plus de 8 caractères.",
                     score: score-1,
                  };
               }

               score += ((value.length >= 8) ? 1 : -1);
               if (/[A-Z]/.test(value)) score += 1;
               if (/[a-z]/.test(value)) score += 1; 
               if (/[0-9]/.test(value)) score += 1;
               if (/[!#$%&^~*_]/.test(value)) score += 1; 
               return {
                  valid: true,
                  score: score,
               };
            },
         };
      };

    // Register new validator named checkPassword
    FormValidation.validators.checkPassword = strongPassword;
   
   formulid.forEach(function(item, index, array) {
      const fvitem = FormValidation.formValidation(
         document.getElementById(item),{
            locale: "'.language_iso(1,"_",1).'",
            localization: FormValidation.locales.'.language_iso(1,"_",1).',
         fields: {
         ';
   if($fv_parametres!='')
      echo '
            '.$fv_parametres[0];
   echo '
         },
         plugins: {
            declarative: new FormValidation.plugins.Declarative({
               html5Input: true,
            }),
            trigger: new FormValidation.plugins.Trigger(),
            submitButton: new FormValidation.plugins.SubmitButton(),
            defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
            bootstrap: new FormValidation.plugins.Bootstrap(),
            icon: new FormValidation.plugins.Icon({
               valid: "fa fa-check",
               invalid: "fa fa-times",
               validating: "fas fa-sync",
               onPlaced: function(e) {
                  e.iconElement.addEventListener("click", function() {
                     fvitem.resetField(e.field);
                  });
               },
            }),
         },

      })

      .on("core.validator.validated", function(e) {
      // The password passes the callback validator
      // voir si on a plus de champs mot de passe : changer par un array de champs ...
      if ((e.field === "adminpass1") && e.validator === "checkPassword") {
         // Get the score
         var score = e.result.score,$pass_level=$("#pass-level"),
             $bar = $("#passwordMeter_cont");

         switch (true) {
           case (score === null):
               $bar.html("").css("width", "0%").removeClass().addClass("progress-bar");
               $bar.attr("value","0");
               break;
           case (score <= 1):
               $bar.html("").css("width", "25%").removeClass().addClass("progress-bar bg-danger");
               $bar.attr("aria-valuenow","25");
               $bar.attr("value","25");
               $pass_level.html("Tr&#xE8;s faible").removeClass().addClass("help-block text-right text-danger");
               break;
           case (score > 0 && score <= 2):
               $bar.html("").css("width", "50%").removeClass().addClass("progress-bar bg-warning");
               $bar.attr("aria-valuenow","50");
               $bar.attr("value","50");
               $pass_level.html("Faible").removeClass().addClass("help-block text-right text-warning");
               break;
           case (score > 2 && score <= 4):
               $bar.html("").css("width", "75%").removeClass().addClass("progress-bar bg-info");
               $bar.attr("aria-valuenow","75");
               $bar.attr("value","75");
               $pass_level.html("Moyen").removeClass().addClass("help-block text-right text-info");
               break;
           case (score > 4):
               $bar.html("").css("width", "100%").removeClass().addClass("progress-bar bg-success");
               $bar_cont.attr("aria-valuenow","100");
               $bar_cont.attr("value","100").removeClass().addClass("progress-bar bg-success");
               $pass_level.html("Fort").removeClass().addClass("help-block text-right text-success");
               break;
           default:
               break;
         }
         }
      });
      ;';
      if($fv_parametres!='')
         if(array_key_exists(1, $fv_parametres))
            echo '
               '.$fv_parametres[1];
   echo '
   })
   });
   //]]>
   </script>';
   }
   switch($foo) {
      case '' :
         echo '
      </div>';
         include ('footer.php');
      break;
      case 'foo' :
         include ('footer.php');
      break;
   }
}

#autodoc getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms=250) : permet de calculer le cout algorythmique optimum pour la procédure de hashage
function getOptimalBcryptCostParameter($pass, $AlgoCrypt, $min_ms=250) {
   for ($i = 4; $i < 31; $i++) {
      $calculCost = [ 'cost' => $i ];
      $time_start = microtime(true);
      password_hash($pass, $AlgoCrypt, $calculCost);
      $time_end = microtime(true);
      if (($time_end - $time_start) * 1000 > $min_ms)
         return $i;
   }
}

?>