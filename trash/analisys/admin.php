<?php 

include "./../../eyeknow-config-web/init.php";


##-- Page variables
$__Company__ = "Visor Financiero";
$__PageTitle__ = "Análisis";

// *****************************************************************************************************************
// ************************************************* Page config ***************************************************


?> 
 
<!-- Inicia header de página  --> 
<!-- Formato: horizontal -->
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title><?php echo $__Company__; ?> - <?php echo $__PageTitle__; ?>  </title>
    
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

    <?php include_once "../css.php"; ?> 

</head>
<body class="full-width" onload="getAnalisys()"> 

<!--<img src="http://portal.visorfinanciero.info/images/visor_financiero.jpg" style="width: 10%; margin-right: 5%; ; margin-left: 5%;">-->

<a href="http://portal.visorfinanciero.info/analisys/" class="redondo">Análisis vista</a>

<a href="http://portal.visorfinanciero.info/comments/" class="redondo">Comentarios vista</a>

<a href="http://portal.visorfinanciero.info/analisys/admin.php" class="redondo">Análisis configuración</a>

<a href="http://portal.visorfinanciero.info/comments/admin.php" class="redondo">Comentarios configuración</a>

<!--main content start-->
<section id="main-content">
    <section class="wrapper">
	   

        <div id="id" style="display: none;"></div>
        <div id="link" style="display: none;"></div>

        <button onclick="new_analisys()" class="redondo">Nuevo análisis </button>
        <!--<img width="48" height="48" src="https://img.icons8.com/emoji/48/plus-emoji.png" alt="plus-emoji"/>-->

        <table style="width:100%">
            <tr>
                <th width="15%">Fecha</th> 
                <th width="15%">Tipo</th>
                <th width="40%">Comentarios</th>
                <th width="20%">Presentación</th>
                <th width="10%"></th>
            </tr>
            <tr id="add_analisys" style="display: none;">
                <td class="td-table"><input class="form-control input change" type="date" id="date" onchange="updateAnalisys(this, 'date')" /></td>
                <td class="td-table">
                    <select class="type form-control" style="width:100%" name="type" id="type" onchange="updateAnalisys(this, 'type')"></select>
                </td>
                <td class="td-table"><textarea class="form-control input change" id="comments" onchange="updateAnalisys(this, 'comments')"></textarea></td>
                <td class="td-table">
                    <form id="add_new_file" enctype="multipart/form-data" accept-charset="UTF-8">
                        <input id="file_pdf" type="file" name="archivo">
                        <input name="value_id" id="value_id" value= "12345"/>
                        <button>Upload file</button>
                    </form>
                </td>
                <td><button id="reset_comments" onclick="reset_analisys()" disabled>Guardar </button></td>
            </tr>
        </table>

      
        <table>
            <tbody id="get_analisys">
            
            </tbody>
        </table>
                        
    <!--main content end-->
    </section>
</section>
<!-- Placed js at the end of the document so the pages load faster -->
 
    <?php include_once "../js.php"; ?> 

</body>
</html> 
 
<script>
    function new_analisys() {
        $('#add_analisys').show();
        //$('#add_analisys').hide();

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/analisys/add',
            async: false,
            type: "POST",
            data: { },
            success: function(response){
                console.log(response.data[0].id);
                document.getElementById("id").innerHTML = response.data[0].id;
            }
        });
    }

    function updateAnalisys(editableObj,column) {

        console.log("column: " + column + " value: " + editableObj.value + " length: " + editableObj.value.length);
        id = $('#id').text();

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/analisys/update',
            async: false,
            type: "POST",
            data: { 
                column: column,
                value: editableObj.value,
                id: id
            },
            success: function(response){
                console.log(response);
            }
        });
    }

    $('#type').select2({
        allowClear: true,
        placeholder: 'Buscar...',
        tags: true,
        ajax: {
        url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/analisys/catalog/type',
        dataType: 'json',
        delay: 250,
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
        }
    });

    $("#add_new_file").submit(function(e) { 

        var formData = new FormData(this);
        var url = '<?php echo $__URL_REPOSITORY__; ?>daily.php';
 
        $.ajax({
            type: "POST",
            url: 'http://portal.visorfinanciero.info/documents/add_file.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function (r) {

                var json = $.parseJSON(r);
                console.log(json.url); 

                document.getElementById("link").innerHTML = json.url;
                $('#reset_comments').prop('disabled', false);
            }
        });
        e.preventDefault(); 
    }); 

    $("form").submit(function(e) { 

        var formData = new FormData(this);
        var url = '<?php echo $__URL_REPOSITORY__; ?>daily.php';

        $.ajax({
            type: "POST",
            url: 'http://portal.visorfinanciero.info/documents/update_file.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function (r) {

                var json = $.parseJSON(r);
                console.log(json.url); 

                document.getElementById("link").innerHTML = json.url;
                $('#reset_comments').prop('disabled', false);
            }
        });
        
        e.preventDefault(); 
    }); 

    function reset_analisys() {

        id = $('#id').text();
        link = $('#link').text();

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/analisys/update',
            async: false,
            type: "POST",
            data: { 
                column: "link",
                value: link,
                id: id
            },
            success: function(response){
                console.log(response);
                document.getElementById("id").innerHTML = "";
                document.getElementById("link").innerHTML = "";
                document.getElementById( 'add_analisys' ).style.display = 'none';

                document.getElementById("date").value = "";
                try {
                    var newOption = new Option("", "", true, true);
                    $('#type').append(newOption).trigger('change');
                } catch(err){  }
                
                document.getElementById("comments").value = "";
                document.getElementById("file_pdf").value = "";
                $('#reset_comments').prop('disabled', true);
            }
        });
    }

    function getAnalisys() {

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/analisys/get',
            async: false,
            type: "GET",
            data: { },
            success: function(response){
                console.log('tamaño: ' + response.data.length);

                table = '<tr>' +
                            '<td style="width: 20%;" class="td-table">Fecha</td>' +
                            '<td style="width: 10%;" class="td-table">Tipo</td>' +
                            '<td style="width: 50%;" class="td-table">Comentario</td>' +
                            '<td style="width: 10%;" class="td-table">Presentación</td>' +
                            '<td style="width: 10%;" class="td-table">Eliminar</td>' +
                        '</tr>';
                for(i = 0; i < response.data.length; i++){
                    table += '<tr>' +
                                '<td>' + response.data[i].date + '</td>' + 
                                '<td contenteditable="true" onBlur="updateVf(this,' + "'type'" + ',' + response.data[i].id + ')">' + response.data[i].type + '</td>' + 
                                '<td contenteditable="true" onBlur="updateVf(this,' + "'comments'" + ',' + response.data[i].id + ')">' + response.data[i].comments + '</td>' + 
                                '<td>' + 
                                    '<form action="../documents/update_file_analisys.php" method="POST" enctype="multipart/form-data" accept-charset="UTF-8">' +
                                        '<input id="file_pdf" type="file" name="archivo">' +
                                        '<input style="display: none;" name="value_id" id="value_id" value= "' + response.data[i].id + '"/>' +
                                        '<button>Upload file</button>' +
                                    '</form>' +
                                '</td>' + 
                                '<td><button onclick="deleteVf(' + response.data[i].id +  ')">Eliminar</button></td>'
                            '</tr>';
                }

                $('#get_analisys').empty();
                $('#get_analisys').append(table);
            }
        });
    }

    function updateVf (editableObj,column,id) {
        
        console.log("column: " + column + " id: " + id + " value: " + $(editableObj).text());

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/' + 'visor_financiero/inline/update',
            async: false,
            type: "POST",
            data: { 
                    column: column,
                    id: id,
                    value: $(editableObj).text(),
                    table: 'vf_analisys'
                },
            success: function(response){ 
               
            }
        });
    }

    function deleteVf (id) {
        

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/' + 'visor_financiero/delete',
            async: false,
            type: "POST",
            data: { 
                    id: id,
                    table: 'vf_analisys'
                },
            success: function(response){ 
               
            }
        });
    }
</script>