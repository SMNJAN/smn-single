/*
 * Created by Kuhva.
 */
const smnswal=Swal.mixin({confirmButtonClass:"btn btn-info",cancelButtonClass:"btn btn-primary",buttonsStyling:!1});$(document).on("click",".language",function(n){n.preventDefault();var e=$(this).data("lang");console.log(e),$.ajax({url:"assets/set_lang.php",method:"POST",data:{lang:e}}).done(function(n){"success"===n?window.location.reload():smnswal({title:"Verarbeitungsfehler",text:"Leider gab es einen Fehler beim Aktualisieren Deines Musicbots",type:"error"})})});