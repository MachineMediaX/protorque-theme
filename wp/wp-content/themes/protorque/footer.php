<?php
/**
 * Sitewide footer. Content per Development Brief Part 2, Section 8 and the approved homepage comp.
 */
$pt_home = home_url( '/' );
?>
<footer class="pt-footer">
	<div class="pt-footer__inner">
		<div class="pt-footer__mark">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logos/pt-mark-white-placeholder.png' ); ?>" alt="" width="60" height="72">
		</div>

		<div class="pt-footer__col">
			<h2 class="pt-footer__heading"><?php esc_html_e( 'Links', 'protorque' ); ?></h2>
			<ul class="pt-footer__links">
				<li><a href="<?php echo esc_url( $pt_home . 'about/' ); ?>">About</a></li>
				<li><a href="<?php echo esc_url( $pt_home . 'tubular-running-services/' ); ?>">Tubular Running Services</a></li>
				<li><a href="<?php echo esc_url( $pt_home . 'midstream-services/' ); ?>">Midstream Services</a></li>
				<li><a href="<?php echo esc_url( $pt_home . 'drilling-services/' ); ?>">Drilling Services</a></li>
				<li><a href="<?php echo esc_url( $pt_home . 'equipment-and-innovation/' ); ?>">Equipment</a></li>
				<li><a href="<?php echo esc_url( $pt_home . 'news/' ); ?>">News</a></li>
				<li><a href="<?php echo esc_url( $pt_home . 'careers/' ); ?>">Careers</a></li>
				<li><a href="<?php echo esc_url( $pt_home . 'contact-us/' ); ?>">Contact</a></li>
			</ul>
		</div>

		<div class="pt-footer__col">
			<h2 class="pt-footer__heading"><?php esc_html_e( 'Canada', 'protorque' ); ?></h2>
			<p>24 Hour Dispatch:<br><a href="mailto:PTCT.Sales@ptenergy.com">PTCT.Sales@ptenergy.com</a></p>
			<p>Grande Prairie<br><a href="tel:+17802886128">+1.780.288.6128</a><br>Red Deer</p>
			<p><a href="tel:+17809330404">+1.780.933.0404</a></p>
			<p>Canada Head Office<br>1700, 520-5th Ave SW<br>Calgary, AB, T2P 3R7</p>
		</div>

		<div class="pt-footer__col">
			<h2 class="pt-footer__heading"><?php esc_html_e( 'USA', 'protorque' ); ?></h2>
			<p>24 Hour Dispatch:<br><a href="tel:+17133005216">+1.713.300.5216</a><br><a href="mailto:PTE.Sales@ptenergy.com">PTE.Sales@ptenergy.com</a></p>
			<p>USA Head Office<br>3424 South County Road 1192<br>Midland, TX, 79706</p>
		</div>

		<div class="pt-footer__col">
			<h2 class="pt-footer__heading"><?php esc_html_e( 'South America', 'protorque' ); ?></h2>
			<p>24 Hour Dispatch:<br><a href="tel:+573213202213">+57.321.320.2213</a><br><a href="mailto:PTESAS.Sales@ptenergy.com">PTESAS.Sales@ptenergy.com</a></p>
			<p>Colombia Head Office<br>Bodega #47, Parque Industrial San Jorge, Municipio de Mosquera Cundinamarca</p>
		</div>

		<div class="pt-footer__col pt-footer__legal">
			<p>Copyright All Rights Reserved by ProTorque Energy Canada LTD &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?></p>
			<p><a href="<?php echo esc_url( $pt_home . 'privacy-policy/' ); ?>">Privacy Policy</a><br><a href="<?php echo esc_url( $pt_home . 'terms-and-conditions/' ); ?>">Terms and Conditions</a></p>
			<ul class="pt-footer__social" aria-label="<?php esc_attr_e( 'Social', 'protorque' ); ?>">
				<li><a href="https://www.instagram.com/" aria-label="Instagram"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 1.2.1 1.8.2 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1.1.4 2.2.1 1.3.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 1.2-.2 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1.1.4-2.2.4-1.3.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-1.2-.1-1.8-.2-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1.1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8c.1-1.2.2-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1.1-.4 2.2-.4C8.4 2.2 8.8 2.2 12 2.2M12 0C8.7 0 8.3 0 7.1.1 5.8.1 4.9.3 4.1.6c-.8.3-1.5.7-2.2 1.4C1.3 2.7.9 3.4.6 4.2.3 5 .1 5.8.1 7.1 0 8.3 0 8.7 0 12s0 3.7.1 4.9c.1 1.3.3 2.1.6 2.9.3.8.7 1.5 1.4 2.2.7.7 1.4 1.1 2.2 1.4.8.3 1.6.5 2.9.6 1.2.1 1.6.1 4.9.1s3.7 0 4.9-.1c1.3-.1 2.1-.3 2.9-.6.8-.3 1.5-.7 2.2-1.4.7-.7 1.1-1.4 1.4-2.2.3-.8.5-1.6.6-2.9.1-1.2.1-1.6.1-4.9s0-3.7-.1-4.9c-.1-1.3-.3-2.1-.6-2.9-.3-.8-.7-1.5-1.4-2.2C21.3 1.3 20.6.9 19.8.6 19 .3 18.2.1 16.9.1 15.7 0 15.3 0 12 0zm0 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.8a1.4 1.4 0 1 0 0 2.9 1.4 1.4 0 0 0 0-2.9z"/></svg></a></li>
				<li><a href="https://www.youtube.com/" aria-label="YouTube"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.6 15.6V8.4l6.2 3.6-6.2 3.6z"/></svg></a></li>
				<li><a href="https://www.linkedin.com/" aria-label="LinkedIn"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M20.4 20.4h-3.5v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9v5.7H9.4V9h3.4v1.6c.5-.9 1.6-1.9 3.4-1.9 3.6 0 4.3 2.4 4.3 5.5v6.2zM5.3 7.4a2.1 2.1 0 1 1 0-4.1 2.1 2.1 0 0 1 0 4.1zm1.8 13H3.6V9h3.5v11.4zM22.2 0H1.8C.8 0 0 .8 0 1.7v20.5c0 1 .8 1.8 1.8 1.8h20.5c1 0 1.8-.8 1.8-1.8V1.7C24 .8 23.2 0 22.2 0z"/></svg></a></li>
			</ul>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
