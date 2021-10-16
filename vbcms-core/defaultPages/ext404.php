<?php
if(isset($bdd)){
    $websiteUrl = VBcmsGetSetting("websiteUrl");
}else{
    if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";
    $url = parse_url("$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    $websiteUrl = $url["scheme"]."://".$url["host"]."/";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
	<title>VBcms</title>
    <link rel="icon" type="image/png" href="<?=$websiteUrl?>vbcms-admin/images/vbcms-logo/raccoon-in-box-512x.png" />
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
            width:7em;
            height:7em;
        }
        .vbcmsLogo span{
            font-family: "Linotype Kaliber Bold";
            font-size: 2em;
        }
        .vbcmsLogo .sub{
            font-family: "Linotype Kaliber Bold";
            font-size: 1em;
        }
        .vbcmsLogo .text{
            display:flex;
            flex-direction:column;
            align-items: start;
            margin-left: .5em;
        }
    </style>
    <div class="pageContent">
        <div class="vbcmsLogo">
            <img src="<?=$websiteUrl?>vbcms-admin/images/vbcms-logo/raccoon-in-box-512x.png">
            <div class="text">
                <span>VBcms</span>
                <span class="sub">Erreur 404: L'extension demandée n'existe pas</span>
            </div>
        </div>
    </div>
</body>
</html>