$(document).ready(function(){ 
   $('#buttonAll').on('click',function(){
        $('#accepted').hide();
        $('#pending').hide();
        $('#rejected').hide();
        $('#all').toggle('slow');
   });
   $('#buttonAccepted').on('click',function(){
        $('#all').hide();
        $('#pending').hide();
        $('#rejected').hide();
        $('#accepted').toggle('slow');
   });
   $('#buttonPending').on('click',function(){
        $('#all').hide();
        $('#accepted').hide();
        $('#rejected').hide();
        $('#pending').toggle('slow');
   });
   $('#buttonRejected').on('click',function(){
        $('#all').hide();
        $('#pending').hide();
        $('#accepted').hide();
        $('#rejected').toggle('slow');
   });
});