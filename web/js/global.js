$(document).ready(function(){

    $(document).on("click", "#btnToggleSidebar", function(){
        $(".app-layout").toggleClass("sidebar-collapsed");
    });

    $(document).on("keyup","#filtro",function(){
        let data = $(this).val();
        let url = $(this).data("url");

        $.ajax({
            url: url,
            type: "GET",
            data: {
                buscar: data
            },
            success: function(data){
                if(data.trim() !== ""){
                    $("tbody").html(data);
                } else {
                    $("tbody").html("<tr><td colspan='5'>Elemento no encontrado</td></tr>");
                }
            }
        })
    });

});
document.getElementById('buscadorUsuarios').addEventListener('keyup', function () {
      var filtro = this.value.toLowerCase();
      document.querySelectorAll('#tablaUsuarios tbody tr').forEach(function (fila) {
          fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
      });
  });

