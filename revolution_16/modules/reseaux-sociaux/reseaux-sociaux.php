<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* NPDS Copyright (c) 2002-2015 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*                                                                      */
/* Module core reseaux-sociaux                                          */
/* reseaux-sociaux     file 2015 by jpb                                 */
/*                                                                      */
/* version 1.0 17/02/2016                                               */
/************************************************************************/

if (strstr($ModPath,"..") || strstr($ModStart,"..") || stristr($ModPath, "script") || stristr($ModPath, "cookie") || stristr($ModPath, "iframe") || stristr($ModPath, "applet") || stristr($ModPath, "object") || stristr($ModPath, "meta") || stristr($ModStart, "script") || stristr($ModStart, "cookie") || stristr($ModStart, "iframe") || stristr($ModStart, "applet") || stristr($ModStart, "object") || stristr($ModStart, "meta")) {
   die();
}
if (!function_exists("Mysql_Connexion")) {
   include ("mainfile.php");
}
if (!$user) header('location:index.php');

global $language;
$ModStart='reseaux-sociaux';
include ("modules/$ModPath/lang/rs-$language.php");

function ListReseaux($ModPath, $ModStart) {
   if (file_exists("modules/$ModPath/reseaux-sociaux.conf.php"))
      include ("modules/$ModPath/reseaux-sociaux.conf.php");
   include("header.php");

   echo '
   <h3>'.rs_translate("Réseaux sociaux").'</h3>
   <hr />
   <h3><a href="modules.php?&amp;ModPath='.$ModPath.'&amp;ModStart='.$ModStart.'&amp;op=EditReseaux"><i class="fa fa-edit fa-lg"></i></a>&nbsp;'.rs_translate("Editer").'</h3>
'.rs_translate("Liste des réseaux sociaux mis à disposition par l'administrateur.").'

   <table id ="lst_res_soc" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons">
      <thead>
         <tr>
            <th data-sortable="true" data-halign="center" data-align="right">'.rs_translate("Name").'</th>
            <th data-halign="center" data-align="center">'.rs_translate("Icône").'</th>
            <th data-halign="center" data-align="right">'.rs_translate("Fonctions").'</th>
         </tr>
      </thead>
      <tbody>';
      
   foreach ($rs as $v1) {
        echo '
         <tr>
            <td>'.$v1[0].'</td>
            <td><i class="fa fa-'.$v1[2].' fa-2x text-primary"></i></td>
            <td>
               <a href="modules.php?&amp;ModPath='.$ModPath.'&amp;ModStart='.$ModStart.'&amp;op=EditReseaux&amp;rs_id='.urlencode($v1[0]).'" ><i class="fa fa-edit fa-lg" title="'.rs_translate("Editer").'" data-toggle="tooltip"></i></a>
            </td>
        </tr>';
   }
   echo '
      </tbody>
   </table>';
   adminfoot('','','','');
}

function EditReseaux($ModPath, $ModStart, $rs_id, $rs_uid) {
   if (file_exists("modules/$ModPath/reseaux-sociaux.conf.php"))
      include ("modules/$ModPath/reseaux-sociaux.conf.php");
   include("header.php");

   global $cookie;
      $posterdata_extend = get_userdata_extend_from_id($cookie[0]);
         if ($posterdata_extend['M2']!='') {
            $i=0;
            $socialnetworks= explode(';',$posterdata_extend['M2']);
            foreach ($socialnetworks as $socialnetwork) {
               $res_id[] = explode('|',$socialnetwork);
            }
         }
/*
   echo'
   <div class="row">
   <div class="col-sm-6">
   <pre> From db
   ';
   print_r($res_id);
   echo'</pre></div>
   <div class="col-sm-6">
   <pre> From file conf
   ';
   print_r($rs);
   echo'</pre></div></div>';
*/


   echo '<h3>'.rs_translate("Réseaux sociaux").'</h3>';
   echo'
   <hr />
   <form id="reseaux_user" action="modules.php?ModStart='.$ModStart.'&amp;ModPath='.$ModPath.'&amp;op=SaveSetReseaux" method="post">';
   $i=0;
   foreach ($rs as $v1) {
      $ident='';
      if ($res_id[$i]) {
         $k = array_search($v1[0], $res_id[$i]);
         if (false !== $k) {
         $ident=$res_id[$i][1];
         } 
         else{$ident='';}
      }
      if($i==0) echo'
   <div class="row">';
   echo '
   <div class="col-sm-6">
   <fieldset>
   <legend><i class="fa fa-'.$v1[2].' fa-2x text-primary">&nbsp;</i>'.$v1[0].'</legend>
      <div class="form-group row">
         <label class="form-control-label col-sm-12" for="rs_uid'.$i.'">'.rs_translate("Identifiant").'</label>
         <div class="col-sm-12">
            <input id="rs_uid'.$i.'" class="form-control" type="text" name="rs['.$i.'][uid]"  maxlength="50"  placeholder="'.rs_translate("Identifiant").' '.$v1[0].'" value="'.$ident.'"/>
            <span class="help-block text-xs-right"><span id="countcar_rs_uid'.$i.'"></span></span>
            <input type="hidden" name="rs['.$i.'][id]" value="'.$v1[0].'" />
         </div>
      </div>
   </fieldset>
   </div>';
   if ($nombre%2 == 1) echo '
   </div>
   <div class="row">';
   $i++;
   }
echo'
   </div>
      <div class="form-group row">
         <div class="col-sm-offset-6 col-sm-6">
            <button class="btn btn-primary col-xs-12" type="submit"><i class="fa fa-check fa-lg"></i>&nbsp;'.rs_translate("Sauvegarder").'</button>
            <input type="hidden" name="ModPath" value="'.$ModPath.'" />
            <input type="hidden" name="ModStart" value="'.$ModStart.'" />
            <input type="hidden" name="op" value="SaveSetReseaux" />
         </div>
      </div>
   </form>';
   adminfoot('','','','');
}

function SaveSetReseaux($ModPath, $ModStart) {
/*
   if (file_exists("modules/$ModPath/reseaux-sociaux.conf.php"))
      include ("modules/$ModPath/reseaux-sociaux.conf.php");
*/
   global $cookie;
   $li_rs='';
   foreach ($_POST['rs'] as $v1){
   if($v1['uid']!=='')
   $li_rs.=$v1['id'].'|'.$v1['uid'].';';
   }
   $li_rs=rtrim($li_rs,';');
   $li_rs=removeHack(stripslashes(FixQuotes($li_rs)));
   sql_query("UPDATE ".$NPDS_Prefix."users_extend SET M2='$li_rs' WHERE uid='$cookie[0]'");
   Header("Location: modules.php?&ModPath=$ModPath&ModStart=$ModStart");

}

   switch ($op) {
       case "SaveSetReseaux":
       SaveSetReseaux($ModPath, $ModStart);break;
       case "EditReseaux":
       EditReseaux($ModPath, $ModStart, $rs_id, $rs_url, $rs_ico, $subop);break;
       default:
       ListReseaux($ModPath, $ModStart);
    break;
   }
?>