// $("#new_edit_user").on('submit', function(){
//     if($("#user_password").val() != $("#verifpass").val()) {
//         $("#verifpass").css("border", "1px solid red");
//         $("#verifpass").css("background-color", "rgba(255, 0, 0, 0.1)");
//         $("#verifpass").css("color", "red");
//         $("#verifpass").val("");
//         $("#user_password").val("");
//         alert("Les deux mots de passe saisies sont différents");
//         alert("Merci de renouveler l'opération");
//         return false;
//     }
// })

// $('#confirm-delete').on('show.bs.modal', function(e) {
//     $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
//     $('.debug-url').html('Delete URL: <strong>' + $(this).find('.btn-ok').attr('href') + '</strong>');
// });

//   $('#confirm-delete').on('show.bs.modal', function (event) {
//             var button = $(event.relatedTarget);
//             var action = button.data('action');
//             var modal = $(this);
//             modal.find('form').attr('action', action);
//             modal.find('input[name="token"]').val(button.data('token'));
//         });

var modal = document.getElementById('confirm-delete');
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
