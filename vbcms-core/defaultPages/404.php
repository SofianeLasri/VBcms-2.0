<?php
// Arrive si on charge la page seule, ce n'est pas censé arriver
if(!isset($websiteUrl)){
    if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";
    $url = parse_url("$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    $websiteUrl = $url["scheme"]."://".$url["host"]."/";
} 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
	<title>VBcms | Erreur 404</title>
    <link rel="icon" type="image/png" href="https://vbcms.net/vbcms-content/uploads/vbcms-logo/raccoon-in-box-512x.png" />
    <link rel="stylesheet" href="<?=$websiteUrl?>vbcms-admin/fonts/fonts.css">
</head>
<body>
    <style type="text/css">
        body{
            margin: 0;
            background-color: #bf946f;
        }
        .pageContent{
            display: flex;
            flex-direction: column;
            position: absolute;
            z-index: 10;
            margin: 0 auto;
            width: 100%;
            margin-top: 50vh;
            transform: translateY(-50%);
            text-align: center;
            color: white;
            font-family: "Inter Regular";
        }
        .vbcmsLogo{
            display: flex;
            align-self: center;
            flex-direction: row;
            align-items: center;
        }
        .vbcmsLogo img{
            width:8em;
            height:8em;
        }
        .vbcmsLogo span{
            font-family: "Linotype Kaliber Bold";
            font-size: 2em;
        }
    </style>
    <div class="pageContent">
        <div class="vbcmsLogo">
            <img src="https://vbcms.net/vbcms-content/uploads/vbcms-logo/raccoon-in-box-512x.png">
            <span>VBcms</span>
        </div>
        <p>Erreur 404: La page demandée n'existe pas. :/</p>
    </div>
</body>
</html>