<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
//Documentos de conexión y header
require 'header.anon.php';
include '../conexion.php';
//
//Variables Alta Cita
$paciente_cita = 0;
$medico_cita = 0;
$hora_cita = 0;
$costo_cita = 0;
$motivo_cita = 0;
//Se hace una consulta al a base de datos para obtener el horario en el que esta atendiendo el medico
$conn=mysqli_connect("localhost","root","","hospital1") or die("Error in connection");
$query = mysqli_query($conn,"SELECT horaentrada,horasalida from usuario");
    while ($result=  mysqli_fetch_array($query)) {
      //Se asigna a las variables respectivas para usarse
      //posteriormente
      $hora_entrada=$result['horaentrada'];
      $hora_salida=$result['horasalida'];
      }
//
//echo "Hora entrada ",$hora_entrada;
//echo "<br>";
//echo "Hora salida ",$hora_salida;
//Verifica si se completó el registro, de ser así, lo informa al usuario mediante un mensaje
if(isset($_GET['status'])){
	$status = $_GET['status'];
	if($status == 1){
		echo "<script>
		$(function(){
			Materialize.toast('La cita ha sido agendada correctamente', 2200, 'rounded')
		});
	</script>";
}else if($status == 0){
	echo "<script>
	$(function(){
		Materialize.toast('Ha ocurrido un error, intente nuevamente', 2200, 'rounded')
	});
</script>";
}else if($status == 2){
	echo "<script>
	$(function(){
		Materialize.toast('No esta en el horario del medico', 2200, 'rounded')
	});
</script>";
}
}
//Se verifica si se hizo click en el boton
if (isset($_POST['submit_cita'])) {

	// Se asigna el valor de las variables al post
	$paciente_cita='';
	if (isset($_POST['paciente_cita'])) {
		$paciente_cita=$_POST['paciente_cita'];
	}
	if (isset($_POST['hora_cita'])) {
		$hora_cita= $_POST['hora_cita'];
	}
	if (isset($_POST['motivo_cita'])) {
		$motivo_cita=$_POST['motivo_cita'];
	}
	if (isset($_POST['fecha_cita'])) {
		$fecha_cita=$_POST['fecha_cita'];
	}
	if (isset($_POST['email'])) {
		$email=$_POST['email'];
	}

//Una vez que se asignaron los valores a las variables de arriba se llama al procedimiento almacenado correspondiente
//Tambine se verifica si el horario no se sale del rango del medico si es asi se procede
if ($hora_cita < $hora_salida and $hora_cita>$hora_entrada) {
//Se llama el query del procedimiento almacenado
if (!$con->query("CALL alta_cita('$paciente_cita','$motivo_cita','$hora_cita','$fecha_cita','$email')")) {
	header("location: agendar_citas1.php?status=0");
	//echo "Falló CALL: (" . $con->errno . ") " . $con->error;
}else{
  //Si ocurre un error se cambia el header para mandar un mensaje al usuario
	header("location: agendar_citas1.php?status=1");
}
}else {
header("location: agendar_citas1.php?status=2");
}
}
?>
<style type="text/css">

	.tabs .tab a{
		color:#1976d2;
	} /*Black color to the text*/

	.tabs .tab a:hover {
		/*background-color:#eee;*/
		color:#0d47a1;
	} /*Text color on hover*/

	.tabs .tab a.active {
		/*background-color:#888;*/
		color:#0d47a1;
	} /*Background and text color when a tab is active*/

	.tabs .indicator {
		background-color:#2196f3;
	} /*Color of underline*/
</style>

<script type="text/javascript">
//Funcion para validar los campos
function validate()
{
    //Se declaran variables
    var paciente_cita = document.forms["RegForm"]["paciente_cita"];
    var hora_cita = document.forms["RegForm"]["hora_cita"];
    var motivo_cita = document.forms["RegForm"]["motivo_cita"];
    var email =  document.forms["RegForm"]["email"];
    var fecha_cita = document.forms["RegForm"]["fecha_cita"];

    //Luego se verifica con un if cada variable
    if (paciente_cita.value == "")
    {
        window.alert("Please enter your name.");
        paciente_cita.focus();
        return false;
    }
    if (hora_cita.value == "")
    {
        window.alert("Please enter the time.");
        hora_cita.focus();
        return false;
    }
    if (motivo_cita.value == "")
    {
        window.alert("Please enter a valid e-mail address.");
        motivo_cita.focus();
        return false;
    }
    if (email.value.indexOf("@", 0) < 0)
    {
        window.alert("Please enter a valid e-mail address.");
        email.focus();
        return false;
    }
    if (email.value.indexOf(".", 0) < 0)
    {
        window.alert("Please enter a valid e-mail address.");
        email.focus();
        return false;
    }
    if (fecha_cita.value == "")
    {
        window.alert("Please enter a valid date.");
        fecha_cita.focus();
        return false;
    }
    return true;
}
</script>
<main>
  <!-- Se muestra la hora de entrada y salida del medico al usuario--->
 <p class="flow-text">hora de entrada: <?php echo $hora_entrada; ?></p>
 <p class="flow-text">hora de salida: <?php echo $hora_salida; ?></p>
	<!-- El form con el metodo post que pasa los valores--->
<form method="post" name="RegForm" onsubmit="return validate()">
	<div class="row">
		<div class="col s12">
			<ul class="tabs blue-text text-darken-4">
				<li class="tab col s6 "><a class="active" href="#cita">Generar Cita</a></li>
			</ul>
		</div>
			<div id="cita" class="col s12">
				<br>
				<div class="row">
					<div class="input-field col s3">
						<label for="costo_cita">Paciente</label>
						<input type="text" name="paciente_cita" id="paciente_cita" placeholder="Ingrese el nombre" maxlength="50">
					</div>
					<div class="input-field col s3">
						<label for="hora_cita">Hora</label>
						<input type="text" id="hora_cita" name="hora_cita" class="timepicker" placeholder="Hora">
					</div>
				</div>
				<div class="row">
					<div class="input-field col s6">
						<input id="motivo_cita" name="motivo_cita" type="text" placeholder="Ingrese el motivo de la cita" maxlength="50"></textarea>
						<label for="motivo_cita">Motivo</label>
					</div>
				</div>
				<div class="row">
					<div class="col s3">
						<label>Fecha</label>
						<input type="text" class="datepicker" name="fecha_cita">
					</div>
					<div class="input-field col s6">
						<input id="email" name="email" type="email" placeholder="sophie@example.com">
						<label for="email">Email</label>
					</div>
				</div>
				<button class="btn waves-effect waves-light blue darken-4 right" type="submit" name="submit_cita">Agendar
					<i class="material-icons right">send</i>
				</button>
			</div>
</div>
</form>
</main>
<?php
//require 'footer.web.php';
?>
