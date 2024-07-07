<?php 

include "./../../eyeknow-config-web/init.php";


##-- Page variables
$__Company__ = "Visor Financiero";
$__PageTitle__ = "Comentarios";

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
<body class="full-width" onload="getComments()"> 
    <div style="padding-top: 2%; padding-left: 2%;">
        <a href="https://visorfinanciero.info/" class="redondo">Inicio </a>
        <a href="http://portal.visorfinanciero.info/analisys/" class="redondo">Análisis </a>
    </div>
    <!--<center><img style="width:15%" src="http://portal.visorfinanciero.info/images/visor_financiero.jpg"></center>-->

<!--main content start-->
<section id="main-content">
    <section class="wrapper">
	   

        <div id="id" style="display: none;"></div>
        <div id="link" style="display: none;"></div>

        <!--
        <button onclick="new_comments()" class="redondo">Nuevo comentario </button>

        <table style="width:100%">
            <tr>
                <th width="15%">Fecha</th>
                <th width="50%">Comentarios</th>
                <th width="20%">Gráfico</th>
                <th width="10%"></th>
            </tr>
            <tr id="add_comments" style="display: none;">
                <td class="td-table"><input class="form-control input change" type="date" id="date" onchange="updateComments(this, 'date')" /></td>
                <td class="td-table"><textarea class="form-control input change" id="comments" onchange="updateComments(this, 'comments')"></textarea></td>
                <td class="td-table">
                    <form id="add_new_file" enctype="multipart/form-data" accept-charset="UTF-8">
                        <input id="file_pdf" type="file" name="archivo">
                        <button>Upload file</button>
                    </form>
                </td>
                <td><button onclick="reset_comments()">Guardar </button></td>
            </tr>
        </table>
        -->
      
        <table>
            <tbody id="get_comments">
            
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
    function new_comments() {
        $('#add_comments').show();

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/comments/add',
            async: false,
            type: "POST",
            data: { },
            success: function(response){
                console.log(response.data[0].id);
                document.getElementById("id").innerHTML = response.data[0].id;
            }
        });
    }

    function updateComments(editableObj,column) {

        console.log("column: " + column + " value: " + editableObj.value + " length: " + editableObj.value.length);
        id = $('#id').text();

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/comments/update',
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
            }
        });
        e.preventDefault(); 
    }); 

    function reset_comments() {

        id = $('#id').text();
        link = $('#link').text();

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/comments/update',
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
                document.getElementById( 'add_comments' ).style.display = 'none';

                document.getElementById("date").value = "";
            
                document.getElementById("comments").value = "";
                document.getElementById("file_pdf").value = "";
            }
        });
    }

    function getComments() {

        $.ajax({  
            
            url: 'http://ec2-3-131-17-88.us-east-2.compute.amazonaws.com/visor_financiero/comments/get',
            async: false,
            type: "GET",
            data: { },
            success: function(response){
                console.log('tamaño: ' + response.data.length);

                table = '<tr>' +
                            '<td style="width: 10%;" class="td-table">Fecha</td>' +
                            '<td style="width: 85%;" class="td-table">Comentario</td>' +
                            '<td style="width: 5%;" class="td-table"></td>' +
                        '</tr>';
                for(i = 0; i < response.data.length; i++){
                    table += '<tr class="spaceUnder">' +
                                '<td>' + response.data[i].date + '</td>' + 
                                '<td>' + response.data[i].comments + '</td>' + 
                                '<td></td>' + 
                            '</tr>'+
                            '<tr class="spaceUnder">' + 
                                '<td colspan=3><center><img src="' + response.data[i].link + '" style="width: 50%; text-align: center;"></center></td>' +
                            '</tr>';
                }

                $('#get_comments').empty();
                $('#get_comments').append(table);
            }
        });
    }
</script>