function CompteSolidaireTemplate(){this.compte='<div id="contenu"><div class="com-widget-window ui-widget ui-widget-content ui-corner-all"><div class="com-widget-header ui-widget ui-widget-header ui-corner-all">Solde : {solde} {sigleMonetaire}</div><div><div id="content-nav-liste-operation" class="ui-helper-clearfix ui-state-default ui-corner-all"><form>	<span id="icone-nav-liste-operation-w" class="prev ui-helper-hidden ui-state-default ui-corner-all com-button" ><span class="ui-icon ui-icon-circle-arrow-w"></span></span>	<span id="page-compteur">Page : <span type="text" class="pagedisplay"></span></span>	<span id="icone-nav-liste-operation-e" class="next ui-state-default ui-corner-all com-button" ><span class="ui-icon ui-icon-circle-arrow-e"></span></span>	<input type="hidden" class="pagesize" value="20"></form></div><table id="table-operation" class="com-table"><thead><tr class="ui-widget ui-widget-header" ><th class="com-table-th">Date</th><th class="com-table-th">Compte</th><th class="com-table-th">Débit</th><th class="com-table-th">Crédit</th><th class="com-table-th"></th><th class="com-table-th"></th></tr></thead><tbody><!-- BEGIN operation --><tr><td class="com-table-td td-date "><span class="ui-helper-hidden id-operation">{operation.opeId}</span>{operation.opeDate}</td><td class="com-table-td cpt-label">{operation.cptLabel}</td><td class="com-table-td td-montant">{operation.debit}</td><td class="com-table-td td-montant">{operation.credit}</td><td class="com-table-td td-edt" id="td-edt-{operation.opeId}"></td><td class="com-table-td td-edt" id="td-sup-{operation.opeId}"></td></tr><!-- END operation --></tbody></table></div></div></div>';this.montantDebit='<span class="montant">{debit}</span> {sigleMonetaire}';this.montantCredit="{credit} {sigleMonetaire}";this.btnEdt='<span class="com-cursor-pointer com-btn-header ui-widget-content ui-corner-all btn-edt-modifier" title="Modifier"><span class="ui-icon ui-icon-pencil"></span>';this.btnSup='<span class="com-cursor-pointer com-btn-header ui-widget-content ui-corner-all btn-edt-supprimer" title="Supprimer"><span class="ui-icon ui-icon-closethick"></span>';this.listeAdherent='<div id="contenu"><div id="liste_adherent_solde_int"><div class="com-widget-window ui-widget ui-widget-content ui-corner-all"><div class="com-widget-header ui-widget ui-widget-header ui-corner-all">Les Adhérents</div><div id="liste-adh-recherche" class="recherche com-widget-header ui-widget ui-widget-header ui-corner-all"><form id="filter-form"><div><span class="conteneur-icon com-float-left ui-widget-content ui-corner-left" title="Chercher"><span class="ui-icon ui-icon-search"></span></span><input class="com-input-text ui-widget-content ui-corner-right" name="filter" id="filter" value="" maxlength="30" size="15" type="text" /></div></form></div><table class="com-table"><thead><tr class="ui-widget ui-widget-header"><th class="com-table-th com-underline-hover liste-adh-th-num com-cursor-pointer"><span class="ui-icon span-icon"></span>N°</th><th class="com-table-th com-underline-hover liste-adh-th-num com-cursor-pointer"><span class="ui-icon span-icon"></span>Compte</th><th class="com-table-th com-underline-hover liste-adh-th-nom com-cursor-pointer"><span class="ui-icon span-icon"></span>Nom</th><th class="com-table-th com-underline-hover liste-adh-th-nom com-cursor-pointer"><span class="ui-icon span-icon"></span>Prénom</th></tr></thead><tbody><!-- BEGIN listeAdherent --><tr class="com-cursor-pointer compte-ligne" ><td class="com-table-td com-underline-hover"><span class="ui-helper-hidden id-adherent">{listeAdherent.adhId}</span>{listeAdherent.adhNumero}</td><td class="com-table-td com-underline-hover">{listeAdherent.cptLabel}</td><td class="com-table-td com-underline-hover">{listeAdherent.adhNom}</td><td class="com-table-td com-underline-hover">{listeAdherent.adhPrenom}</td></tr><!-- END listeAdherent --></tbody></table></div></div></div></div>';this.listeAdherentVide='<div id="contenu"><div class="com-widget-window ui-widget ui-widget-content ui-corner-all"><div class="com-widget-header ui-widget ui-widget-header ui-corner-all">Les Adhérents</div><p id="texte-liste-vide">Aucun adhérent dans la base.</p></div></div>';this.listeVirementVide='<div id="contenu"><div class="com-widget-window ui-widget ui-widget-content ui-corner-all"><div class="com-widget-header ui-widget ui-widget-header ui-corner-all">Solde : {solde} {sigleMonetaire}</div><p id="texte-liste-vide">Aucun Virement effectué.</p></div></div>';this.dialogAjoutVirement='<div id="dialog-ajout-virement" title="Virement Solidaire"><form><table class="com-table-100"><tr>Destinataire : {adhNumero} {adhPrenom} {adhNom}</tr><tr>N° de compte : {cptLabel}</tr><tr class="com-center" ><td class="com-table-form-td montant-virement">Montant <input class="com-numeric com-input-text ui-widget-content ui-corner-all" type="text" name="montant" maxlength="12" id="montant"/> {sigleMonetaire}</td></tr></table></form></div>';this.dialogModifVirement='<div id="dialog-ajout-virement" title="Virement Solidaire"><form><table class="com-table-100"><tr><td>N° de compte : {label}</td></tr><tr class="com-center" ><td class="com-table-form-td montant-virement">Montant <input class="com-numeric com-input-text ui-widget-content ui-corner-all" type="text" name="montant" maxlength="12" id="montant" value="{montant}" /> {sigleMonetaire}</td></tr></table></form></div>';this.dialogSupVirement='<div id="dialog-ajout-virement" title="Supprimer un Virement"><form><table class="com-table-100"><tr><td>N° de compte : {label}</td></tr><tr class="com-center" ><td class="com-table-form-td montant-virement">Montant {montant} {sigleMonetaire}</td></tr></table></form></div>'}function CompteSolidaireVue(a){this.mCommunVue=new CommunVue();this.solde=0;this.modifVirement=[];this.construct=function(b){var c=this;var d={fonction:"compte"};$.post("./index.php?m=CompteSolidaire&v=CompteSolidaire","pParam="+$.toJSON(d),function(e){Infobulle.init();if(e.valid){if(b&&b.vr){Infobulle.generer(b.vr,"")}c.afficher(e)}else{Infobulle.generer(e,"")}},"json")};this.afficher=function(e){var d=this;var c=new CompteSolidaireTemplate();this.solde=e.solde;e.solde=e.solde.nombreFormate(2,","," ");e.sigleMonetaire=gSigleMonetaire;$(e.operation).each(function(){if(this.opeDate!=null){if(this.opeTypePaiement==9&&differenceDateTime(this.opeDate,getDateTimeAujourdhuiDb())>-15000000){d.modifVirement.push(this.opeId)}this.opeDate=this.opeDate.extractDbDate().dateDbToFr();var g={};g.sigleMonetaire=gSigleMonetaire;if(this.opeMontant<0){g.debit=(this.opeMontant*-1).nombreFormate(2,","," ");this.debit=c.montantDebit.template(g);this.credit=""}else{this.debit="";g.credit=this.opeMontant.nombreFormate(2,","," ");this.credit=c.montantCredit.template(g)}}});if(e.operation.length>0&&e.operation[0].opeId!=null){var f=c.compte;var b=$(f.template(e));if(e.operation.length<21){b=this.masquerPagination(b)}else{b=this.paginnation(b)}$("#contenu").replaceWith(d.affect(b))}else{var f=c.listeVirementVide;$("#contenu").replaceWith(f.template(e))}};this.affect=function(b){b=this.ajoutModification(b);b=this.affectModification(b);b=this.affectSuppression(b);b=this.mCommunVue.comHoverBtn(b);return b};this.paginnation=function(b){b.find("#table-operation").tablesorter({headers:{0:{sorter:false},1:{sorter:false},2:{sorter:false},3:{sorter:false},4:{sorter:false},5:{sorter:false}}}).tablesorterPager({container:b.find("#content-nav-liste-operation"),positionFixed:false,size:20});return b};this.masquerPagination=function(b){b.find("#content-nav-liste-operation").hide();return b};this.ajoutModification=function(c){var b=new CompteSolidaireTemplate();$(this.modifVirement).each(function(){c.find("#td-edt-"+this).html(b.btnEdt);c.find("#td-sup-"+this).html(b.btnSup)});return c};this.affectModification=function(b){var c=this;b.find(".btn-edt-modifier").click(function(){var g=$(this).parents("tr").find(".id-operation").text();var e=new CompteSolidaireTemplate();var f=e.dialogModifVirement;var d={};d.label=$(this).parents("tr").find(".cpt-label").text();d.montant=$(this).parents("tr").find(".montant").text();d.sigleMonetaire=gSigleMonetaire;var h=$(c.affectDialog($(f.template(d)))).dialog({autoOpen:true,modal:true,draggable:false,resizable:false,width:450,buttons:{Valider:function(){c.modifierVirement(this,g,d.montant)},Annuler:function(){$(this).dialog("close")}},close:function(i,j){$(this).remove()}});h.find("form").submit(function(){c.modifierVirement(h,g,d.montant);return false})});return b};this.modifierVirement=function(b,d,g){var f=this;var c=new CompteSolidaireModifierVirementVO();c.id=d;c.montantActuel=g.numberFrToDb();c.montant=$(b).find(":input[name=montant]").val().numberFrToDb();c.solde=this.solde;var e=new CompteSolidaireVirementValid();var i=e.validUpdate(c);Infobulle.init();if(i.valid){c.fonction="modifierVirement";var h=this;$.post("./index.php?m=CompteSolidaire&v=CompteSolidaire","pParam="+$.toJSON(c),function(k){Infobulle.init();if(k.valid){var m=new TemplateVR();m.valid=false;m.log.valid=false;var j=new VRerreur();j.code=ERR_308_CODE;j.message=ERR_308_MSG;m.log.erreurs.push(j);var l={vr:m};f.construct(l);$(b).dialog("close")}else{Infobulle.generer(k,"")}},"json")}else{Infobulle.generer(i,"")}};this.affectDialog=function(b){b=this.mCommunVue.comNumeric(b);return b};this.affectSuppression=function(b){var c=this;b.find(".btn-edt-supprimer").click(function(){var g=$(this).parents("tr").find(".id-operation").text();var e=new CompteSolidaireTemplate();var f=e.dialogSupVirement;var d={};d.label=$(this).parents("tr").find(".cpt-label").text();d.montant=$(this).parents("tr").find(".montant").text();d.sigleMonetaire=gSigleMonetaire;var h=$(f.template(d)).dialog({autoOpen:true,modal:true,draggable:false,resizable:false,width:450,buttons:{Valider:function(){c.supprimerVirement(this,g)},Annuler:function(){$(this).dialog("close")}},close:function(i,j){$(this).remove()}});h.find("form").submit(function(){c.supprimerVirement(h,g);return false})});return b};this.supprimerVirement=function(b,d){var f=this;var c=new CompteSolidaireSupprimerVirementVO();c.id=d;var e=new CompteSolidaireVirementValid();var h=e.validDelete(c);Infobulle.init();if(h.valid){c.fonction="supprimerVirement";var g=this;$.post("./index.php?m=CompteSolidaire&v=CompteSolidaire","pParam="+$.toJSON(c),function(j){Infobulle.init();if(j.valid){var l=new TemplateVR();l.valid=false;l.log.valid=false;var i=new VRerreur();i.code=ERR_309_CODE;i.message=ERR_309_MSG;l.log.erreurs.push(i);var k={vr:l};f.construct(k);$(b).dialog("close")}else{Infobulle.generer(j,"")}},"json")}else{Infobulle.generer(h,"")}};this.construct(a)}function CompteSolidaireListeAdherentVue(a){this.mCommunVue=new CommunVue();this.listeAdherent=[];this.solde=0;this.construct=function(b){var c=this;var d={fonction:"adherent"};$.post("./index.php?m=CompteSolidaire&v=ListeAdherent","pParam="+$.toJSON(d),function(e){Infobulle.init();if(e.valid){if(b&&b.vr){Infobulle.generer(b.vr,"")}$(e.listeAdherent).each(function(){c.listeAdherent[this.adhId]=this});c.solde=e.solde;c.afficher(e)}else{Infobulle.generer(e,"")}},"json")};this.afficher=function(d){var c=this;var b=new CompteSolidaireTemplate();if(d.listeAdherent.length>0&&d.listeAdherent[0].adhId!=null){var e=b.listeAdherent;$("#contenu").replaceWith(c.affect($(e.template(d))))}else{$("#contenu").replaceWith(b.listeAdherentVide)}};this.affect=function(b){b=this.affectTri(b);b=this.affectRecherche(b);b=this.affectVirement(b);return b};this.affectTri=function(b){b.find(".com-table").tablesorter({sortList:[[0,0]],headers:{4:{sorter:false}}});return b};this.affectRecherche=function(b){b.find("#filter").keyup(function(){$.uiTableFilter($(".com-table"),this.value)});b.find("#filter-form").submit(function(){return false});return b};this.affectVirement=function(b){var c=this;b.find(".compte-ligne").click(function(){var g=$(this).find(".id-adherent").text();var e=new CompteSolidaireTemplate();var f=e.dialogAjoutVirement;var d=c.listeAdherent[g];d.sigleMonetaire=gSigleMonetaire;var h=$(c.affectDialog($(f.template(d)))).dialog({autoOpen:true,modal:true,draggable:false,resizable:false,width:450,buttons:{Valider:function(){c.envoyerVirement(this,g)},Annuler:function(){$(this).dialog("close")}},close:function(i,j){$(this).remove()}});h.find("form").submit(function(){c.envoyerVirement(h,g);return false})});return b};this.affectDialog=function(b){b=this.mCommunVue.comNumeric(b);return b};this.envoyerVirement=function(b,d){var c=new CompteSolidaireAjoutVirementVO();c.id=d;c.montant=$(b).find(":input[name=montant]").val().numberFrToDb();c.solde=this.solde;var e=new CompteSolidaireVirementValid();var g=e.validAjout(c);Infobulle.init();if(g.valid){c.fonction="ajoutVirement";var f=this;$.post("./index.php?m=CompteSolidaire&v=ListeAdherent","pParam="+$.toJSON(c),function(i){Infobulle.init();if(i.valid){var j=new TemplateVR();j.valid=false;j.log.valid=false;var h=new VRerreur();h.code=ERR_307_CODE;h.message=ERR_307_MSG;j.log.erreurs.push(h);Infobulle.generer(j,"");$(b).dialog("close")}else{Infobulle.generer(i,"")}},"json")}else{Infobulle.generer(g,"")}};this.construct(a)};