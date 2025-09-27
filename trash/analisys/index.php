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
    <div style="padding-top: 2%; padding-left: 2%;">
        <a href="https://visorfinanciero.info/" class="redondo" type="submit">Inicio </a>
        <a href="http://portal.visorfinanciero.info/comments/" class="redondo">Comentarios </a>
    </div>
    <!--<center><img style="width:15%" src="http://portal.visorfinanciero.info/images/visor_financiero.jpg"></center>-->

<!--main content start-->
<section id="main-content">
    <section class="wrapper">

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
        allowClear: false,
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
                            '<td style="width: 10%;" class="td-table"></td>' +
                            '<td style="width: 10%;" class="td-table"></td>' +
                            '<td style="width: 65%;" class="td-table"></td>' +
                            '<td style="width: 10%;" class="td-table"></td>' +
                        '</tr>';
                for(i = 0; i < response.data.length; i++){
                    table += '<tr class="spaceUnder">' +
                                '<td>' + response.data[i].date + '</td>' + 
                                '<td>' + response.data[i].type + '</td>' + 
                                '<td>' + response.data[i].comments + '</td>' + 
                                '<td></td>' + 
                            '</tr>' +
                            '<tr class="spaceUnder">';

                            if(response.data[i].link == null || response.data[i].link == ""){
                                table += '<td colspan=4></td>';
                            } else {
                                table += '<td colspan=4><center><object data="' + response.data[i].link + '" width="800" height="500"></object></center></td>';
                            }

                            table += '</tr>';

                }

                $('#get_analisys').empty();
                $('#get_analisys').append(table);
            }
        });
    }
</script>