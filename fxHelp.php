<?php
include_once  'validate.php';
include_once 'gauchadasFx.php';
include_once 'usersFx.php';
include_once 'credits.php';
include_once 'gauchadasFx.php';
include_once 'fxScore.php';

function newHelp($idUser, $idGauchada, $description = "")
{
    if (validateUser($idUser) && validateGauchada($idGauchada)) {
        $link = connect();
        $query = "INSERT INTO help (idUsers, idGauchada, description)";
        $query = $query."VALUES ($idUser, $idGauchada, '$description')";
        if ($result = $link->query($query)) {
            return $result;
        }
        $_SESSION['msg'] = $link->error;
    }
    return false;
}

function getHelps($idGauchada)
{
    if (validateGauchada($idGauchada)) {
        $link = connect();
        $query = "SELECT * FROM help WHERE idGauchada = $idGauchada;";
        $result = $link->query($query);
        return $result;
    }
    return false;
}

function getOneHelp($idGauchada, $idUser)
{
    if (validateGauchada($idGauchada) && validateUser($idUser)) {
        $link = connect();
        $query = "SELECT * FROM help WHERE idGauchada = $idGauchada AND idUsers = $idUser;";
        $result = $link->query($query);
        return $result;
    }
    return false;
}

function deleteHelp($idGauchada, $idUser)
{
    if (validateGauchada($idGauchada) && validateUser($idUser)) {
        $link = connect();
        $query = "DELETE FROM help WHERE idUsers=$idUser AND idGauchada=$idGauchada";
        $result = $link->query($query);
        return $result;
    }
    return false;
}

function acceptHelp($idGauchada, $idUser)
{
    if (validateGauchada($idGauchada) && validateUser($idUser)) {
        $link = connect();
        $query = "UPDATE help SET selected=1 WHERE idUsers=$idUser AND idGauchada=$idGauchada";
        if ($link->query($query)) {
            finishGauchada($idGauchada);
            return true;
        }
    }
    $_SESSION['msg'] = $link->error;
    return false;
}

function hasAccepted($idGauchada){
    $link = connect();
    if (validateGauchada($idGauchada)) {
        $query = "SELECT * FROM help WHERE idGauchada = $idGauchada AND selected=1;";
        $result = $link->query($query);
        if ($result && $result->num_rows > 0) {
            $result = $result->fetch_assoc();
            return $result['idUsers'];
        }
    }
    $_SESSION['msg'] = $link->error;
    return false;
}

function listHelps($idGauchada)
{
    $accepted = hasAccepted($idGauchada);
    if ($ayudas = getHelps($idGauchada)) {
            $cant_ayudas= $ayudas->num_rows; ?>
            <div class="row">
            <?php 
            if ($cant_ayudas > 0) {
            ?>
                <legend><span class='badge'><?php echo $cant_ayudas ?></span>
            <?php
            if ($cant_ayudas > 1) {
                echo " Postulantes:";
            } else {
                echo " Postulante:";
            }?>
            </legend>
            <?php 
            } else {
                 echo "No hay postulantes hasta el momento.";
            }
            ?> 
            <?php
            while ($help = $ayudas->fetch_assoc()) {
                $gauchada = getOneGauchada($help['idGauchada']);
                $user = getUser($help['idUsers'])->fetch_assoc();
                ?>
                <div class="row">
                    <div class="col-md-4">
                        <p>
                            <?php echo $user['name']." ".$user['surname']; ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p>
                            <?php if($help['description']) echo $help['description']; else echo "---";?>
                        </p>
                    </div>
                  
                        <?php
                        if (!$accepted) {
                            ?>
                            <form action="acceptHelp.php" method="post" class="col-md-4">
                                <input type="text" name="idUsers" value="<?php echo $help['idUsers'] ?>" hidden>
                                <input type="text" name="idGauchadas" value="<?php echo $help['idGauchada'] ?>" hidden>
                                <button type="submit" class="btn btn-warning" style="<?php if(!($help['description'])) ?> margin-bottom: 20px;"><span class="glyphicon glyphicon-ok-circle"></span> Aceptar Ayuda</button>
                            </form>
                            <?php
                        }
                        elseif ($help['idUsers'] == $accepted) {
                            if (hasScore($help['idGauchada'])) {
                                $score = getScoreForGauchada($idGauchada)->fetch_assoc();
                                switch ($score['points']) {
                                    case -2:
                                        echo "Puntuado negativamente.";
                                        break;
                                    case 0:
                                        echo "Puntuado neutro.";
                                        break;
                                    case 1:
                                        echo "Puntuado positivamente.";
                                        break;
                                }
                                //echo "Descripcion: ".$score['description'];
                            }
                            else {
                                ?>

                            <div>
                                <a href="#" class="btn btn-warning" onclick="style.display = 'none'; formPuntuar.style.display = 'block'">Puntuar</a>
                            </div>
                            <div class="col-md-12">
                            <br>
                            <form method="post" action="score.php" style="display:none" id="formPuntuar">
                                    <div class="form-group col-md-6"> 
                                        <input type="text" name="idGauchadas" hidden value="<?php echo $gauchada['idGauchadas']; ?>">
                                        <input type="text" name="idUser" hidden value="<?php echo $help['idUsers']; ?>">
                                        <textarea style="width:100%;"class="form-control" rows="3" name="description" placeholder="Envía una descripción opcional de la participación de <?php echo $user['name']." ".$user['surname']; ?>"></textarea>
                                    </div>
                                    <div class="form-group col-md-3"> 
                                        <input type="radio" name="score" value="0">&nbsp;&nbsp;&nbsp; <span style="color: red" class="glyphicon glyphicon-thumbs-down"></span> <br>
                                        <input type="radio" name="score" value="1" checked> <span style="color: orange" class="glyphicon glyphicon-thumbs-up"></span><span style="color: orange" class="glyphicon glyphicon-thumbs-down"></span><br>
                                        <input type="radio" name="score" value="2">&nbsp;&nbsp;&nbsp; <span style="color: green" class="glyphicon glyphicon-thumbs-up"></span>
                                    </div>                                 
                                    <button type="submit" name="submit" class="btn btn-warning ir col-md-3"> Calificar a <?php echo $user['name'] ?></button>
                            </form>
                            </div>


                            <!--    <form method="post" action="score.php">
                                    <input type="number" name="idGauchadas" value=<?php echo '"'.$idGauchada.'"' ?> hidden>
                                    <input type="text" name="description" value="">
                                    <select name="score">
                                        <option value="0">Negativo</option>
                                        <option value="1">Neutro</option>
                                        <option value="2">Positivo</option>
                                    </select>
                                    <input type="submit" name="submit" value="submit">
                                </form> -->
                                <?php
                            }
                        }
                        else {?>
                           <div class="col-md-4">
                            <p>Rechazado/a</p>
                           </div>
                        <?php 
                        }
                        ?>
                </div>
            <?php
            }
        ?>
        </div>
        <?php
    }
}

function getUserHelp($idUser){
    if (validateUser($idUser)) {
        $link = connect();
        $query = "SELECT * FROM help WHERE idUsers = $idUser;";
        $result = $link->query($query);
        return $result;
    }
    return false;
}




?><!--

<div class="col-md-12">
                            <form action="newHelp.php" method="post" style="display:none" id="formPuntuar">
                                    <div class="form-group col-md-6"> 
                                        <input type="text" name="idGauchadas" hidden value="<?php //echo $gauchada['idGauchadas']; ?>">
                                        <textarea style="width:100%;"class="form-control" name="description" placeholder="Envía una descripción opcional de la participación de <?php echo $user['name']." ".$user['surname']; ?>"></textarea>
                                    </div>
                                    <div class="form-group col-md-3"> 
                                        
                                    </div>                                   
                                    <button type="submit" class="btn btn-warning ir col-md-3"> Calificar a <?php //echo $user['name'] ?></button>
                            </form>
                            </div>
 -->