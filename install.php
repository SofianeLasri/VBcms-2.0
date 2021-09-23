<?php
error_reporting(E_ALL);
if(empty($_GET)&&empty($_POST)){
    $operation = "installation";
} 

if(isset($_GET['update'])){
    $operation = "update";
}elseif(isset($_GET['databaseTest'])&&!empty($_GET['databaseTest'])){
    if(file_exists("tempInstallConfig.json")){
        $savedParameters = file_get_contents("tempInstallConfig.json");
        $savedParameters = json_decode($savedParameters, true);
    }

    if($_GET['databaseTest'] == "get"){
        $bddInfos = array();        
        if (isset($savedParameters) && $savedParameters) {            
            if(isset($savedParameters['bddHost'])) $bddInfos['bddHost'] = $savedParameters['bddHost'];
            else $bddInfos['bddHost'] = null;
    
            if(isset($savedParameters['bddUser'])) $bddInfos['bddUser'] = $savedParameters['bddUser'];
            else $bddInfos['bddUser'] = null;
            
            if(isset($savedParameters['bddMdp'])) $bddInfos['bddMdp'] = $savedParameters['bddMdp'];
            else $bddInfos['bddMdp'] = null;
    
            if(isset($savedParameters['bddName'])) $bddInfos['bddName'] = $savedParameters['bddName'];
            else $bddInfos['bddName'] = null;
        }
        
        echo json_encode($bddInfos);
    }elseif($_GET['databaseTest'] == "send"){
        if(!empty($_POST)){
            foreach($_POST as $index => $value){
                if(!empty($index)){
                    $savedParameters[$index] = $value;
                }
            }
            file_put_contents("tempInstallConfig.json", json_encode($savedParameters));

            $bddError = false;
            try {
                $bddConn = new PDO("mysql:host=".$savedParameters['bddHost'].";dbname=".$savedParameters['bddName'], $savedParameters['bddUser'], $savedParameters['bddMdp']); //Test de la connexion
            } catch (PDOException $e) {
                $bddError = true;
                echo $e->getCode()." - ";
                if ($e->getCode() == 1045) {
                    echo "Mauvais couple identifiant/mot de passe.";
                } elseif ($e->getCode() == 1044) {
                    echo "L'utilisateur n'a pas accès à la base spécifiée.";
                } elseif ($e->getCode() == 2002) {
                    echo "L'hôte de la base de donnée est inaccessible.";
                } else {
                    print $e->getMessage();
                }
            }
            if(!$bddError){
                echo "success";
            }
        }else{
            echo "Aucune donnée n'a été envoyée (formulaire POST vide).";
        }
    }else{
        echo "Commande databaseTest(".$_GET['databaseTest'].") non reconnue.";
    }
}

if(isset($_GET['checkRequirements'])){
    $check["PHP"] = PHP_VERSION_ID>=70200;
    $check["PDO"] = defined('PDO::ATTR_DRIVER_NAME');
    $check["WRITABLE"] = is_writable(getcwd());
    $check["ZIP"] = extension_loaded('zip');
    echo json_encode($check);
}

if(isset($operation)||isset($_GET['step'])) { 
if(!isset($operation)) $operation = "installation";    
?>
    
<htlm lang="fr">
    <head>
        <meta charset="utf-8">
        <title>VBcms | Installation</title>
        <meta name="theme-color" content="#BF946F">
        <meta name="author" content="Sofiane Lasri">
        <link rel="icon" href="https://i.imgur.com/ArZg6zX.png" type="image/png">
    
        <meta content="VBcms" property="og:title">
        <meta content="Installation de VBcms" property="og:description">
        <meta content='https://i.imgur.com/ArZg6zX.png' property='og:image'>
    
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
       <!-- Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    </head>
    <body operation="<?=$operation?>">
        <!-- JS Snackbar -->
        <style type="text/css">
            .js-snackbar-container {
                position: absolute;
                bottom: 0;
                right: 0;
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                max-width: 100%;
                padding: 10px;
                z-index: 999;
                overflow: hidden;
            }

            .js-snackbar-container--top-left {
                bottom: unset;
                right: unset;
                top: 0;
                left: 0;
            }

            .js-snackbar-container--top-right {
                bottom: unset;
                right: 0;
                left: unset;
                top: 0;
            }

            .js-snackbar-container--bottom-left {
                bottom: 0;
                right: unset;
                left: 0;
                top: unset;
            }

            .js-snackbar-container--fixed {
                position: fixed;
            }

            .js-snackbar-container * {
                box-sizing: border-box;
            }

            .js-snackbar__wrapper {
                overflow: hidden;
                height: auto;
                margin: 5px 0;
                transition: all ease .5s;
                border-radius: 3px;
                box-shadow: 0 0 4px 0 black;
            }

            .js-snackbar {
                display: inline-flex;
                box-sizing: border-box;
                border-radius: 3px;
                color: #212529;
                font-size: 16px;
                background-color: white;
                vertical-align: bottom;
            }

            .js-snackbar__close,
            .js-snackbar__status,
            .js-snackbar__message {
                position: relative;
            }

            .js-snackbar__message {
                padding: 12px;
            }

            .js-snackbar__status {
                display: none;
                width: 15px;
                margin-right: 5px;
                border-radius: 3px 0 0 3px;
                background-color: transparent;
            }

            .js-snackbar__status.js-snackbar--success,
            .js-snackbar__status.js-snackbar--warning,
            .js-snackbar__status.js-snackbar--danger,
            .js-snackbar__status.js-snackbar--info {
                display: block;
            }

            .js-snackbar__status.js-snackbar--success  {
                background-color: #4caf50;
            }

            .js-snackbar__status.js-snackbar--warning  {
                background-color: #ff9800;
            }

            .js-snackbar__status.js-snackbar--danger {
                background-color: #b90909;
            }

            .js-snackbar__status.js-snackbar--info {
                background-color: var(--lightMB);
            }

            .js-snackbar__action {
                display: flex;
                align-items: center;
                padding: 0 10px;
                color: #838cff;
                cursor: pointer;
            }

            .js-snackbar__action:hover {
                background-color: #333;
            }

            .js-snackbar__close {
                cursor: pointer;
                display: flex;
                align-items: center;
                padding: 0 10px;
                user-select: none;
                color: #BBB;
            }

            .js-snackbar__close:hover {
                background-color: #444;
            }
        </style>

        <!-- Fonts -->
        <style type="text/css">
            @font-face {
                font-family: "Linotype Kaliber";
                src: url("//db.onlinewebfonts.com/t/8271dc7c2cb7809ba144276fdea636ab.eot");
                src: url("//db.onlinewebfonts.com/t/8271dc7c2cb7809ba144276fdea636ab.eot?#iefix") format("embedded-opentype"),
                url("//db.onlinewebfonts.com/t/8271dc7c2cb7809ba144276fdea636ab.woff2") format("woff2"),
                url("//db.onlinewebfonts.com/t/8271dc7c2cb7809ba144276fdea636ab.woff") format("woff"),
                url("//db.onlinewebfonts.com/t/8271dc7c2cb7809ba144276fdea636ab.ttf") format("truetype"),
                url("//db.onlinewebfonts.com/t/8271dc7c2cb7809ba144276fdea636ab.svg#Linotype Kaliber") format("svg");
            }
        </style>

        <!-- Style custom -->
        <style type="text/css">
            :root{
              --mainBrown: #bf946f;
              --secondaryBrown: #a77c58;
              --darkBrown: #74492a;
              --darkerBrown: #5c351f;
    
              --lightMB: #d3ab89;
            }
    
            ::-webkit-scrollbar {
                width: 5px;
                height: 7px;
            }
            ::-webkit-scrollbar-button {
                width: 0px;
                height: 0px;
            }
            ::-webkit-scrollbar-corner {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #525965;
                border: 0px none #ffffff;
                border-radius: 0px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
                border: 0px none #ffffff;
                border-radius: 50px;
            }
            html{
                height: 100%
            }
            body{
                font-size: 14px;
                font-family: 'Inter', sans-serif;
    
                /*background-image: url("https://vbcms.net/vbcms-admin/images/general/vbcms-illustration1.jpg");*/
                background: rgb(167,124,88);
                background: -moz-linear-gradient(180deg, rgba(167,124,88,1) 0%, rgba(116,73,42,1) 100%);
                background: -webkit-linear-gradient(180deg, rgba(167,124,88,1) 0%, rgba(116,73,42,1) 100%);
                background: linear-gradient(180deg, rgba(167,124,88,1) 0%, rgba(116,73,42,1) 100%);
                background-size: cover;
                background-position: center;
            }
            ul{
                padding: 0;
                list-style: none;
            }
            .text-brown{
                color: var(--mainBrown);
            }
            .btn-brown {
              color: #fff;
              background-color: var(--mainBrown);
              border-color: var(--mainBrown);
            }
    
            .btn-brown:hover {
              color: #fff;
              background-color: var(--lightMB);
              border-color: var(--lightMB);
            }
    
            .btn-brown:focus, .btn-brown.focus {
              color: #fff;
              background-color: var(--darkerBrown);
              border-color: var(--darkerBrown);
              box-shadow: 0 0 0 0.2rem rgba(167, 124, 88, 0.5);
            }
    
            .btn-brown.disabled, .btn-brown:disabled {
              color: #fff;
              background-color: var(--secondaryBrown);
              border-color: var(--secondaryBrown);
            }
    
            .btn-brown:not(:disabled):not(.disabled):active, .btn-brown:not(:disabled):not(.disabled).active,
            .show > .btn-brown.dropdown-toggle {
              color: #fff;
              background-color: var(--secondaryBrown);
              border-color: var(--secondaryBrown);
            }
    
            .btn-brown:not(:disabled):not(.disabled):active:focus, .btn-brown:not(:disabled):not(.disabled).active:focus,
            .show > .btn-brown.dropdown-toggle:focus {
              box-shadow: 0 0 0 0.2rem rgba(167, 124, 88, 0.5);
            }
    
            .installDiv{
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
    
                width: 64rem;
                height: 36rem;
                background-color: white;
                border-radius: 5px;
                overflow: hidden;
                box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.5);
            }
            .installDiv .header{
                position: relative;
                display: flex;
                width: 100%;
                height: 50px;
                background-color: var(--mainBrown);
                align-items: center;
            }
            .installDiv .header img{
                margin-left: 5px;
                margin-right: 5px;
            }
            .installDiv .header span{
                font-family: "Linotype Kaliber";
                font-size: 1.2em;
                font-weight: bold;
                color: white;
            }
            #installStateTitle{
                position: absolute;
                width: 100%;
                text-align: center;
            }
            .installContent{
                height: 100%;
            }
            .centerItems{
                position: absolute;
                margin: 0;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
    
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
            }
            .installNavButtons{
                position: absolute;
                bottom: 0;
                width: 100%;
            }
            .progress{
                height: 5px !important;
                border-radius: 0px!important;
                background-color: #e6e6e6
            }
            .progress-bar{
                background-color: var(--lightMB) !important;
            }
            .sidebar{
                flex-basis: 200px;
                flex-grow: 0;
                flex-shrink: 0;
            }
            .stateIndicator{
                border-radius: 5px;
                margin-top: .25rem;
            }
            .stateIndicator span{
                margin: 0 .25rem 0 .25rem;
            }
            .stateIndicator.active{
                background-color: var(--lightMB);
                color: white!important
            }
            .stateIndicator.passed{
                background-color: #dadada;
            }
            .content{
                overflow-y: scroll;
                height: calc(100% - 42px)!important;
            }
            .controls{
                position: relative;
                bottom: 0;
                height: 32px;
                margin: 5px;
            }
        </style>

        <div class="installDiv">
            <div class="header">
                <img height="45" src="https://i.imgur.com/ArZg6zX.png" alt="vbcms-logo">
                <span>VBcms</span><span id="installStateTitle">Bienvenu</span>
            </div>
            <div class="d-flex" style="height: calc(100% - 50px);">
                <div class="sidebar p-2 bg-light">
                    <h3>Étapes</h3>
                    <div style="overflow-y: scroll;">
                        <ul id="sidebar">
                            <!-- <li id="state-0" class="stateIndicator">Test</li> -->
                        </ul>
                    </div>
                </div>
                
                <div class="w-100">
                    <?php if($operation == "update"){ ?>

                    <div id="slide-0" title="" class="content" style="display: none;">
                        <div class="centerItems">
                            <h3>Bienvenu sur VBcms!</h3>
                            <p>Merci de participer aux tests de pré-release! <br>Le panel actuel ne reflète qu'assez peu le travail final, de nombreuses fonctionnalités vont être ajoutées dans les semaines à suivre.
                            <br><br><br>
                            Pour te remercier de ton investissement, une liscence t'as été offerte. Tu as juste à te connecter pour procéder à l'installation. ^^</p>
                        </div>
                    </div>
                    
                    <?php }elseif($operation == "installation"){ ?>
                    
                    <div id="slide-0" title="Bienvenue" class="content p-2" style="display: none;">
                        <div class="d-flex flex-column">
                            
                            <h3>Bienvenue sur VBcms!</h3>
                            <p>Merci d'avoir mon cms, j'espère qu'il vous sera autant utile qu'il l'est pour moi. :D
                            <br>
                            <br>Je vais vous guider durant toute l'installation. Vous pouvez rejoindre <a href="https://discord.gg/Qbgew8R4Ga" class="text-brown">mon serveur Discord</a> si vous le souhaitez, vous pourrez y demander de l'aide en cas de problème.
                            </p>
    
                            <h4>Une dernière chose</h4>
                            <p>Vous vous en doutez sûrement, VBcms est loin d'être terminé. En réalité, il s'agit là beaucoup plus d'une démo technique que d'un système réellement opérationnel. Bien que le projet, dans son état actuel, te permettra peut-être de réaliser tout ce que vous souhaitez faire, <strong>il faut bien prendre en compte le fait que le développement est très long que le code est encore très incomplet.</strong></p>
    
                            <p>Certains aspect de VBcms apparaissent ainsi négligés, me concentrant d'avantage sur le développement des fonctionnalités princiaples du cms que sur les détails qui les composent. Ainsi, certains points comme les messages et codes d'erreurs ne sont pas encore uniformisés. Certains fichiers gardent une architecture facilement améliorable, et la stabilité du système n'est pas garantie (<strong>surtout si vous choisissez le canal de mise à jour de développement</strong>).</p>
    
                            <p style="margin-bottom: 0;">Voilà, je crois que j'ai tout dit. :)
                            <br>
                            <br><strong>NOTE for English people:</strong> VBcms is not completely translated. At this time, only french translate is available. I recommend you to have Google traduction opened on a new tab. :)
                            </p>
                        </div>
                    </div>
                    <div id="slide-1" title="Pré-requis" class="content p-2" style="display: none;">
                        <div class="d-flex flex-column">
                            <h3>Pré-requis</h3>
                            <p>VBcms n'est pas compatible sur toutes les installation. L'ayant développé sur le tas sans avoir prévu une quelconque configuration spécifique, je vous propose là un test qui va simplement vérifier que vous disposez une installation similaire à la mienne.</p>

                            <div>
                                <ul id="prerequisCheck" style="list-style: inside; margin-left: 1rem;"></ul>
                            </div>
                        </div>
                    </div>
                    <div id="slide-2" title="Base de données" class="content p-2" style="display: none;">
                        <div class="d-flex flex-column">
                            <h3>Connexion à la base de données</h3>
                            <p>VBcms nécessite une base de donnée pour fonctionner. Il n'est pas nécessaire d'en créer une spécifiquement pour le cms, VBcms dispose de ses propres alias pour éviter tout conflit avec d'autres scripts.</p>

                            <form class="w-50" id="databaseConn" action="">
                                <div class="form-group">
                                    <label>Hôte de la base de donnée</label>
                                    <input required class="form-control form-control-sm" value="" type="text" id="databaseHost" name="bddHost">
                                    <small class="form-text text-muted">Souvent localhost sur les serveurs auto-hébergés</small>
                                </div>
                                <div class="form-group">
                                    <label>Nom de la base de donnée</label>
                                    <input required class="form-control form-control-sm" value="" type="text" id="databaseName" name="bddName">
                                </div>
                                <div class="form-group">
                                    <label>Utilisateur</label>
                                    <input required class="form-control form-control-sm" value="" type="text" id="databaseUser" name="bddUser">
                                </div>
                                <div class="form-group">
                                    <label>Mot de passe</label>
                                    <input class="form-control form-control-sm" value="" type="password" id="databasePass" name="bddMdp">
                                </div>
                                <button type="button" id="testDatabaseConn" onclick="databaseTest('send')" class="btn btn-sm btn-brown">Tester la connexion</button>
                            </form>
                        </div>
                    </div>
        
                    <?php } ?>

                    <div class="controls">
                        <button id="prevBtn" onclick="previousStep()" class=" btn btn-sm btn-brown float-left">Précédent</button>
				        <button id="nextBtn" onclick="nextStep()" class=" btn btn-sm btn-brown float-right">Suivant</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- JS Snackbar -->
        <script type="text/javascript">
            function SnackBar(userOptions) {
                var _This = this;
                var _Interval;
                var _Message;
                var _Element;
                var _Container;
                var _Parent;
                
                
                var _OptionDefaults = {
                    message: "Operation performed successfully.",
                    dismissible: true,
                    timeout: 5000,
                    status: "",
                    actions: [],
                    fixed: false,
                    position: "br"
                }
                var _Options = _OptionDefaults;

                function _Create() {
                    if (_Options.container === null || _Options.container === undefined) {
                        _Parent = document.body;
                    }
                    else {
                        if (typeof _Options.container === "object" && _Options.container instanceof Element) {
                            _Container = _Options.container;
                        }
                        else {
                            var targetParent = document.getElementById(_Options.container);

                            if (targetParent === undefined) {
                                console.error("SnackBar: Could not find target container " + _Options.container);
                                targetParent = document.body;
                            }

                            _Parent = targetParent;
                        }
                    }

                    _Container = searchChildren(_Parent);

                    if (!_Container) {
                        // need to create a new container for notifications
                        _Container = document.createElement("div");
                        _Container.classList.add("js-snackbar-container");

                        if(_Options.fixed) {
                            _Container.classList.add("js-snackbar-container--fixed");
                        }

                        _Parent.appendChild(_Container);
                    }

                    if (_Options.fixed) {
                        _Container.classList.add("js-snackbar-container--fixed");
                    }
                    else {
                        _Container.classList.remove("js-snackbar-container--fixed");
                    }

                    // Apply the positioning class
                    _Container.classList.add(getPositionClass());

                    _Element = document.createElement("div");
                    _Element.classList.add("js-snackbar__wrapper");

                    var innerSnack = document.createElement("div");
                    innerSnack.classList.add("js-snackbar", "js-snackbar--show");
                
                    if (_Options.status) {
                        _Options.status = _Options.status.toLowerCase().trim();

                        var status = document.createElement("span");
                        status.classList.add("js-snackbar__status");


                        if (_Options.status === "success" || _Options.status === "green") {
                            status.classList.add("js-snackbar--success");
                        }
                        else if (_Options.status === "warning" || _Options.status === "alert" || _Options.status === "orange") {
                            status.classList.add("js-snackbar--warning");
                        }
                        else if (_Options.status === "danger" || _Options.status === "error" || _Options.status === "red") {
                            status.classList.add("js-snackbar--danger");
                        }
                        else {
                            status.classList.add("js-snackbar--info");
                        }

                        innerSnack.appendChild(status);
                    }
                    
                    _Message = document.createElement("span");
                    _Message.classList.add("js-snackbar__message");
                    _Message.textContent = _Options.message;

                    innerSnack.appendChild(_Message);

                    if (_Options.actions !== undefined && typeof _Options.actions === "object" && _Options.actions.length !== undefined) {
                        for (var i = 0; i < _Options.actions.length; i++) {
                            var thisAction = _Options.actions[i];

                            if (thisAction !== undefined 
                                && thisAction.text !== undefined && typeof thisAction.text === "string") {

                                    if (thisAction.function !== undefined && typeof thisAction.function === "function"
                                        || thisAction.dissmiss !== undefined && typeof thisAction.dissmiss === "boolean" && thisAction.dissmiss === true) {

                                            var newButton = document.createElement("span");
                                                newButton.classList.add("js-snackbar__action");

                                                if (thisAction !== undefined && typeof thisAction.function === "function") {
                                                    if (thisAction.dissmiss !== undefined && typeof thisAction.dissmiss === "boolean" && thisAction.dissmiss === true) {
                                                        newButton.onclick = function() {
                                                            thisAction.function();
                                                            _This.Close()
                                                        };
                                                    }
                                                    else {
                                                        newButton.onclick = thisAction.function;
                                                    }
                                                }
                                                else {
                                                    newButton.onclick = _This.Close;
                                                }

                                                newButton.textContent = thisAction.text;

                                                innerSnack.appendChild(newButton);


                                        }
                                    
                                }
                        }


                    }

                    if (_Options.dismissible) {
                        var closeBtn = document.createElement("span");
                        closeBtn.classList.add("js-snackbar__close");
                        closeBtn.innerText = "\u00D7";

                        closeBtn.onclick = _This.Close;

                        innerSnack.appendChild(closeBtn);
                    }

                    _Element.style.height = "0px";
                    _Element.style.opacity = "0";
                    _Element.style.marginTop = "0px";
                    _Element.style.marginBottom = "0px";

                    _Element.appendChild(innerSnack);
                    _Container.appendChild(_Element);

                    if (_Options.timeout !== false) {
                        _Interval = setTimeout(_This.Close, _Options.timeout);
                    }
                }

                var _ConfigureDefaults = function() {
                    // if no options given, revert to default
                    if (userOptions === undefined) {
                        return;
                    }

                    if (userOptions.message !== undefined) {
                        _Options.message = userOptions.message;
                    }

                    if (userOptions.dismissible !== undefined) {
                        if (typeof (userOptions.dismissible) === "string") {
                            _Options.dismissible = (userOptions.dismissible === "true");
                        }
                        else if (typeof (userOptions.dismissible) === "boolean") {
                            _Options.dismissible = userOptions.dismissible;
                        }
                        else {
                            console.debug("Invalid option provided for 'dismissable' [" + userOptions.dismissible + "] is of type " + (typeof userOptions.dismissible));
                        }
                    }


                    if (userOptions.timeout !== undefined) {
                        if (typeof (userOptions.timeout) === "boolean" && userOptions.timeout === false) {
                            _Options.timeout = false;
                        }
                        else if (typeof (userOptions.timeout) === "string") {
                            _Options.timeout = parseInt(userOptions.timeout);
                        }


                        if (typeof (userOptions.timeout) === "number") {
                            if (userOptions.timeout === Infinity) {
                                _Options.timeout = false;
                            }
                            else if (userOptions.timeout >= 0) {
                                _Options.timeout = userOptions.timeout;
                            }
                            else {
                                console.debug("Invalid timeout entered. Must be greater than or equal to 0.");
                            }

                            _Options.timeout = userOptions.timeout;
                        }

                        
                    }

                    if (userOptions.status !== undefined) {
                        _Options.status = userOptions.status;
                    }

                    if (userOptions.actions !== undefined) {
                        _Options.actions = userOptions.actions;
                    }

                    if (userOptions.container !== undefined && (typeof userOptions.container === "string" || typeof userOptions.container === "object")) {
                        _Options.container = userOptions.container;
                    }

                    if (userOptions.fixed !== undefined) {
                        _Options.fixed = userOptions.fixed;
                    }

                    _Options.position = userOptions.position ?? _OptionDefaults.position;
                }



                var searchChildren = function(target) {
                    var htmlCollection = target.children;
                    var node = null;
                    var i = 0;
                    var positionClass = getPositionClass();

                    for (i = 0; i < htmlCollection.length; i++) {
                        node = htmlCollection.item(i);

                        if (node.nodeType === 1
                            && node.classList.length > 0
                            && node.classList.contains("js-snackbar-container")
                            && node.classList.contains(positionClass)) {
                            return node;
                        }
                    }

                    return null;

                    
                }

                this.Open = function() {
                    var contentHeight = _Element.firstElementChild.scrollHeight; // get the height of the content

                    _Element.style.height = contentHeight + "px";
                    _Element.style.opacity = 1;
                    _Element.style.marginTop = "5px";
                    _Element.style.marginBottom = "5px";

                    _Element.addEventListener("transitioned", function() {
                        _Element.removeEventListener("transitioned", arguments.callee);
                        _Element.style.height = null;
                    })
                }

                this.Close = function () {
                    if (_Interval)
                        clearInterval(_Interval);

                    var snackbarHeight = _Element.scrollHeight; // get the auto height as a px value
                    var snackbarTransitions = _Element.style.transition;
                    _Element.style.transition = "";

                    requestAnimationFrame(function() {
                        _Element.style.height = snackbarHeight + "px"; // set the auto height to the px height
                        _Element.style.opacity = 1;
                        _Element.style.marginTop = "0px";
                        _Element.style.marginBottom = "0px";
                        _Element.style.transition = snackbarTransitions

                        requestAnimationFrame(function() {
                            _Element.style.height = "0px";
                            _Element.style.opacity = 0;
                        })
                    });

                    setTimeout(function() {
                        _Container.removeChild(_Element);
                    }, 1000);
                };

                this.getPositionClass = function() {
                    console.log(_Options.position)
                    switch(_Options.position)
                    {
                        case "bl":
                            return "js-snackbar-container--bottom-left";
                        case "tl":
                            return "js-snackbar-container--top-left";
                        case "tr":
                            return "js-snackbar-container--top-right";
                        default:
                            return "js-snackbar-container--bottom-right";
                    }
                }

                _ConfigureDefaults();
                _Create();
                _This.Open();
            }
        </script>

        <!-- Script custom -->
        <script type="text/javascript">
            $( document ).ready(function() {
                // On va checker la liste des étapes pour les ajouter dans la sidebar
                var i = 0;
                while($("#slide-"+i).length){
                    $("#sidebar").append('<li id="state-'+i+'" class="stateIndicator"><span>'+$("#slide-"+i).attr("title")+'</span></li>');
                    i++;
                }

                var url = new URL(window.location.href);
                var search_params = url.searchParams;
                if(search_params.get('step')==null){
                    search_params.append('step', '0');
                    var new_url = url.toString();
                    window.history.replaceState({}, '',new_url);
                    showStep(0);
                } else {
                    showStep(search_params.get('step'));
                }
            });

            async function showStep(id){
                id = parseInt(id, 10);
                $("#slide-"+(id)).css("display", "block");

                if(!$("#slide-"+(id-1)).length){
                    $("#prevBtn").css("display", "none");
                }else{
                    $("#prevBtn").css("display", "block");
                }

                if(!$("#slide-"+(id+1)).length){
                    $("#nextBtn").css("display", "none");
                }else{
                    $("#nextBtn").css("display", "block");
                }

                if($("#state-"+(id)).hasClass('passed')){
                    $("#state-"+(id)).removeClass('passed');
                }
                $("#state-"+id).addClass("active");
                for(let k=id-1; k>=0; k--){
                    if($("#state-"+(k)).length){
                        if($("#state-"+(k)).hasClass('active')){
                            $("#state-"+(k)).removeClass('active');
                        }
                        $("#state-"+(k)).addClass('passed');
                    }
                }
                
                if($("#state-"+(id+1)).length){
                    if($("#state-"+(id+1)).hasClass('active')){
                        $("#state-"+(id+1)).removeClass('active');
                    }
                }

                if ($("#slide-"+(id)).attr("title").length) {
                    $("#installStateTitle").html($("#slide-"+(id)).attr("title"));
                }


                // Ici sont répertoriées les différentes actions liées aux pages
                if($("body").attr("operation")=="installation"){
                    if(id==1){
                        $("#nextBtn").css("display", "none");
                        checkRequirements();
                    }else if(id==2){
                        $("#nextBtn").css("display", "none");
                        databaseTest("get");
                    }
                }
            }

            function nextStep(){
                var url = new URL(window.location.href);
                var search_params = url.searchParams;
                id = parseInt(search_params.get('step'), 10);
                $("#slide-"+(id)).css("display", "none");

                var url = new URL(window.location.href);
                var search_params = url.searchParams;
                search_params.set('step', id+1);
                var new_url = url.toString();
                window.history.replaceState({}, '',new_url);

                showStep(id+1);
            }
            function previousStep(){
                var url = new URL(window.location.href);
                var search_params = url.searchParams;
                id = parseInt(search_params.get('step'), 10);
                $("#slide-"+(id)).css("display", "none");

                var url = new URL(window.location.href);
                var search_params = url.searchParams;
                search_params.set('step', id-1);
                var new_url = url.toString();
                window.history.replaceState({}, '',new_url);

                showStep(id-1);
            }
            function isJson(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }
            Object.size = function(obj) {
                var size = 0,
                    key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;
            };
            function checkRequirements(){
                $("#prerequisCheck").html("Récupération des informations.");
                $.get("install.php?checkRequirements", function(data) {
                    if(isJson(data)){
                        var total = 0;
                        $("#prerequisCheck").html("");
                        var requirementsInfos = JSON.parse(data);
                        $("#prerequisCheck").append('<li><strong><span class="text-warning">(Impossible de vérifier)</span> Le serveur web doit fonctionner sous Apache2</strong>, auquel cas vous devrez vous même recréer un équivalent au fichier <code>.htaccess</code>.</li>');
                        if(requirementsInfos.PHP == true){
                            $("#prerequisCheck").append("<li><strong><span class=\"text-success\">PHP ⩾ 7.2</span></strong> (<?=phpversion()?>)</li>");
                            total++;
                        }else{
                            $("#prerequisCheck").append('<li><strong><span class="text-danger">PHP < 7.2</span></strong> (<?=phpversion()?>)</li>');
                        }
                        if(requirementsInfos.PDO == true){
                            $("#prerequisCheck").append('<li><strong class="text-success">PDO est installé</strong></li>');
                            total++;
                        }else{
                            $("#prerequisCheck").append('<li><strong class="text-danger">PDO n\'est pas installé</strong></li>');
                        }
                        if(requirementsInfos.ZIP == true){
                            $("#prerequisCheck").append('<li><strong class="text-success">php-zip est chargé</strong></li>');
                            total++;
                        }else{
                            $("#prerequisCheck").append('<li><strong class="text-danger">php-zip n\'est pas chargé</strong></li>');
                        }
                        if(requirementsInfos.WRITABLE == true){
                            $("#prerequisCheck").append('<li><strong class="text-success">Le dossier est accessible en écriture</strong></li>');
                            total++;
                        }else{
                            $("#prerequisCheck").append('<li><strong class="text-danger">Le dossier n\'est accessible en écriture</strong></li>');
                        }
                        
                        if(total==Object.size(requirementsInfos)){
                            $("#nextBtn").css("display", "block");
                        }
                    } else {
                        console.log("ERREUR - Retour de 'install.php?checkRequirements' illisible (not JSON): "+data);
                        $("#nextBtn").css("display", "none");
                        SnackBar({
                            message: "Echec lors de la vérification, vérifiez la console.",
                            status: "danger",
                            timeout: false
                        });
                    }
                });
            }
            function databaseTest(action){
                if(action == "get"){
                    $.get("install.php?databaseTest="+action, function(data) {
                    if(isJson(data)){
                        var databaseInfo = JSON.parse(data);
                        
                        if(Object.size(databaseInfo)!=0){
                            $("#databaseHost").val(databaseInfo.bddHost);
                            $("#databaseUser").val(databaseInfo.bddUser);
                            $("#databasePass").val(databaseInfo.bddMdp);
                            $("#databaseName").val(databaseInfo.bddName);
                        }
                    } else {
                        console.log("ERREUR - Retour de 'install.php?databaseTest="+action+"' illisible (not JSON): "+data);
                        $("#nextBtn").css("display", "none");
                        SnackBar({
                            message: "Echec lors de la vérification, vérifiez la console.",
                            status: "danger",
                            timeout: false
                        });
                    }
                });
                }else if(action == "send"){
                    $.post( "install.php?databaseTest="+action, $( "#databaseConn" ).serialize() )
                    .done(function( data ) {
                        if(data!="success"){
                            $("#nextBtn").css("display", "none");
                            SnackBar({
                                message: data,
                                status: "danger",
                                timeout: false
                            });
                        } else {
                            $("#nextBtn").css("display", "block");
                            SnackBar({
                                message: 'Connexion réussie.',
                                status: "success"
                            });
                        }
                    });
                }
            }
        </script>
    </body>
</htlm>

<?php } ?>