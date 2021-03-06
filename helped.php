<html>
<head>

    <?php
    session_start();
    include_once 'validate.php';
    include_once 'fxHelp.php';
    include_once 'gauchadasFx.php';
    if (!validateLogin()) {
        $_SESSION['msg'] = "No estas logueado! No podes acceder a ver las gauchadas en las que ayudaste.";
        header('Location: index.php');
        die;
    }
    include_once "header.php"; 
    ?>

    <link rel="stylesheet" href="css/verAyudas.css" type="text/css" media="all" />
    <title>Postulaciones</title>
</head>
<body id="body" style="display:none">
	<?php
    $idUser = $_SESSION['idUsers'];

    $AllHelps = getUserHelp($idUser);		//Todas las gauchadas en las que me postule.
    $all = array();
    $accepted = array();					//Las que me aceptaron pero todavia no calificaron.
    $rejected = array();					//Las que me rechazaron.
    $pending = array();						//Las que no tienen ningun aceptado.
    $calificated = array();					//Las que me aceptaron y calificaron.

    while ($help = $AllHelps->fetch_assoc()) {
        $all[] = $help;
        if ($hasAccepted = hasAccepted($help['idGauchada'])) {
            if ($hasAccepted == $idUser) {
                if ($hasScore = hasScore($help['idGauchada'])) {
                    $calificated[] = $help;
                }
                else {
                    $accepted[] = $help;
                }
            } else {
                $rejected[] = $help;
            }
        } else {
            $pending[] = $help;
        }
    }   
    ?>

    <div class="row">
        <div class="container-fluid  col-md-4 col-md-offset-4 ph">
            <div class="page-header">
                <h3 style="text-align:center;">Mis postulaciones</h3>
            </div>
        </div>
    </div>
    <br>
    <div class="col-md-12">
        <div class="container">
            <div class="row">
                <div class="centered helpedButtons">
                    <button type="button" class="btn btn-default btn-filter" href="#" id="buttonAll">Todas</button>
                    <button type="button" class="btn btn-success btn-filter" href="#" id="buttonAccepted">Aceptadas</button>
                    <button type="button" class="btn btn-warning btn-filter" href="#" id="buttonPending">Pendientes</button>
                    <button type="button" class="btn btn-danger btn-filter" href="#" id="buttonRejected">Rechazadas</button>
                    <button type="button" class="btn btn-info btn-filter" href="#" id="buttonCalificated">Calificadas</button>
                </div>
                <br>
                <br>
                <div id="all" style="display:none"><?php showGauchadas($all); ?></div>
                <div id="accepted" style="display:none"><?php showGauchadas($accepted); ?></div>
                <div id="pending" style="display:none"><?php showGauchadas($pending); ?></div>
                <div id="rejected" style="display:none"><?php showGauchadas($rejected, false); ?></div>
                <div id="calificated" style="display:none"><?php showGauchadas($calificated); ?></div>


            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="js/helped.js"></script>

<?php
function showGauchadas($state, $enabledLink = true) {
        if (sizeof($state) == 0) {
            echo "<br><br><br>";
            hacerAlertV2("No hay gauchadas en esta seccion.");
        
        } else {
        foreach ($state as $i => $value) {
            
            $gauchada = getOneGauchada($state[$i]['idGauchada']);
            $accepted = hasAccepted($gauchada['idGauchadas']);
            $idUser = $_SESSION['idUsers'];
            $link = true;

            if ($accepted) {
                if ($accepted != $idUser) {
                    $link = false;
                }
            }

            showGauchadaForAllPrueba($gauchada, $link, true);
        }
    }
}
?>

<?php
    include_once "footer.html";
?>

</html>