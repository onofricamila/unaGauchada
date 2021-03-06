<html>

<head>
    <title>Detalle</title>
<?php
session_start();
include_once "header.php";
include_once 'validate.php';
include_once 'gauchadasFx.php';
include_once 'fxComments.php';
include_once 'fxHelp.php';
include_once 'alert.php';
?>
    <div class="row center-block over-box-detail">
         <?php
            if (isset($_SESSION['msg']) && $_SESSION['msg'] != "") {
                hacerAlert($_SESSION['msg']);
                $_SESSION['msg'] = "";
            }
            elseif (isset($_SESSION['success']) && $_SESSION['success'] != "") {
                hacerAlert($_SESSION['success'], 'success');
                $_SESSION['success'] = "";
            }?>
     
        <div class="container-fluid  col-md-6 col-md-offset-3 box-detail">
            <?php
            $idGauchada = $_GET['idGauchadas'];

            if (validate($idGauchada)) {
                $gauchada=getOneGauchada($idGauchada);
                showOneGauchada($gauchada);
            } else {
                $_SESSION['msg'] = "No selecciono ninguna gauchada.";
                header('Location: index.php');
                die;
            }
?>
    </div>
    </div>
    <BR><BR>
    <div class="container col-md-8 col-md-offset-2 comments">
        <legend>Comentarios relacionados</legend>

        <?php
        $link= connect();
        $sql="select idUser from gauchadas where idGauchadas=$idGauchada;";
        $query=$link->query($sql);
        $row=mysqli_fetch_array($query);
        $idUsers=$row['idUser'];

        $comments = getCommentsForGauchada($idGauchada);
        $cant_comments= $comments->num_rows;
        if ($cant_comments > 0) {
            ?>
                <p><span class='badge'><?php echo $cant_comments ?></span>
            <?php
            if ($cant_comments > 1) {
                echo " Comentarios:";
            } else {
                echo " Comentario:";
            }
            ?> 
            </p><br>

            <?php
            $i = 0;
            while ($row = $comments->fetch_assoc()) {
                showComment($row, $idGauchada, $i);
                $i++;
            }
        } else {
            ?>
            <div 
                <?php 
                if (!(isset($_SESSION['idUsers'])) || (isset($_SESSION['idUsers']) and ($_SESSION['idUsers'] == $idUsers))) {
                    ?> 
                    style="margin-bottom: 100px;"
                    <?php
                }
            ?> 
            >No hay comentarios hasta el momento.</div>
            <?php
        }

        if (isset($_SESSION['idUsers']) and ($_SESSION['idUsers'] != $idUsers) && (!($_SESSION['admin'])) && (!(hasAccepted($idGauchada)))) {?>
            <br><br>
            <div>
                <legend>Deja un comentario!</legend>
                <form action="newComment.php" method="post" role='form' class="comment">
                    <div class='form-group'>
                        <textarea class='form-control comment' name='comment' rows='3' placeholder="Ingresa tu comentario aquí" required></textarea>
                    </div>
                    <br>
                    <input type="text" name="idGauchadas" hidden value="<?php echo $_GET['idGauchadas']; ?>">
                    <button type='submit' id="comment" class='btn btn-info col-md-2'><span class="glyphicon glyphicon-comment"></span> Comentar </button>
                </form>
            </div>
            <?php
        }
    ?>

    </div>
    <div id="formReplyComment"> 
        <?php
        // Fix villa para 1 solo comment
        ?>
    </div>
    <div style="float: none; height: 50px;"></div>
    <br><br><br><br>
    <?php include("footer.html");?>
