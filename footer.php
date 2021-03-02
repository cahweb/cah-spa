			</div>
		</main>
		<?php echo ucfwp_get_footer_markup(); ?>
		<?php wp_footer(); ?>
		<script type="text/javascript">
			adroll_adv_id = "CQXU4CETCBEH7ODCRODRVR";
			adroll_pix_id = "U2VV6JXPCRDUPDG4FIAO33";
			adroll_version = "2.0";

			(function(w, d, e, o, a) {
				w.__adroll_loaded = true;
				w.adroll = w.adroll || [];
				w.adroll.f = [ 'setProperties', 'identify', 'track' ];
				var roundtripUrl = "https://s.adroll.com/j/" + adroll_adv_id
						+ "/roundtrip.js";
				for (a = 0; a < w.adroll.f.length; a++) {
					w.adroll[w.adroll.f[a]] = w.adroll[w.adroll.f[a]] || (function(n) {
						return function() {
							w.adroll.push([ n, arguments ])
						}
					})(w.adroll.f[a])
				}

				e = d.createElement('script');
				o = d.getElementsByTagName('script')[0];
				e.async = 1;
				e.src = roundtripUrl;
				o.parentNode.insertBefore(e, o);
			})(window, document);
			adroll.track("pageView");
		</script>
	</body>
</html>
