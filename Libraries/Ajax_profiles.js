$(document).keyup(function(e){
    if(e.which == 27) { // || e.which == 13){
        $('#addProfile').hide();

        endAllModifyProfile();
        
        hideConfirmMessage();
    }
});

$(document).ready(function(){
    // Masque la modale quand on clique un objet de class "close"
     $(".close").on('click', function() {
        $('#addProfile').hide();
    });

    // Change couleur si vide ou plein des champs obligatoires
    $('input.obligatoire').focusout(function(){
        if ($(this).val() != '') {
            $(this).css("border", "1px solid #cdcdcd");
        } else {
            $(this).css("border", "2px solid #00608d");
        }
    });

    $('#iButtonCreateProfile').on('click', function(){
        addProfile();
    });

    $('#iButtonDeleteProfile').on('click', function(){
        deleteProfile();
    });
});


// Gestion des créations de Profil à la volée.
function putAddProfile(){
    $('#addProfile').show('slow');

    $('#iProfileLabel').keyup(function(e){
        if (e.which == 13) {
            if ($('#iProfileLabel').val() != '') addProfile();
        }
    });

    $('#iProfileLabel').focus();
}


function addProfile(){
    if ($('#iProfileLabel').val() != '') {
        $.ajax({
            url: 'SM-users.php?action=PRF_AX',
            type: 'POST',
            data: $.param({'Label': $('#iProfileLabel').val()}),
            dataType: 'json',
            success: function(reponse) {
                $('#addProfile').hide();

                var Label = $('#iProfileLabel').val();

                $('#iProfileLabel').val('');

                var statut = reponse['Status'];

                if (statut == 'success') {
                    var Id = reponse['idProfile'];
                    var Label = reponse['Label'];
                    var Script = reponse['Script'];
                    var URL_PICTURES = reponse['URL_PICTURES'];
                    var L_Modify = reponse['L_Modify'];
                    var L_Delete = reponse['L_Delete'];
                    var L_Cancel = reponse['L_Cancel'];
                    var L_Delete_Profile_Confirmation = reponse['L_Delete_Profile_Confirmation'];
                    var L_Warning = reponse['L_Warning'];
                    var L_Groups_Associate = reponse['L_Groups_Associate'];

                    if ($('#dashboard').length == 0) {
                        $('#listeSecrets').prepend(
                         '<tr class="pair td-aere">'+
                         '<td class="align-middle align-center"><input type="checkbox" name="'+Id+'" id="P_'+Id+'" /></td>'+
                         '<td class="td-aere align-middle"><label for="P_'+Id+'">'+Label+'</label></td>'+
                         '<td class="align-center"><a class="simple" href="'+Script+'?action=PRF_G&prf_id='+Id+'">'+
                         '<img src="'+URL_PICTURES+'/b_usrscr_2.png" class="no-border" alt="'+L_Groups_Associate+'" title="'+L_Groups_Associate+'" /></a>'+
                         '</td>'+
                         '</tr>'
                        );
                    } else {
                        $('#listeSecrets').prepend(
                         '<tr id="profil_'+Id+'" class="surline">'+
                         '<td id="label_'+Id+'" class="align-middle">'+Label+'</td>'+
                         '<td>'+
                         '<a class="simple" href="javascript:modifyProfile(\'' + Id + "');\">"+
                         '<img class="no-border" src="'+URL_PICTURES+'/b_edit.png" alt="'+L_Modify+'" title="'+L_Modify+
                         '" /></a>\n'+
                         '<a class="simple" href="javascript:confirmDeleteProfile(\''+Id+'\',\''+
                         L_Delete_Profile_Confirmation+'\',\''+L_Warning+'\',\''+L_Cancel+'\',\''+L_Delete+'\');">'+
                         '<img class="no-border" src="'+URL_PICTURES+'/b_drop.png" alt="'+L_Delete+'" title="'+L_Delete+
                         '" /></a>\n'+
                         '<a class="simple" href="'+Script+'?action=PRF_G&prf_id='+Id+'">'+
                         '<img class="no-border" src="'+URL_PICTURES+'/b_usrscr_2.png" alt="'+L_Groups_Associate+'" title="'+L_Groups_Associate+'" /></a>'+
                         '</td>'+
                         '</tr>'
                        );
                        
                        var Total = $('#total').text();
                        Total = Number(Total) + 1;
                        $('#total').text( Total );
                    }

                    showInfoMessage( reponse['Status'], reponse['Message'] ); // SecretManager.js
                } else if (statut == 'error') {
                    showInfoMessage( reponse['Status'], reponse['Message'] ); // SecretManager.js
                }
            },
            error: function(reponse) {
                alert('Erreur sur serveur : ' + reponse['responseText']);
            }
        });
    }
}


// Gestion des suppressions de Profil à la volée.
function deleteProfile( Id ){
    $.ajax({
        url: 'SM-users.php?action=PRF_DX',
        type: 'POST',
        data: $.param({'prf_id': Id}),
        dataType: 'json',
        success: function(reponse) {
            var statut = reponse['Status'];

            showInfoMessage( reponse['Status'], reponse['Message'] ); // SecretManager.js

            if (statut == 'success') {
                $('#profil_'+Id).remove();
                hideConfirmMessage();

                var Total = $('#total').text();
                Total = Number(Total) - 1;
                $('#total').text( Total );
            }
        },
        error: function(reponse) {
            alert('Erreur sur serveur : ' + reponse['responseText']);
        }
    });
}


// Gestion des modifications "en place".
function modifyProfile( Id ) {
    endAllModifyProfile();
    
    var CancelButton, ModifyButton;
    
     $.ajax({
        async: false,
        url: 'SM-users.php?action=L_MODIF_PROFILE_X',
        type: 'POST',
        dataType: 'json',
        success: function(reponse) {
            CancelButton = reponse['Cancel'];
            ModifyButton = reponse['Modify'];
        },
        error: function(reponse) {
            alert('Erreur sur serveur : ' + reponse['responseText']);
        }
    });
   

    $('#profil_'+Id).hide();
    
    var Label = $('#label_'+Id).text();

	$( "       <tr id=\"MODI_" + Id + "\" class=\"pair\">\n" +
		"        <td class=\"align-middle blue-border-line\"><input id=\"iLabel\" value=\"" +
		Label + "\" class=\"input-xxlarge\" /></td>\n" +
		"        <td class=\"align-middle blue-border-line\"><a class=\"button\" href=\"javascript:endModifyProfile('" +
		Id + "');\">" + CancelButton + "</a>&nbsp;<a class=\"button\" href=\"javascript:saveModifyProfile('" +
		Id + "');\">" + ModifyButton + "</a></td>\n" +
		"       </tr>\n" ).insertAfter( '#profil_'+Id );
    
        document.getElementById('iLabel').focus();
        document.getElementById('iLabel').selectionStart = Label.length;

        $('#iLabel').keyup(function(e){
            if (e.which == 13) {
                if ($('#iLabel').val() != '') saveModifyProfile( Id );
            }
        });
}


function saveModifyProfile( Id ) {
    var Label = $('#iLabel').val();

    $.ajax({
        url: 'SM-users.php?action=PRF_MX',
        type: 'POST',
        data: $.param({'prf_id': Id, 'Label': Label}),
        dataType: 'json',
        success: function(reponse) {
            var statut = reponse['Status'];

            showInfoMessage( reponse['Status'], reponse['Message'] ); // SecretManager.js

            if (statut == 'success') {
                $('#label_'+Id).text(Label);
                
                $('#MODI_'+Id).remove();
                $('#profil_'+Id).show();
            }
        },
        error: function(reponse) {
            alert('Erreur sur serveur : ' + reponse['responseText']);
        }
    });
}


// Annule la modification en cours.
function endModifyProfile(Id) {
    $('#MODI_'+Id).remove();
    $('#profil_'+Id).show();
}


// Annule toutes les modifications en cours.
function endAllModifyProfile() {
    $('tr[id^="MODI_"]').each( function(index) {
        var T_ID = $(this).attr( 'id' );
        T_ID = T_ID.split('_');
        endModifyProfile(T_ID[1]);
    } );
}


function hideConfirmMessage() {
    $('#confirm_message').remove();
}


function confirmDeleteProfile( Id ) {
    var Message, Warning, Cancel, Confirm;

    $.ajax({
        async: false,
        url: 'SM-users.php?action=L_DELETE_PROFILE_X',
        type: 'POST',
        dataType: 'json',
        success: function(reponse) {
            Message = reponse['Message'];
            Warning = reponse['Warning'];
            Cancel = reponse['Cancel'];
            Confirm = reponse['Confirm'];
        },
        error: function(reponse) {
            alert('Erreur sur serveur : ' + reponse['responseText']);
        }
    });

    var Label = $('#label_'+Id).text();
    
    $('<div id="confirm_message" class="modal" role="dialog" tabindex="-1">' +
     '<div class="modal-header">' +
     '<button class="close" aria-hidden="true" data-dismiss="modal" type="button" onClick="javascript:hideConfirmMessage();">×</button>' +
     '<h3 id="myModalLabel">'+Warning+'</h3>' +
     '</div>' +
     '<div class="modal-body">' +
     '<div class="row-fluid"style="width:82%; margin-top:8px;">' +
     '<p>' + Message + '<b>' + Label + '</b></p>' +
     '</div>' +
     '</div>' +
     '<div class="modal-footer">' +
     '<a class="button" id="iCancel" href="javascript:hideConfirmMessage();">'+Cancel+
     '</a>&nbsp;<a class="button" href="javascript:deleteProfile('+Id+');">'+
     Confirm+'</a>' +
     '</div>' +
     '</div>\n' ).prependTo( 'body' );
    
    document.getElementById('iCancel').focus();
}
