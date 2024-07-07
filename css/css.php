<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="Eyeknow">

<meta http-equiv=”X-UA-Compatible” content=”IE=EmulateIE9”>
<meta http-equiv=”X-UA-Compatible” content=”IE=9”>

<link rel="shortcut icon" type="image/x-icon" href="<?= $__URL_FAVICON__ ?>">

<!--Core CSS -->
<link href="../resources/css/bootstrap.min.css" rel="stylesheet"> 
<link href="../resources/css/jquery-ui-1.10.1.custom.min.css" rel="stylesheet">
<link href="../resources/css/bootstrap-reset.css" rel="stylesheet"> 
<link href="../resources/css/jquery-jvectormap-1.2.2.css" rel="stylesheet">
<link href="../resources/css/clndr.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="../resources/css/style.css" rel="stylesheet">
<link href="../resources/css/style-responsive.css" rel="stylesheet"/> 
<link href="../resources/css/style-eyeknow.css" rel="stylesheet">  
    
<!-- Button -->
<link rel="stylesheet" type="text/css" href="../resources//css/button.css" />

<!--  boostrap select -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

<!--eyeknow -->
<link href="../resources/css/notifications.css" rel="stylesheet">
<style>
    .select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #efefef;
    border-radius: 4px;
    }

    .select2-container .select2-selection--single {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        height: 35px;
        user-select: none;
        -webkit-user-select: none;
    }

    .form-control:focus, #focusedInput {
        border: 3px solid #96c227;
        box-shadow: none;
    }

    body {
        margin: 0;
        background-color: #ffffff00;
        color: #000;
    }

    .form-control {
        color: #393939;
        font-size: 13px;
        border: 1px solid #959597;
    }

    #block_container {
        display: flex;
        justify-content: center;
    }

    .reset-modal-dialog{
        margin-top: 1%; 
        width: 70%;
    }

    .reset-modal{
        /*font-family: fantasy;*/
    }

    .reset-title-modal{
        font-size: 1.2em; 
        background-color: #fafafa; 
        margin-left: 10%;
        padding-top: 1%;
    }

    .reset-box-modal{
        max-height: 100px; 
        overflow: auto;  
        width: 80%; 
        height: 200px; 
        border: 3px solid #fff; 
        background-color: #fff; 
        margin: auto;
        font-size: 14px; 
        border-radius: 10px;
        border: 1px solid #b8b8b8;
    }

    .reset-button{
        background-color: #95b75d;
        border-color:#95b75d;
        color: #ffffff;
        font-size: 16px;
        margin-left: 10%;
        padding: 2%;
    }

    @media only screen and (max-width: 12000px) {
        .reset-modal-dialog {
            margin-top: 1%;
            padding-right: 3%;
            width: 100%; 
        }

        .hide-media-screen{
            display:none;
            width:0;
            height:0;
            opacity:0;
            visibility: collapse; 
        }
    }


    .block_container
    {
        /*text-align:center;*/
        display: inline-flex;
    }

    .bloc1, .bloc2
    {
        display:inline;
    }

    .select2-selection__rendered {
        line-height: 31px !important;
    }

    .select2-container .select2-selection--single {
        height: 35px !important;
    }

    .select2-selection__arrow {
        height: 34px !important;
    }

    .table-scroll {
        position: relative;
        width:100%;
        z-index: 1;
        margin: auto;
        overflow: auto;
        height: 350px;
    }
    .table-scroll table {
        width: 100%;
        min-width: 1280px;
        margin: auto;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-wrap {
        position: relative;
    }

    .table-scroll th,
    .table-scroll td {
        padding: 5px 10px;
        border: 1px solid #000;
        background: #fff;
        vertical-align: top;
    }
    .table-scroll thead th {
        background: #333;
        color: #fff;
        position: -webkit-sticky;
        position: sticky;
        top: 0;
    }

    .table-scroll tfoot,
    .table-scroll tfoot th,
    .table-scroll tfoot td {
        position: -webkit-sticky;
        position: sticky;
        bottom: 0;
        background: #666;
        color: #fff;
        z-index:4;
    }

    th:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 2;
        background: #fff;
    }
    thead th:first-child,
    tfoot th:first-child {
        z-index: 5;
    }

    .modal-header {
        min-height: 16.43px;
        padding: 15px;
        border-bottom: 1px solid #fafafa;
    }

    .close {
        font-size:60px;
        font-weight: 700;
        line-height: 0.32;
        color: #000;
        text-shadow: 0 1px 0 #fff;
    }

    .modal-open .modal {
        overflow-x: initial;
        overflow-y: initial;
    }
    
    .td-table{
        font-size:12px;
        padding: 1%;
    }

    .form-control {
        color: #393939;
        font-size:11px;
        border: 1px solid #959597;
    }

    .redondo{
        display: inline-block;
        padding: 1em 2em;
        background: #048fd5;
        color: white;
        border-radius: 2em;
        border:none;
        font-weight: 700;
    }

    tr.spaceUnder>td {
        padding-bottom: 2em;
    }

    .wrapper {
        display: inline-block;
        margin-top: 20px;
        padding: 15px;
        width: 100%;
    }
</style>