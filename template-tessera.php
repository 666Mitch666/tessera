<?php /* Template Name: Tessera */ ?>

<?php 
get_header();
 ?>


<?php
// get current URL 
$current_url = get_permalink( get_the_ID() );
if( is_category() ) $current_url = get_category_link( get_query_var( 'cat' ) );

// Includi il file di connessione
require_once 'conn-db.php';


// Crea un'istanza della classe per connetterti al database
$connection = new DatabaseConnection('$db_host', '$db_username', '$db_password', 'my_rivatests');
// Stabilisci la connessione
$connection->connect();
// Recupera la connessione
$mysqli = $connection->getConnection();

?>




<div style="text-align:center; margin:auto; width:100%">
<br><br>
<form action="<?php get_permalink( get_the_ID() );?>" method="post" id="CF" name="codice-fiscale">
  <label for="cf">Codice fiscale:</label>
  <br><br>
  <input type="text" name="cf" id="cf" onkeyup="this.value = this.value.toUpperCase()">
  <br><br>
  <input type="submit" value="Invia">
</form>
<br>

</div>

  <?php
  
  // Includi libreria valdiazione CF
require_once 'CodiceFiscale.php';

use NigroSimone\CodiceFiscale;

$cf = new CodiceFiscale();

class cc {

    private $comuneNascita;

    public function getComuneNascita() {
        return $this->comuneNascita;
    }

}


	  
  // Se il form è stato inviato (solo cf)
  if (isset($_POST["cf"])) {
	  
	    $codiceFiscale = sanitize_text_field($_POST["cf"]);
		
		
	  echo '<div style="text-align:center; margin:auto; width:100%">Il codice fiscale inserito è: <span style="font-weight:bold;">' . $codiceFiscale . '</span> ';
	  
if ( $cf->validaCodiceFiscale($codiceFiscale) )
{
    echo 'e risulta <span style="font-weight:bold;">corretto</span><br><br></div>';
	
function controllo_codice_fiscale() { // Inizia funzione per determinare duplicati
  global $wpdb;

  // Controlla se il codice fiscale esiste nella tabella di controllo generale
  $codice_fiscale_esistente_controllo = $wpdb->get_var($wpdb->prepare("SELECT 1 FROM tesserati WHERE CodiceFiscale = %s", $codiceFiscale));

  // Controlla se il codice fiscale esiste nella tabella dell'anno corrente
  $current_year = date('Y');
  $table_name_current_year = generate_table_name($current_year);
  $codice_fiscale_esistente_anno_corrente = $wpdb->get_var($wpdb->prepare("SELECT 1 FROM $table_name_current_year WHERE CodiceFiscale = %s", $codiceFiscale));

  if ($codice_fiscale_esistente_controllo) {
    // Codice fiscale presente nella tabella di controllo generale
    // Reindirizza alla pagina con ID 420
    header('Refresh: 1; url=' . get_permalink( 420 ));
    exit;
  } elseif ($codice_fiscale_esistente_anno_corrente) {
    // Codice fiscale presente nella tabella dell'anno corrente
    // Reindirizza alla pagina con ID 403
    header('Refresh: 1; url=' . get_permalink( 403 ));
    exit;
  } else {
    // Codice fiscale non presente, precompila il form

// ------------------------CREO I DATI DA INSERIRE NELLE VARIABILI DEI CAMPI PRECOMPILATI
// Estrai i dati dal CF
$codiceCatastale = $cf->getComuneNascita();
$sesso = $cf->getSesso();
$giornoNascita  = $cf->getGiornoNascita();
$meseNascita  = $cf->getMeseNascita();
$annoNascita  = $cf->getAnnoNascita();
$datanascita = $giornoNascita.' - '.$meseNascita.' - '.$annoNascita ;

//data di oggi
date_default_timezone_set('Europe/Rome');
$dataoggi = date('d/m/Y');
$ora = date('H:i:s');


// Sanitizzazione del codice catastale
$codiceCatastaleSanitizzato = mysqli_real_escape_string($mysqli, $codiceCatastale);

// Query per recuperare i dati per il comune
$query = "SELECT denominazione_ita, sigla_provincia FROM gi_comuni WHERE codice_belfiore = ?";

// Prepara lo statement
$statement = $mysqli->prepare($query);

// Associa i parametri
$statement->bind_param("s", $codiceCatastaleSanitizzato);

// Esegui la query
$statement->execute();

if (!$statement->execute()) {
  // Errore durante l'esecuzione della query
  echo "Errore durante l'esecuzione della query: " . $mysqli->error . PHP_EOL;
  exit;
}

// Recupera i risultati
$result = $statement->get_result();
	
	// Controlli
if (!$statement) {
  echo "Errore durante l'esecuzione della query: " . mysqli_error($mysqli);
  exit;
}

if ($result->num_rows === 1) {
  // Un record trovato
  $row = $result->fetch_assoc();

  if (isset($row['denominazione_ita']) && isset($row['sigla_provincia'])) {
    // Precompila i campi di nascita
  } else {
    // Mancano informazioni nella riga
    echo "Errore: dati incompleti per nome comune e nome provincia";
  }

}  if ($result->num_rows === 0) {
  // Nessun risultato trovato per il codice catastale
  echo "Errore: manca informazione nel database (da aggiornare) o il codice fiscale inserito è errato";
} 

  
  ?>
<div style="text-align:center; margin:auto; width:100%">
  <h2>Nuovo tesserato</h2>
  <div style="text-align:left; width:40%; min-width:300px; margin:auto; border:4px solid #999; padding:20px; font-family: Arial, Helvetica, sans-serif">

<form action="<?php echo get_permalink(331); ?>" method="post" name="anagrafica" id="anagrafica">
  <input type="hidden" name="form_id" value="anagrafica">
<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'salva_dati_form' ); ?>">
  <br>
  <div style="text-align:center; width:100%; border-bottom:1px solid #F00;">Anagrafica</div>
  <br>
  <br>
  <input type="hidden" name="codice_fiscale" id="codice_fiscale" value="<?php echo $codiceFiscale ;?>">
  <label for="nome" style="color:#666;">Nome:</label>
  <input type="text" name="nome" id="nome" onkeyup="this.value = this.value.toUpperCase()" style="text-align:right; width:100%;" required>
  <br>
  <br>
  <label for="cognome" style="color:#666;">Cognome:</label>
  <input type="text" name="cognome" id="cognome" onkeyup="this.value = this.value.toUpperCase()" style="text-align:right; width:100%;" required>
  <br>
  <br>
<div class="radio-group" id="selezionacontatto">
  <input type="radio" id="email-solo" name="email-solo" value="email-solo">
  <label for="email-solo">Solo email</label>
  <input type="radio" id="cellulare-solo" name="cellulare-solo" value="cellulare-solo">
  <label for="cellulare-solo">Solo cellulare</label>
  <input type="radio" id="entrambi" name="entrambi" value="entrambi">
  <label for="entrambi">Entrambi</label>
</div>
<div id="errorMessage" style="text-align:center; color: #F00; margin:auto;"></div>
  <br>
  <br>
   <label for="cel" style="color:#666; display: none;">Contatto telefonico:</label>
  <input type="tel" name="cel" id="cel" onkeyup="this.value = this.value.toUpperCase()" style="text-align:right; width:100%; display: none;">
   <label for="email" style="color:#666; display: none;">Indirizzo email:</label>
  <input type="email" name="email" id="email" style="text-align:right; width:100%; display: none;">
  <br>
  <br>
    <label for="datatessera" style="color:#666;">Data di tesseramento:</label>
  <input type="text" name="datatessera" id="datatessera" onkeyup="this.value = this.value.toUpperCase()" style="text-align:right; width:100%;"  value="<?php echo $dataoggi ;?>"  >
  <br>
  <br>
  <span style="color:#666;">Sono le ore:</span> <?php echo $ora; ?> 
  <input type="hidden" name="ora" id="ora" value="<?php echo $ora; ?> ">
  <br>
  <br>
  <label for="datanascita" style="color:#666;">Data di nascita:</label>
  <input type="text" name="datanascita" id="datanascita"  value="<?php echo $datanascita ;?>" style="text-align:right; width:100%;">
  <br>
  <br>
  <label for="sesso" style="color:#666;">Sesso biologico di nascita:</label>
  <input type="text" name="sesso"  id="sesso"  value="<?php echo $sesso ;?>" onkeyup="this.value = this.value.toUpperCase()" style="text-align:right; width:100%;" pattern="[MF]">
  <br>
  <br>
  <label for="comune" style="color:#666;">Comune di nascita:</label>
  <input type="text" name="comune"  id="comune"   value="<?php echo $row["denominazione_ita"]; ?>" style="text-align:right; width:100%;"> 
  <br>
  <br>
  <span style="color:#666;">Provincia:</span> <?php echo $row["sigla_provincia"]; ?> 
    <input type="hidden" name="provincia" id="provincia" value="<?php echo $row["sigla_provincia"]; ?> ">

  <br>
  <br>
  <input type="submit" value="Salva" id="salva" style="text-align:center">
</form>
</div>
</div>

<script>
//-------------------obbligo maiuscolo per entrambi i form
const formCF = document.querySelector('#CF');
const formAnagrafica = document.querySelector('#anagrafica');

// Aggiungi l'event listener a entrambi i form
formCF.addEventListener('submit', convertiMaiuscolo);
formAnagrafica.addEventListener('submit', convertiMaiuscolo);

function convertiMaiuscolo(event) {
  const form = event.currentTarget; // Ottieni il form che ha scatenato l'evento
  const inputs = form.querySelectorAll('input[type="text"], input[type="email"]');

  for (const input of inputs) {
	  
    if (input.type === 'email') {
      input.value = input.value.toLowerCase(); // Converti in minuscolo il valore di ogni input email
    } else {
    input.value = input.value.toUpperCase();
  }
}
}
//--------------------------------------anagrafica-----------------
	  
	  
    // Selezione radio button
const radioGruppo = document.querySelector('.radio-group');
const emailInput = document.getElementById('email');
const celInput = document.getElementById('cel');
const emailLabel = document.querySelector('label[for="email"]');
const celLabel = document.querySelector('label[for="cel"]');
	
radioGruppo.addEventListener('change', (evento) => {
  const selectedValue = evento.target.value;

  // Selezione radio button
  const emailRadio = document.getElementById('email-solo');
  const celRadio = document.getElementById('cellulare-solo');
  const entrambiRadio = document.getElementById('entrambi');
  if (selectedValue === 'email-solo') {
    emailRadio.checked = true;
    celRadio.checked = false;
    entrambiRadio.checked = false;
  } else if (selectedValue === 'cellulare-solo') {
    emailRadio.checked = false;
    celRadio.checked = true;
    entrambiRadio.checked = false;
  } else {
    emailRadio.checked = false;
    celRadio.checked = false;
    entrambiRadio.checked = true;
  }

  // Gestione visibilità
  if (selectedValue === 'email-solo') {
    emailLabel.style.display = 'block';
    celLabel.style.display = 'none';
    emailInput.style.display = 'block';
    celInput.style.display = 'none';
  } else if (selectedValue === 'cellulare-solo') {
    emailLabel.style.display = 'none';
    celLabel.style.display = 'block';
    emailInput.style.display = 'none';
    celInput.style.display = 'block';
  } else {
    emailLabel.style.display = 'block';
    celLabel.style.display = 'block';
    emailInput.style.display = 'block';
    celInput.style.display = 'block';
  }
});

// Controllo selezione obbligatoria all'invio del modulo
const modulo = document.querySelector('#anagrafica'); // Ottieni l'elemento del modulo
modulo.addEventListener('submit', (evento) => {
  // Controllo compilazione
  if (emailInput.value.trim() === '' && celInput.value.trim() === '') {
    // Entrambi i campi sono vuoti, mostra il messaggio di errore
    errorMessage.textContent = 'Compila almeno un campo di contatto: telefono o email.';
    errorMessage.style.display = 'block';
    // Impedisci l'invio del form (se c'è un messaggio di errore)
    evento.preventDefault();
  } else {
    // Almeno un campo è compilato, rimuovi il messaggio di errore
    errorMessage.textContent = '';
    errorMessage.style.display = 'none';
  }
});
</script>




<?php

  }//fine se il CF non è già presente
}//fine funzione di controllo CF duplicato


} //fine if "è valido il cod fisc?"
else
{ 
    echo 'ma sembra che <span style="font-weight:bold;color:red;">NON SIA CORRETTO</span></div><br>';
}

  };?>

<?php //listare gli ultimi 10 codici fiscali inseriti ?>
<?php
/* Start the Loop */
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/content-page' );

	// If comments are open or there is at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile; // End of the loop.

get_footer();
 ?>
