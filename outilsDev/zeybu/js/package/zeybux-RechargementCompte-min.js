function RechargementCompteTemplate(){this.listeAdherent='<div id="contenu"><div id="liste_adherent_solde_int"><div class="com-widget-window ui-widget ui-widget-content ui-corner-all"><div class="com-widget-header ui-widget ui-widget-header ui-corner-all">Les Adhérents</div><div id="liste-adh-recherche" class="recherche com-widget-header ui-widget ui-widget-header ui-corner-all"><form id="filter-form"><div><span class="conteneur-icon com-float-left ui-widget-content ui-corner-left" title="Chercher"><span class="ui-icon ui-icon-search"></span></span><input class="com-input-text ui-widget-content ui-corner-right" name="filter" id="filter" value="" maxlength="30" size="15" type="text" /></div></form></div><table class="com-table"><thead><tr class="ui-widget ui-widget-header"><th class="com-table-th com-underline-hover liste-adh-th-num com-cursor-pointer"><span class="ui-icon span-icon"></span>N°</th><th class="com-table-th com-underline-hover liste-adh-th-nom com-cursor-pointer"><span class="ui-icon span-icon"></span>Nom</th><th class="com-table-th com-underline-hover liste-adh-th-nom com-cursor-pointer"><span class="ui-icon span-icon"></span>Prénom</th><th class="com-table-th com-underline-hover com-cursor-pointer"><span class="ui-icon span-icon"></span>Courriel</th><th class="com-table-th liste-adh-th-solde">Solde</th></tr></thead><tbody><!-- BEGIN listeAdherent --><tr class="com-cursor-pointer compte-ligne" ><td class="com-table-td com-underline-hover"><span class="ui-helper-hidden">{listeAdherent.adhIdTri}</span><span class="ui-helper-hidden id-adherent">{listeAdherent.adhId}</span>{listeAdherent.adhNumero}</td><td class="com-table-td com-underline-hover">{listeAdherent.adhNom}</td><td class="com-table-td com-underline-hover">{listeAdherent.adhPrenom}</td><td class="com-table-td com-underline-hover">{listeAdherent.adhCourrielPrincipal}</td><td class="com-table-td com-underline-hover liste-adh-td-solde"><span class="{listeAdherent.classSolde}">{listeAdherent.cptSolde} {sigleMonetaire}</span></td></tr><!-- END listeAdherent --></tbody></table></div></div></div></div>';this.listeAdherentVide='<div id="contenu"><div class="com-widget-window ui-widget ui-widget-content ui-corner-all"><div class="com-widget-header ui-widget ui-widget-header ui-corner-all">Les Adhérents</div><p id="texte-liste-vide">Aucun adhérent dans la base.</p></div></div>';this.dialogRecharger='<div title="Rechargement du compte {compte}"><div><div id="resa-info-commande">{numero} :  {prenom} {nom}<br/>N° de Compte : {compte}</div><div><span>Solde Actuel : </span><span>{solde} {sigleMonetaire}</span><br/><span>Nouveau Solde : </span><span id="nouveau-solde">{solde}</span> <span id="nouveau-solde-sigle">{sigleMonetaire}</span></div></div><div class="com-widget-content"><table><thead><tr><th>Montant</th><th>Type de Paiement</th><th id="label-champ-complementaire"></th></tr></thead><tbody><tr><td><input type="text" name="montant-rechargement" value="" class="com-numeric com-input-text ui-widget-content ui-corner-all" id="montant" maxlength="12" size="3"/> <span>{sigleMonetaire}</span></td><td class="com-center"><select name="typepaiement" id="typePaiement"><option value="0">== Choisir ==</option><!-- BEGIN typePaiement --><option value="{typePaiement.tppId}">{typePaiement.tppType}</option><!-- END typePaiement --></select></td><td id="td-champ-complementaire"><input type="text" name="champ-complementaire" value="" class="com-input-text ui-widget-content ui-corner-all" id="champComplementaire" maxlength="50" size="15"/></td></tr></tbody></table></div></div>'}function RechargerCompteVue(a){this.mCommunVue=new CommunVue();this.mTypePaiement=[];this.solde=0;this.construct=function(b){$.history({vue:function(){RechargerCompteVue(b)}});var c=this;var d={fonction:"listeAdherent"};$.post("./index.php?m=RechargementCompte&v=RechargerCompte","pParam="+$.toJSON(d),function(e){Infobulle.init();if(e){if(e.valid){if(b&&b.vr){Infobulle.generer(b.vr,"")}$(e.typePaiement).each(function(){c.mTypePaiement[this.tppId]=this});c.afficher(e)}else{Infobulle.generer(e,"")}}},"json")};this.afficher=function(d){var c=this;var b=new RechargementCompteTemplate();if(d.listeAdherent.length>0&&d.listeAdherent[0].adhId!=null){var e=b.listeAdherent;d.sigleMonetaire=gSigleMonetaire;$(d.listeAdherent).each(function(){this.classSolde="";if(this.cptSolde<0){this.classSolde="com-nombre-negatif"}this.cptSolde=this.cptSolde.nombreFormate(2,","," ");this.adhIdTri=this.adhNumero.replace("Z","")});$("#contenu").replaceWith(c.affect($(e.template(d))))}else{$("#contenu").replaceWith(b.listeAdherentVide)}};this.affect=function(b){b=this.affectTri(b);b=this.affectRecherche(b);b=this.affectLienCompte(b);return b};this.affectTri=function(b){b.find(".com-table").tablesorter({sortList:[[0,0]],headers:{4:{sorter:false}}});return b};this.affectRecherche=function(b){b.find("#filter").keyup(function(){$.uiTableFilter($(".com-table"),this.value)});b.find("#filter-form").submit(function(){return false});return b};this.affectLienCompte=function(b){var c=this;b.find(".compte-ligne").click(function(){var d={"id-adherent":$(this).find(".id-adherent").text(),fonction:"infoRechargement"};$.post("./index.php?m=RechargementCompte&v=RechargerCompte","pParam="+$.toJSON(d),function(g){Infobulle.init();if(g){if(g.valid){c.solde=parseFloat(g.solde);g.sigleMonetaire=gSigleMonetaire;g.solde=g.solde.nombreFormate(2,","," ");g.typePaiement=c.mTypePaiement;var h=g.idCompte;var f=new RechargementCompteTemplate();var i=f.dialogRecharger;var e=$(i.template(g));e=c.affectDialog(e);e.dialog({autoOpen:true,modal:true,draggable:false,resizable:false,width:800,buttons:{Valider:function(){var j=c.getRechargementVO();j.id=h;var k=new RechargementCompteValid();var m=k.validAjout(j);Infobulle.init();if(m.valid){j.fonction="rechargerCompte";var l=this;$.post("./index.php?m=RechargementCompte&v=RechargerCompte","pParam="+$.toJSON(j),function(o){Infobulle.init();if(o.valid){var q=new TemplateVR();q.valid=false;q.log.valid=false;var n=new VRerreur();n.code=ERR_306_CODE;n.message=ERR_306_MSG;q.log.erreurs.push(n);var p={vr:q};c.construct(p);$(l).dialog("close")}else{Infobulle.generer(o,"")}},"json")}else{Infobulle.generer(m,"")}},Annuler:function(){$(this).dialog("close")}},close:function(j,k){$(this).remove()}});c.changerTypePaiement($(":input[name=typepaiement]"));c.majNouveauSolde()}else{Infobulle.generer(g,"")}}},"json")});return b};this.affectDialog=function(b){b=this.affectSelectTypePaiement(b);b=this.affectNouveauSolde(b);b=this.mCommunVue.comNumeric(b);return b};this.affectSelectTypePaiement=function(b){var c=this;b.find(":input[name=typepaiement]").change(function(){c.changerTypePaiement($(this))});return b};this.changerTypePaiement=function(d){var c=d.val();var b=this.getLabelChamComplementaire(c);if(b!=null){$("#label-champ-complementaire").text(b).show();$("#td-champ-complementaire").show()}else{$("#label-champ-complementaire").text("").hide();$(":input[name=champ-complementaire]").val("");$("#td-champ-complementaire").hide()}};this.getLabelChamComplementaire=function(c){var b=this.mTypePaiement;if(b[c]){if(b[c].tppChampComplementaire==1){return b[c].tppLabelChampComplementaire}}return null};this.affectNouveauSolde=function(b){var c=this;b.find(":input[name=montant-rechargement]").keyup(function(){c.majNouveauSolde()});return b};this.majNouveauSolde=function(){var b=this.calculNouveauSolde();if(b<=0){$("#nouveau-solde").addClass("com-nombre-negatif");$("#nouveau-solde-sigle").addClass("com-nombre-negatif")}else{$("#nouveau-solde").removeClass("com-nombre-negatif");$("#nouveau-solde-sigle").removeClass("com-nombre-negatif")}$("#nouveau-solde").text(b.nombreFormate(2,","," "))};this.calculNouveauSolde=function(){var b=parseFloat($(":input[name=montant-rechargement]").val().numberFrToDb());if(isNaN(b)){b=0}return this.solde+b};this.getRechargementVO=function(){var b=new RechargementCompteVO();var c=$(":input[name=montant-rechargement]").val().numberFrToDb();if(!isNaN(c)&&!c.isEmpty()){c=parseFloat(c)}b.montant=c;b.typePaiement=$(":input[name=typepaiement]").val();if(this.getLabelChamComplementaire(b.typePaiement)!=null){b.champComplementaireObligatoire=1;b.champComplementaire=$(":input[name=champ-complementaire]").val()}else{b.champComplementaireObligatoire=0}return b};this.construct(a)};