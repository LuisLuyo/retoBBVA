var accion = 0;
var clientSelected = 0;
var nombresSelected = '';
var fechanacSelected = '';
var depaSelected = '';
var provSelected = '';
var distSelected = '';
var direcSelected = '';

function Principal(){
    $.post('public/Login.html',function(datos){
            $("#card").html(datos);
    });
}

function Reg_Cliente(valor){
    accion = valor;
    if(accion == 0){
        if($('#inputUser').val() != "admin" && $('#inputPassword').val() == "123"){
            swal("Error!!", "Usuario Incorrecto, utilize el usuario genérico!", "error");
            return false;
        }
    }
    else if(accion == 1){
        if(clientSelected == "" | clientSelected == "0" | clientSelected == undefined | clientSelected == null){
            swal("Advertencia!!", "Para modificar seleccione un Cliente...", "warning");
            return false;
        }
    }
    $("#card").html('');
    $.post('public/Reg_Cliente.html',function(datos){  
        $("#card").html(datos);
            //Mascara Calendario
            $('#datepicker_fechanac').datepicker({
                autoclose:true,
                language: 'es'
            });
            //Mascara SOLO LETRAS
            $('#nombres').mask('Z',{translation: {'Z': {pattern: /[a-zA-Z ]/, recursive: true}}});
            //Mascara Formato de fecha
            $('#fechanac').mask("00/00/0000", {placeholder: "__/__/____"});


            //Solicitud Listado de Departamentos RESTful
            $.ajax({
                url: "src/path/Ubigeo.php/api/listarDepartamento",
                ContentType:"application/json; charset=utf-8",
                success: function(response){           
                    myObj = JSON.parse(response);
                    for(x in myObj){
                        var cadena = '';
                        cadena = "<option value='"+myObj[x].CODDEPA+"'>"+myObj[x].DEPARTAMENTO+"</option>";
                        $('#coddepa').append(cadena);
                    }
                    if(accion == 1){
                        $("#coddepa > option").each(function () {
                            if ($(this).html() == depaSelected) {
                                $(this).attr("selected", "selected");
                                return;
                            }
                        });
                        Carga_Provincia($("#coddepa option:selected").html());
                    }
                },
                error: function(err){
                    swal("Error!", err, "error");
                }
            });

            $("#coddepa").unbind("change").change(function(){
                $("#codprov, #coddist").html("");
                var coddepa = $("#coddepa option:selected").val();
                if(coddepa != "00"){
                    $('#codprov, #coddist').append("<option value='00'>Seleccione...</option>");
                    Carga_Provincia($("#coddepa option:selected").html());
                }
            });

        $("#codprov").unbind("change").change(function(){
            $("#coddist").html("");
            var codprov = $("#codprov option:selected").val();
            if(codprov != "00"){
                $('#coddist').append("<option value='00'>Seleccione...</option>");
                Carga_Distrito($("#codprov option:selected").html());
            }    
        });
        
        if(accion == 1){
            $('#codcliente').val(clientSelected);
            $('#nombres').val(nombresSelected);
            $('#fechanac').val(fechanacSelected);
            $('#direccion').val(direcSelected);
        }
    });

}

function InsertarCliente(codcliente){
    var sendCodcliente = $('#codcliente').val();
    var sendNombres = $('#nombres').val();
    var sendFechanac = $('#fechanac').val();
    var sendDireccion = $('#direccion').val(); 
    var sendcoddepa = $("#coddepa option:selected").val();
    var sendcodprov = $("#codprov option:selected").val();
    var sendcoddist = $("#coddist option:selected").val();
    var sendUbigeo = sendcoddepa + sendcodprov + sendcoddist;
    if(sendNombres == "" | sendFechanac == "" | sendcoddepa == "00" | sendcodprov == "00" | sendcoddist == "00" | sendDireccion == ""){
        swal("Advertencia!!", "Completar datos requeridos", "warning");
        return false;
    }
    if(sendCodcliente == ''){
        sendType = "POST";
        sendURL = "src/path/Cliente.php/api/Cliente/Insertar";
    }else{
        sendType = "PUT";
        sendURL = "src/path/Cliente.php/api/Cliente/Modificar/"+sendCodcliente;
    }
    $.ajax({
    	type: sendType,
        url: sendURL,
        data:{nombres:sendNombres,fechanac:sendFechanac, ubigeo:sendUbigeo, direccion:sendDireccion},
        ContentType:"application/json; charset=utf-8",
        success: function(response){           
           swal("Correcto!!", response, "success");
        },
        error: function(err){
            swal("Error!", err, "error");
        }
    });
}

function Carga_Provincia(departamento){
    $.ajax({
        ContentType:"application/json; charset=utf-8",
        url: "src/path/Ubigeo.php/api/listarProvincia/"+departamento,
        success: function(response){
            myObjProvincia = JSON.parse(response);
            for(x in myObjProvincia){
                var cadena = '';
                cadena = "<option value='"+myObjProvincia[x].CODPROV+"'>"+myObjProvincia[x].PROVINCIA+"</option>";
                $('#codprov').append(cadena);
            }
                                
           if(accion == 1){
                $("#codprov > option").each(function () {
                    if ($(this).html() == provSelected) {
                        $(this).attr("selected", "selected");
                        return;
                    }
                });
                Carga_Distrito($("#codprov option:selected").html());
            }
        },
        error: function(err){
            swal("Error!", err, "error");
        }
    });
}
            
function Carga_Distrito(provincia){
    $.ajax({
        url: "src/path/Ubigeo.php/api/listarDistrito/"+provincia,
        success: function(response){
            myObj = JSON.parse(response);
            for(x in myObj){
                var cadena = '';
                cadena = "<option value='"+myObj[x].CODDIST+"'>"+myObj[x].DISTRITO+"</option>";
                $('#coddist').append(cadena);
            }
           if(accion == 1){
                $("#coddist > option").each(function () {
                    if ($(this).html() == distSelected) {
                        $(this).attr("selected", "selected");
                        accion = 0;
                        return;
                    }
                });
            }
        },
        error: function(err){
            swal("Error!", err, "error");
        }
    });
}

function CerrarApp(){
    $.post('index.php',function(datos){
    	$("#card").html('');
	    $("#card").html(datos);
    });

}

function ListarCliente(){
    $("#card").html('');
    $.post('public/List_Cliente.html',function(datos){
        $("#card").html(datos);
            
            $.ajax({
                url: "src/path/Cliente.php/api/listarCliente"
            }).then(function(data) {
             

               myObjClient = JSON.parse(data);
 
                
                var col = [];
                for (var i = 0; i < myObjClient.length; i++) {
                    for (var key in myObjClient[i]) {
                        if (col.indexOf(key) === -1) {
                            col.push(key);
                        }
                    }
                }

                
                var table = document.createElement("table");
                
                table.classList.add('table');
                table.id = "myTableCliente";
                
                var tr = table.insertRow(-1); 
                
                for (var i = 0; i < col.length; i++) {
                    var th = document.createElement("th");      // TABLE HEADER.
                    th.innerHTML = col[i];
                    tr.appendChild(th);
                }
                
                for (var i = 0; i < myObjClient.length; i++) {

                    tr = table.insertRow(-1);
                    tr.id = myObjClient[i][col[0]];

                    for (var j = 0; j < col.length; j++) {
                        var tabCell = tr.insertCell(-1);
                        tabCell.innerHTML = myObjClient[i][col[j]];
                    }
                }
                
                var divContainer = document.getElementById("showData");
                divContainer.innerHTML = "";
                divContainer.appendChild(table);
                
                $("#myTableCliente tr").click(function() {
                    if($(this).attr("id") != null){
                        $(this).parent().find(".info").removeClass("info");    
                        $(this).addClass("info");
                        clientSelected = $(this).attr("id");
                        var id = $(this).index();
                        clientSelected = $("#myTableCliente tr:eq("+id+") td:eq(0)").html();
                        nombresSelected = $("#myTableCliente tr:eq("+id+") td:eq(1)").html();
                        fechanacSelected = $("#myTableCliente tr:eq("+id+") td:eq(2)").html();
                        depaSelected = $("#myTableCliente tr:eq("+id+") td:eq(3)").html();
                        provSelected = $("#myTableCliente tr:eq("+id+") td:eq(4)").html();
                        distSelected = $("#myTableCliente tr:eq("+id+") td:eq(5)").html();
                        direcSelected = $("#myTableCliente tr:eq("+id+") td:eq(6)").html();
                    }    
                });
            });
    });
}

function EliminarCliente(){
    if(clientSelected == "" | clientSelected == "0" | clientSelected == undefined | clientSelected == null){
        swal("Advertencia!!", "Para eliminar seleccione un Cliente...", "warning");
        return false;
    }
    $.ajax({
    	type: "DELETE",
        url: "src/path/Cliente.php/api/Cliente/Eliminar/"+clientSelected,
        ContentType:"application/json; charset=utf-8",
        success: function(response){           
           swal("Correcto!!", response, "success");
           ListarCliente();
        },
        error: function(err){
            swal("Error!", err, "error");
        }
    });
}