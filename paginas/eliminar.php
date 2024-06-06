<?php
session_start();
include './constantes_funcoes.php';

sessionProtection();
$cargosAceites = array(CLIENTE, FUNCIONARIO, ADMINISTRADOR);
jobProtection($cargosAceites);
$objetivosAceites = array('eliminarUser', 'apagarReserva', 'apagarCompetencia');
objectiveProtection($objetivosAceites);

if ($_GET['obj'] == 'apagarCompetencia') {
    $loc = 'pag_inicial.php';
    try {
        if (!isset($_GET['comp'], $_GET['competencias'], $_GET['username'], $_GET['loc']))
            throw new Exception('Ocorreu um erro na transmissão dos dados, por favor tente outra vez.');


        $loc = $_GET['loc'];
        $user = $_GET['username'];
        if ($loc == "editarDadosUser")
            $loc = "dados_pessoais.php?obj=$loc&username=" . $user;
        else if ($loc == "alterarDadosPessoais")
            $loc = "dados_pessoais.php?obj=$loc";


        $comp = explode(',', $_GET['competencias']);
        $competencias = "";
        foreach ($comp as $i) {
            if (strcmp($i, $_GET["comp"]) != 0) {
                if (strcmp($competencias, "") == 0)
                    $competencias = $i;
                else
                    $competencias = $competencias . "," . $i;
            }
        }

        $categ = explode('.', $_GET['comp']);

        include '../basedados/basedados.h';
        //apaga todas as reservas em nome desse funcionário com a competência que estamos a apagar
        $sql = "DELETE FROM reservas WHERE funcionario= '$user' AND marcacao =" . $categ[0] . " AND animal ='" . $categ[1] . "'
    AND (data_comb > CURRENT_DATE OR (data_comb = CURRENT_DATE AND hora_comb > CURRENT_TIME))";
        verificaConexao($conn, $sql);

        $sql = "UPDATE utilizadores SET competencia = '$competencias' WHERE username ='$user'";
        verificaConexao($conn, $sql);

        if (mysqli_affected_rows($conn) == 0)
            throw new Exception('Nenhum utilizador perdeu a competência, por favor tente outra vez!');
    } catch (Exception $e) {
        echo "<h2>" . $e->getMessage() . "</h2>";
        if (isset($conn))
            mysqli_close($conn);
        header("Refresh:3; url=$loc");
        exit();
    }
    mysqli_close($conn);
    echo "<h2>Competência eliminada com sucesso!</h2>";
    header("Refresh:2; url=$loc");
    exit();
}

if ($_GET['obj'] == 'eliminarUser') {
    $loc = 'gerir_users.php?obj=gerirUsers';
    try {
        if (!isset($_GET['username']))
            throw new Exception('Não definiu o utilizador que pretende eliminar, por favor tente outra vez.');

        $user = $_GET['username'];

        include '../basedados/basedados.h';
        //apagar reservas associadas ao user que queremos eliminar
        $sql = "DELETE FROM reservas WHERE cliente = '$user' OR funcionario = '$user'";
        verificaConexao($conn, $sql);

        //apagar user
        $sql = "DELETE FROM utilizadores WHERE username = '$user'";
        verificaConexao($conn, $sql);
        if (mysqli_affected_rows($conn) == 0)
            throw new Exception('Nenhum utilizador foi expulso, por favor tente outra vez!');

    } catch (Exception $e) {
        echo "<h2>" . $e->getMessage() . "</h2>";
        if (isset($conn))
            mysqli_close($conn);
        header("Refresh:3; url=$loc");
        exit();
    }
    mysqli_close($conn);
    echo "<h2>O utilizador '$user' foi expulso!</h2>";
    header("Refresh:2; url=$loc");
    exit();
}


if ($_GET['obj'] == 'apagarReserva') {
    $loc = 'reservas.php?obj=gerirReservas';
    try {
        if (!isset($_GET['reserva'])) 
            throw new Exception ('Não definiu a reserva que pretende apagar/cancelar, por favor tente outra vez!');
        
        include '../basedados/basedados.h';
        $sql = "DELETE FROM reservas WHERE id=" . $_GET["reserva"];
        verificaConexao($conn, $sql);
        if (mysqli_affected_rows($conn) == 0)
            throw new Exception('Nenhuma reserva foi apagada, por favor tente outra vez!');

    } catch (Exception $e) {
        echo "<h2>" . $e->getMessage() . "</h2>";
        if (isset($conn))
            mysqli_close($conn);
        header("Refresh:3; url=$loc");
        exit();
    }

    mysqli_close($conn);
    echo "<h2>A reserva foi apagada com sucesso!</h2>";
    header("Refresh:2; url=$loc");
}
?>