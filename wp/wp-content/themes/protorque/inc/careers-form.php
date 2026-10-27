<?php
/**
 * Careers application forms: Canada, USA (English), USA (Español).
 * Rebuilt from the live ptenergy.com Elementor forms (exported Oct 27, 2026) so the field set, required fields and
 * email routing match. Rendered and handled by the theme, no form plugin. Submissions are emailed and also stored as a
 * private "Application" post so nothing is lost if mail fails. Spam control: honeypot, a minimum fill time, and the
 * required-field count the live form already relies on.
 */

/** Recipients. Filter 'pt_careers_recipients' to change without editing the theme. */
function pt_careers_recipients( $region, $midstream ) {
	$r = [
		'ca'      => [ 'to' => [ 'info@machinemedia.ca' ], 'cc' => [] ],  // live Canada form posts to the site admin address (info@machinemedia.ca)
		'us'      => [ 'to' => [ 'PTE.Careers@ptenergy.com' ], 'cc' => [ 'ptenergycompanies@gmail.com' ] ],
		'us-mid'  => [ 'to' => [ 'PTEM.Careers@ptenergy.com' ], 'cc' => [ 'ptenergycompanies@gmail.com' ] ],
	];
	$key = $region === 'ca' ? 'ca' : ( $midstream ? 'us-mid' : 'us' );
	return apply_filters( 'pt_careers_recipients', $r[ $key ], $region, $midstream );
}

/**
 * Field spec. Each row: [ type, label, required, options ] or [ 'h', heading ] / [ 'p', text ].
 * Types: text, tel, email, number, date, textarea, radio, checkbox, select.
 */
function pt_careers_spec( $lang = 'en' ) {
	$yn = $lang === 'es' ? [ 'Sí', 'No' ] : [ 'Yes', 'No' ];
	$emp = function ( $req, $lang ) {
		$l = $lang === 'es' ? [ 'Fechas de empleo', 'Teléfono de la empresa', 'De (mes/año)', 'A (mes/año)', 'Dirección', 'Nombre del supervisor', 'Número de teléfono del supervisor', 'Título profesional', 'Sueldo/Salario Inicio', 'Sueldo/Salario Fin', 'Trabajo realizado', 'Motivo de salida' ]
		                   : [ 'Company Name', 'Company Phone', 'Employed From', 'Employed To', 'Work Address', 'Supervisor Name', 'Supervisor Phone', 'Job Title', 'Starting Wage/Salary', 'Ending Wage/Salary', 'Work Performed', 'Reason for Leaving' ];
		$t = [ 'text', 'tel', 'month', 'month', 'text', 'text', 'tel', 'text', 'text', 'text', 'textarea', 'text' ];
		$out = [];
		foreach ( $l as $i => $label ) {
			$out[] = [ $t[ $i ], $label, $req && $i !== 3 ? 1 : 0, null, in_array( $i, [ 4, 7, 10, 11 ], true ) ? 'wide' : '' ]; // Employed To optional (USA live form), current job
		}
		return $out;
	};
	$edu = function ( $req, $lang ) {
		$l = $lang === 'es' ? [ 'Nombre de la escuela a la que asistió', 'Curso de estudio', 'Años completados', 'Diploma/título' ] : [ 'School Name', 'Course of Study', 'Years Completed', 'Diploma/Degree' ];
		return [ [ 'text', $l[0], $req, null, 'half' ], [ 'text', $l[1], 0, null, 'half' ], [ 'text', $l[2], $req, null, 'half' ], [ 'text', $l[3], $req, null, 'half' ] ];
	};
	$ref = function ( $req, $lang ) {
		$l = $lang === 'es' ? [ 'Nombre', 'Número de teléfono', '¿Dónde él o ella supervisó su trabajo?' ] : [ 'Name', 'Phone Number', 'Where did he/she supervise your work?' ];
		return [ [ 'text', $l[0], $req, null, 'half' ], [ 'tel', $l[1], $req, null, 'half' ], [ 'text', $l[2], $req, null, 'wide' ] ];
	};

	if ( $lang === 'es' ) {
		return array_merge( [
			[ 'select', 'Puesto (s) Solicitado (s)', 1, 'positions', 'half' ],
			[ 'radio', '¿Estás postulando a la División Midstream?', 1, [ 'No', 'Sí' ], 'half', 'midstream' ],
			[ 'checkbox', '¿Cómo supo de nosotros?', 0, [ 'Publicidad', 'Agencia de empleo', 'A través de un amigo', 'A través de un familiar', 'Walk-In', 'Otro' ], 'wide' ],
			[ 'h', 'Información Personal' ],
			[ 'text', 'Apellido', 1, null, 'half' ], [ 'text', 'Nombre', 1, null, 'half' ], [ 'text', '2nd Nombre (Inicial)', 0, null, 'third' ],
			[ 'text', 'Calle', 1, null, 'wide' ], [ 'text', 'Ciudad', 1, null, 'half' ], [ 'text', 'Estado', 1, null, 'third' ], [ 'text', 'País', 1, null, 'third' ], [ 'text', 'Código Postal', 1, null, 'third' ],
			[ 'p', '¿Cuánto tiempo ha vivido en este domicilio?' ],
			[ 'number', 'Años', 1, null, 'third' ], [ 'number', 'Meses', 1, null, 'third' ],
			[ 'h', 'Números de teléfono' ],
			[ 'tel', 'Residencial', 1, null, 'third' ], [ 'tel', 'Trabajo', 0, null, 'third' ], [ 'tel', 'Celular', 0, null, 'third' ],
			[ 'radio', '¿Podemos contactarlo a su trabajo?', 1, $yn, 'half' ], [ 'email', 'Correo Electrónico', 1, null, 'half' ],
			[ 'radio', '¿Tiene amigos o familiares que trabajen en ProTorque?', 1, $yn, 'half' ], [ 'text', 'Si la respuesta es “Sí”, por favor incluya el nombre de la persona y su relación con esa persona', 0, null, 'half' ],
			[ 'radio', '¿Alguna vez ha llenado una aplicación para ProTorque?', 1, $yn, 'half' ], [ 'text', 'Si la respuesta es “Sí” por favor anote con cuál operación y la fecha aproximada.', 0, null, 'half' ],
			[ 'radio', 'Debido a la naturaleza del negocio de ProTorque, a veces se nos exige completar una prueba de drogas previa al acceso. ¿Está dispuesto a realizar y aprobar una prueba de drogas previa al empleo o al acceso?', 1, $yn, 'wide' ],
			[ 'radio', '¿Está dispuesto a pasar largos periodos de tiempo fuera de casa?', 1, $yn, 'half' ],
			[ 'radio', '¿Puede levantar hasta 50 libras durante el día?', 1, $yn, 'half' ],
			[ 'radio', '¿Está trabajando actualmente?', 1, $yn, 'third' ], [ 'text', '¿Sí la respuesta es sí, dónde?', 0, null, 'third' ], [ 'radio', 'En caso afirmativo, ¿podemos comunicarnos con su empleador actual?', 0, $yn, 'third' ],
			[ 'date', '¿En qué fecha podría empezar a trabajar?', 1, null, 'half' ],
			[ 'radio', '¿Tiene un medio de transporte confiable hacia y desde el trabajo?', 1, $yn, 'half' ],
			[ 'radio', '¿Tiene una licencia de conducir válida de Texas? ¿CDL?', 1, $yn, 'half' ],
			[ 'radio', '¿Alguna vez le han suspendido la licencia? Una licencia suspendida no lo hace automáticamente inelegible para trabajar en ProTorque.', 1, $yn, 'half' ],
			[ 'textarea', 'En caso afirmativo explicar:', 0, null, 'wide' ],
			[ 'radio', '¿Ha tenido algún accidente automovilístico en los últimos 3 años?', 1, $yn, 'half' ], [ 'number', 'Si es así, ¿cuántos?', 0, null, 'third' ],
			[ 'radio', '¿Ha sido condenado por un delito grave en los últimos siete (7) años? En caso afirmativo, explique en la entrevista. (Indicar que sí no lo eliminará automática ni necesariamente de la consideración para el empleo)', 1, $yn, 'wide' ],
			[ 'h', 'Educación' ], [ 'p', 'Escuela secundaria' ] ], $edu( 1, 'es' ), [ [ 'p', 'Licenciatura, Universidad, Negocios / Comercio / Escuela Profesional' ] ], $edu( 0, 'es' ), [ [ 'p', 'Graduado / Profesional' ] ], $edu( 0, 'es' ), [ [ 'p', 'Otra (especificar)' ] ], $edu( 0, 'es' ), [
			[ 'h', 'Información Adicional' ],
			[ 'textarea', 'Resuma las habilidades y cualificaciones especiales relacionadas con el trabajo que considere que pueden ser útiles para considerar su solicitud.', 0, null, 'wide' ],
			[ 'file', 'Currículum (opcional, PDF o Word)', 0, null, 'wide' ],
			[ 'h', 'Experiencia Laboral' ],
			[ 'p', 'Comience con su trabajo actual o más reciente. Incluya cualquier asignación de servicio militar o actividad voluntaria relacionada con el trabajo. Por favor NO indique "Ver currículum". La solicitud debe completarse en su totalidad. Las solicitudes incompletas no serán consideradas para el empleo.' ],
			[ 'p', 'Empleador más reciente' ] ], $emp( 1, 'es' ), [ [ 'p', 'Empleador anterior' ] ], $emp( 0, 'es' ), [ [ 'p', 'Empleador anterior' ] ], $emp( 0, 'es' ), [ [ 'p', 'Empleador anterior' ] ], $emp( 0, 'es' ), [ [ 'p', 'Empleador anterior' ] ], $emp( 0, 'es' ), [
			[ 'h', 'Referencias Profesionales' ],
			[ 'p', 'Enumere un mínimo de tres referencias profesionales de personas a quienes informó y que no estén relacionadas con usted.' ],
			[ 'p', 'Referencia 1' ] ], $ref( 1, 'es' ), [ [ 'p', 'Referencia 2' ] ], $ref( 1, 'es' ), [ [ 'p', 'Referencia 3' ] ], $ref( 0, 'es' ) );
	}

	$us = $lang === 'us';
	return array_merge( [
		[ 'select', 'Position Applied For', 1, 'positions', 'half' ] ],
		$us ? [ [ 'radio', 'Applying for Midstream Division?', 1, [ 'No', 'Yes' ], 'half', 'midstream' ] ] : [],
		[ [ 'checkbox', 'How did you learn about us?', 0, [ 'Advertisement', 'Employment Agency', 'Friend', 'Relative', 'Walk-In', 'Other' ], 'wide' ],
		[ 'h', 'Personal Information' ],
		[ 'text', 'Last Name', 1, null, 'half' ], [ 'text', 'First Name', 1, null, 'half' ], [ 'text', 'Middle Initial', 0, null, 'third' ],
		[ 'text', 'Street Address', 1, null, 'wide' ], [ 'text', 'City', 1, null, 'half' ],
		[ 'text', $us ? 'State' : 'Province', 1, null, 'third' ], [ 'text', 'Country', 1, null, 'third' ], [ 'text', $us ? 'Zip Code' : 'Postal Code', 1, null, 'third' ],
		[ 'p', 'How long have you lived at this address?' ],
		[ 'number', 'Years', 1, null, 'third' ], [ 'number', 'Months', 1, null, 'third' ],
		[ 'h', 'Telephone Numbers' ],
		[ 'tel', 'Home', 1, null, 'third' ], [ 'tel', 'Work', 0, null, 'third' ], [ 'tel', 'Cellphone', 0, null, 'third' ],
		[ 'radio', 'May we contact you at work?', 1, $yn, 'half' ], [ 'email', 'Email Address', 1, null, 'half' ],
		[ 'radio', 'Do you have any friends or relatives that work at ProTorque?', 1, $yn, 'half' ], [ 'text', 'If yes, please state their name(s) and relationship to you', 0, null, 'half' ],
		[ 'radio', 'Have you ever filed an Application with ProTorque before?', 1, $yn, 'half' ], [ 'text', 'If yes, please note with which Operation and approximate date', 0, null, 'half' ],
		[ 'radio', 'Due to the nature of ProTorque business, we are sometimes required to complete a pre-access drug test. Are you willing to take and pass a pre-employment/pre-access drug test?', 1, $yn, 'wide' ],
		[ 'radio', 'Are you willing to spend extended periods of time away from home?', 1, $yn, 'half' ],
		[ 'radio', 'Are you able to lift up to 50 lbs throughout the day?', 1, $yn, 'half' ],
		[ 'radio', 'Are you currently employed?', 1, $yn, 'third' ], [ 'text', 'If yes, where are you currently employed?', 0, null, 'third' ], [ 'radio', 'If yes, may we contact your current employer?', 0, $yn, 'third' ],
		[ 'date', 'On what date would you be able to begin work?', 1, null, 'half' ],
		[ 'radio', 'Do you have a dependable means of transportation to and from work?', 1, $yn, 'half' ] ],
		$us ? [ [ 'radio', 'Do you have a valid Texas driver’s license? CDL?', 1, $yn, 'half' ] ]
		    : [ [ 'radio', 'Do you have a valid driver’s license?', 1, $yn, 'half' ], [ 'text', 'What class is your driver’s license?', 0, null, 'third' ] ],
		[ [ 'radio', 'Has your license ever been suspended? A suspended license does not automatically make you ineligible for employment with ProTorque.', 1, $yn, 'half' ],
		[ 'textarea', 'If your license has ever been suspended, please explain:', 0, null, 'wide' ],
		[ 'radio', 'Have you had any motor vehicle accidents in the past 3 years?', 1, $yn, 'half' ], [ 'number', 'If yes, how many?', 0, null, 'third' ],
		[ 'radio', 'Have you been convicted of a felony within the past seven (7) years? If yes, explain at interview. (Indicating yes will not automatically or necessarily eliminate you from consideration for employment)', 1, $yn, 'wide' ],
		[ 'h', 'Education' ], [ 'p', 'High School' ] ], $edu( 1, 'en' ), [ [ 'p', 'Undergraduate College, Business / Trade / Professional School' ] ], $edu( 0, 'en' ), [ [ 'p', 'Graduate / Professional' ] ], $edu( 0, 'en' ), [ [ 'p', 'Other (Please Specify)' ] ], $edu( 0, 'en' ), [
		[ 'h', 'Additional Information' ],
		[ 'textarea', 'Summarize special job related skills and qualifications you feel may be helpful to us in considering your application.', 0, null, 'wide' ],
		[ 'file', 'Resume (optional, PDF or Word)', 0, null, 'wide' ],
		[ 'h', 'Employment Experience' ],
		[ 'p', 'Please begin with your present or most recent job. Include any job-related military service assignments or volunteer activities. Please DO NOT state “See Resume”. The Application must be completed in its entirety. Incomplete Applications will not be considered for employment.' ],
		[ 'p', 'Most Recent Employer' ] ], $emp( 1, 'en' ), [ [ 'p', 'Previous Employer' ] ], $emp( 0, 'en' ), [ [ 'p', 'Previous Employer' ] ], $emp( 0, 'en' ), [ [ 'p', 'Previous Employer' ] ], $emp( 0, 'en' ), [ [ 'p', 'Previous Employer' ] ], $emp( 0, 'en' ), [
		[ 'h', 'Professional References' ],
		[ 'p', 'Please list a minimum of three professional references of individuals to whom you reported and who are not related to you.' ],
		[ 'p', 'Reference 1' ] ], $ref( 1, 'en' ), [ [ 'p', 'Reference 2' ] ], $ref( 1, 'en' ), [ [ 'p', 'Reference 3' ] ], $ref( 0, 'en' ) );
}

function pt_careers_forms() {
	return [
		'ca' => [ 'lang' => 'en', 'tab' => 'Canada', 'title' => 'Application for Employment: Canada', 'note' => 'Fields marked * are mandatory. Do not apply if you are outside Canada or the USA.', 'submit' => 'Submit Application', 'success' => 'Your application was received. Thank you.', 'general' => 'General application' ],
		'us' => [ 'lang' => 'us', 'tab' => 'USA', 'title' => 'Application for Employment: USA', 'note' => 'Fields marked * are mandatory. Do not apply if you are outside Canada or the USA.', 'submit' => 'Submit Application', 'success' => 'Your application was received. Thank you.', 'general' => 'General application' ],
		'es' => [ 'lang' => 'es', 'tab' => 'USA (Español)', 'title' => 'Solicitud de Empleo: USA', 'note' => 'Los campos marcados con * son obligatorios. No presente su solicitud si se encuentra fuera de Canadá o los Estados Unidos.', 'submit' => 'Mande su solicitud', 'success' => 'Su solicitud fue recibida. Gracias.', 'general' => 'Solicitud general' ],
	];
}

/** Open positions for the dropdown. */
function pt_careers_positions() {
	$jobs = get_posts( [ 'post_type' => 'pt_job', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );
	return array_map( function ( $j ) { return get_the_title( $j ); }, $jobs );
}

function pt_careers_field_name( $i ) {
	return 'f' . $i;
}

/** Render the three forms with tabs. */
function pt_careers_render() {
	$forms = pt_careers_forms();
	$positions = pt_careers_positions();
	$status = isset( $_GET['applied'] ) ? sanitize_key( $_GET['applied'] ) : '';
	$error  = get_transient( 'pt_careers_err_' . pt_careers_client_key() );
	if ( $error ) {
		delete_transient( 'pt_careers_err_' . pt_careers_client_key() );
	}
	$active = isset( $_GET['region'] ) && isset( $forms[ $_GET['region'] ] ) ? sanitize_key( $_GET['region'] ) : 'ca';
	?>
	<section class="pt-apply pt-section--charcoal" id="apply">
		<div class="pt-container">
			<div class="pt-center">
				<p class="pt-eyebrow pt-eyebrow--yellow">Join the ProTorque Team</p>
				<h2 class="pt-h2">Apply to ProTorque</h2>
				<p class="pt-lede">Progressive? Eager? Driven? We want to hear from you. Choose your region to open the application.</p>
			</div>
			<div class="pt-apply__tabs" role="tablist" aria-label="Application region">
				<?php foreach ( $forms as $key => $f ) : ?>
				<button class="pt-apply__tab<?php echo $key === $active ? ' is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $key === $active ? 'true' : 'false'; ?>" aria-controls="apply-<?php echo esc_attr( $key ); ?>" id="tab-<?php echo esc_attr( $key ); ?>" data-region="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $f['tab'] ); ?></button>
				<?php endforeach; ?>
			</div>
			<?php if ( $status === 'ok' ) : ?>
			<div class="pt-apply__msg pt-apply__msg--ok" role="status"><?php echo esc_html( $forms[ $active ]['success'] ); ?></div>
			<?php elseif ( $error ) : ?>
			<div class="pt-apply__msg pt-apply__msg--err" role="alert"><?php echo esc_html( $error ); ?></div>
			<?php endif; ?>
			<?php foreach ( $forms as $key => $f ) :
				$spec = pt_careers_spec( $f['lang'] );
				?>
			<form class="pt-apply__form<?php echo $key === $active ? ' is-active' : ''; ?>" id="apply-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-<?php echo esc_attr( $key ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" novalidate<?php echo $key === $active ? '' : ' hidden'; ?>>
				<input type="hidden" name="action" value="pt_careers_apply">
				<input type="hidden" name="region" value="<?php echo esc_attr( $key ); ?>">
				<input type="hidden" name="ts" value="<?php echo esc_attr( time() ); ?>">
				<?php wp_nonce_field( 'pt_careers_' . $key, 'pt_nonce' ); ?>
				<p class="pt-apply__hp" aria-hidden="true"><label>Leave this field empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
				<h3 class="pt-apply__title"><?php echo esc_html( $f['title'] ); ?></h3>
				<p class="pt-apply__note"><?php echo esc_html( $f['note'] ); ?></p>
				<div class="pt-apply__grid">
				<?php foreach ( $spec as $i => $row ) :
					$type = $row[0];
					if ( $type === 'h' ) { echo '<h4 class="pt-apply__h wide">' . esc_html( $row[1] ) . '</h4>'; continue; }
					if ( $type === 'p' ) { echo '<p class="pt-apply__p wide">' . esc_html( $row[1] ) . '</p>'; continue; }
					$name = pt_careers_field_name( $i );
					$id   = 'ap-' . $key . '-' . $i;
					$req  = ! empty( $row[2] );
					$w    = $row[4] ?? 'half';
					$label = esc_html( $row[1] ) . ( $req ? ' <span class="pt-apply__req">*</span>' : '' );
					echo '<div class="pt-apply__field ' . esc_attr( $w ) . ( $type === 'radio' || $type === 'checkbox' ? ' pt-apply__field--choice' : '' ) . '">';
					if ( $type === 'radio' || $type === 'checkbox' ) {
						echo '<fieldset><legend>' . $label . '</legend><div class="pt-apply__opts">';
						foreach ( $row[3] as $opt ) {
							$oid = $id . '-' . sanitize_title( $opt );
							echo '<label for="' . esc_attr( $oid ) . '"><input type="' . $type . '" id="' . esc_attr( $oid ) . '" name="' . esc_attr( $name ) . ( $type === 'checkbox' ? '[]' : '' ) . '" value="' . esc_attr( $opt ) . '"' . ( $req && $type === 'radio' ? ' required' : '' ) . '> ' . esc_html( $opt ) . '</label>';
						}
						echo '</div></fieldset>';
					} elseif ( $type === 'select' ) {
						echo '<label for="' . esc_attr( $id ) . '">' . $label . '</label><select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . ( $req ? ' required' : '' ) . ' data-positions>';
						echo '<option value="">' . esc_html( $f['lang'] === 'es' ? 'Seleccione' : 'Select' ) . '</option>';
						foreach ( $positions as $p ) { echo '<option value="' . esc_attr( $p ) . '">' . esc_html( $p ) . '</option>'; }
						echo '<option value="' . esc_attr( $f['general'] ) . '">' . esc_html( $f['general'] ) . '</option></select>';
					} elseif ( $type === 'textarea' ) {
						echo '<label for="' . esc_attr( $id ) . '">' . $label . '</label><textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="4"' . ( $req ? ' required' : '' ) . '></textarea>';
					} elseif ( $type === 'file' ) {
						echo '<label for="' . esc_attr( $id ) . '">' . $label . '</label><input type="file" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" accept=".pdf,.doc,.docx">';
					} else {
						echo '<label for="' . esc_attr( $id ) . '">' . $label . '</label><input type="' . esc_attr( $type ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . ( $req ? ' required' : '' ) . ( $type === 'number' ? ' min="0"' : '' ) . '>';
					}
					echo '</div>';
				endforeach; ?>
				</div>
				<p class="pt-apply__submit"><button class="pt-btn" type="submit"><?php echo esc_html( $f['submit'] ); ?></button></p>
			</form>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}

function pt_careers_client_key() {
	return md5( ( $_SERVER['REMOTE_ADDR'] ?? '' ) . ( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );
}

/** Handle the POST. */
function pt_careers_handle() {
	$forms  = pt_careers_forms();
	$region = sanitize_key( $_POST['region'] ?? '' );
	$back   = home_url( '/careers/' );
	if ( ! isset( $forms[ $region ] ) || ! wp_verify_nonce( $_POST['pt_nonce'] ?? '', 'pt_careers_' . $region ) ) {
		wp_safe_redirect( $back . '#apply' );
		exit;
	}
	$fail = function ( $msg ) use ( $back, $region ) {
		set_transient( 'pt_careers_err_' . pt_careers_client_key(), $msg, 300 );
		wp_safe_redirect( add_query_arg( 'region', $region, $back ) . '#apply' );
		exit;
	};
	// Spam: honeypot filled, or submitted in under 20 seconds.
	if ( ! empty( $_POST['website'] ) || ( time() - (int) ( $_POST['ts'] ?? 0 ) ) < 20 ) {
		wp_safe_redirect( add_query_arg( [ 'applied' => 'ok', 'region' => $region ], $back ) . '#apply' );
		exit;
	}
	$f    = $forms[ $region ];
	$spec = pt_careers_spec( $f['lang'] );
	$es   = $f['lang'] === 'es';
	$rows = [];
	$missing = [];
	$email = '';
	$midstream = false;
	$name = [];
	foreach ( $spec as $i => $row ) {
		$type = $row[0];
		if ( $type === 'h' || $type === 'p' ) { $rows[] = [ 'section', $row[1] ]; continue; }
		$n = pt_careers_field_name( $i );
		$v = $_POST[ $n ] ?? '';
		$v = is_array( $v ) ? implode( ', ', array_map( 'sanitize_text_field', wp_unslash( $v ) ) ) : ( $type === 'textarea' ? sanitize_textarea_field( wp_unslash( $v ) ) : sanitize_text_field( wp_unslash( $v ) ) );
		if ( $type === 'file' ) { $v = ''; }
		if ( ! empty( $row[2] ) && $v === '' && $type !== 'file' ) { $missing[] = $row[1]; }
		if ( $type === 'email' ) { $email = is_email( $v ) ? $v : ''; if ( ! $email && ! empty( $row[2] ) ) { $missing[] = $row[1]; } }
		if ( ( $row[5] ?? '' ) === 'midstream' ) { $midstream = in_array( $v, [ 'Yes', 'Sí' ], true ); }
		if ( in_array( $row[1], [ 'First Name', 'Last Name', 'Nombre', 'Apellido' ], true ) ) { $name[ $row[1] ] = $v; }
		$rows[] = [ $row[1], $v ];
	}
	if ( $missing ) {
		$fail( ( $es ? 'Faltan campos obligatorios: ' : 'Required fields are missing: ' ) . implode( ', ', array_slice( array_unique( $missing ), 0, 8 ) ) . ( count( $missing ) > 8 ? '…' : '' ) );
	}
	// Optional resume.
	$attach = [];
	$fileIdx = null;
	foreach ( $spec as $i => $row ) { if ( $row[0] === 'file' ) { $fileIdx = $i; } }
	$fn = $fileIdx !== null ? pt_careers_field_name( $fileIdx ) : null;
	if ( $fn && ! empty( $_FILES[ $fn ]['name'] ) ) {
		$file = $_FILES[ $fn ];
		$ok   = [ 'pdf' => 'application/pdf', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ];
		$chk  = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $ok );
		if ( $file['error'] || $file['size'] > 10 * 1024 * 1024 || empty( $chk['ext'] ) || ! isset( $ok[ $chk['ext'] ] ) ) {
			$fail( $es ? 'El currículum debe ser PDF o Word y pesar menos de 10 MB.' : 'The resume must be a PDF or Word file under 10 MB.' );
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		add_filter( 'upload_dir', 'pt_careers_upload_dir' );
		$moved = wp_handle_upload( $file, [ 'test_form' => false, 'mimes' => $ok, 'unique_filename_callback' => function ( $dir, $name, $ext ) { return wp_generate_password( 20, false ) . '-' . sanitize_file_name( $name ); } ] ); // random prefix: the folder is denied by .htaccess on Apache, and unguessable on Nginx hosts
		remove_filter( 'upload_dir', 'pt_careers_upload_dir' );
		if ( empty( $moved['file'] ) ) { $fail( $es ? 'No se pudo cargar el currículum.' : 'The resume could not be uploaded.' ); }
		$attach[] = $moved['file'];
	}
	// Store.
	$who  = trim( ( $name['First Name'] ?? $name['Nombre'] ?? '' ) . ' ' . ( $name['Last Name'] ?? $name['Apellido'] ?? '' ) );
	$pos  = $rows[0][1] ?? '';
	$body = '';
	foreach ( $rows as $r ) {
		if ( $r[0] === 'section' ) { $body .= "\n== " . $r[1] . " ==\n"; continue; }
		if ( $r[1] !== '' ) { $body .= $r[0] . ': ' . $r[1] . "\n"; }
	}
	$title = sprintf( '%s: %s (%s)', $who ?: 'Applicant', $pos ?: 'Application', $f['tab'] );
	$pid = wp_insert_post( [
		'post_type'    => 'pt_application',
		'post_status'  => 'private',
		'post_title'   => $title,
		'post_content' => $body,
		'meta_input'   => [ '_pt_region' => $region, '_pt_email' => $email, '_pt_resume' => $attach[0] ?? '', '_pt_midstream' => $midstream ? 1 : 0 ],
	] );
	// Email.
	$to  = pt_careers_recipients( $region, $midstream );
	$hdr = [ 'Content-Type: text/plain; charset=UTF-8' ];
	foreach ( $to['cc'] as $cc ) { $hdr[] = 'Cc: ' . $cc; }
	if ( $email ) { $hdr[] = 'Reply-To: ' . ( $who ? "$who <$email>" : $email ); }
	$subject = sprintf( '[ProTorque Careers] %s', $title );
	$intro   = "Application received " . date_i18n( 'F j, Y g:i a' ) . " via " . home_url( '/careers/' ) . ( $pid ? "\nStored in WordPress: " . admin_url( 'post.php?post=' . $pid . '&action=edit' ) : '' ) . "\n";
	wp_mail( $to['to'], $subject, $intro . $body, $hdr, $attach );
	wp_safe_redirect( add_query_arg( [ 'applied' => 'ok', 'region' => $region ], $back ) . '#apply' );
	exit;
}
add_action( 'admin_post_nopriv_pt_careers_apply', 'pt_careers_handle' );
add_action( 'admin_post_pt_careers_apply', 'pt_careers_handle' );

/** Resumes go in a protected subfolder of uploads. */
function pt_careers_upload_dir( $d ) {
	$d['subdir'] = '/applications' . $d['subdir'];
	$d['path']   = $d['basedir'] . $d['subdir'];
	$d['url']    = $d['baseurl'] . $d['subdir'];
	if ( ! file_exists( $d['basedir'] . '/applications/.htaccess' ) ) {
		wp_mkdir_p( $d['basedir'] . '/applications' );
		@file_put_contents( $d['basedir'] . '/applications/.htaccess', "Require all denied\nDeny from all\n" );
		@file_put_contents( $d['basedir'] . '/applications/index.html', '' );
	}
	return $d;
}

/** Applications post type: private records in wp-admin under Careers > Applications. */
add_action( 'init', function () {
	register_post_type( 'pt_application', [
		'label'               => 'Applications',
		'labels'              => [ 'name' => 'Applications', 'singular_name' => 'Application', 'menu_name' => 'Applications' ],
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=pt_job',
		'supports'            => [ 'title', 'editor' ],
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'exclude_from_search' => true,
	] );
} );
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'pt_application_meta', 'Applicant', function ( $post ) {
		$r = get_post_meta( $post->ID, '_pt_resume', true );
		echo '<p><strong>Region:</strong> ' . esc_html( strtoupper( get_post_meta( $post->ID, '_pt_region', true ) ) ) . '</p>';
		echo '<p><strong>Email:</strong> ' . esc_html( get_post_meta( $post->ID, '_pt_email', true ) ) . '</p>';
		echo '<p><strong>Midstream:</strong> ' . ( get_post_meta( $post->ID, '_pt_midstream', true ) ? 'Yes' : 'No' ) . '</p>';
		echo '<p><strong>Resume:</strong> ' . ( $r ? esc_html( basename( $r ) ) . ' (on the server under uploads/applications, attached to the notification email)' : 'None' ) . '</p>';
	}, 'pt_application', 'side' );
} );
