<?php
namespace Leeflets\User\Addon;

class Social_Media extends \Leeflets\Addon\Base {

	/* Always need this boilerplace */
	function __construct() {
		parent::__construct( __FILE__ );
	}

	function init() {
		$script_url = $this->get_url( 'assets/js/application.js' );
		$this->template_script->add_enqueue( 'addon-social-media', $script_url, array( 'jquery' ), false, true );
		
		$style_url = $this->get_url( 'assets/css/main.css' );
		$this->template_style->add_enqueue( 'addon-social-media', $style_url );
		
		$this->data_file_path = $this->config->data_path . '/addon-social-media.json.php';

		$this->hook->add( 'template_get_content_fields', array( $this, 'get_content_fields' ) );

		$this->hook->add( 'content_set_data', array( $this, 'content_set_data' ) );
		$this->hook->add( 'content_get_data', array( $this, 'content_get_data' ) );

		$this->hook->add( 'footer', array( $this, 'template_html' ) );
	}

	function content_set_data( $values ) {
		if ( isset( $values['addon-social-media'] ) ) {
			$addon_values = $values['addon-social-media'];
			unset( $values['addon-social-media'] );
			
			$file = new \Leeflets\Data_File( $this->data_file_path, $this->config );
			$file->write( $addon_values, $this->filesystem );
		}

		return $values;
	}

	function content_get_data( $values ) {
		$values['addon-social-media'] = $this->get_data();
		return $values;
	}

	function get_data() {
		if ( file_exists( $this->data_file_path ) ) {
			$path = $this->data_file_path;
		}
		else {
			$path = $this->basepath . '/sample.json.php';
		}

		$file = new \Leeflets\Data_File( $path, $this->config );

		return $file->read();
	}

	function get_content_fields( $fields ) {
		$fields['addon-social-media'] = array(
	    	'type' => 'repeatable',
	    	'title' => 'Social Media',
	    	'description' => 'Fill in your social media links.',
	    	'empty-to-show' => 3,
	    	'elements' => array(
	    		'icon' => array(
	    			'type' => 'image',
	    			'label' => 'Icon',
	    			'subfolder' => 'social-media',
	    			'preview-style' => 'background-color: #333;',
					'versions' => array(
						'icon@2x' => array(
							'width' => 16,
							'height' => 16,
							'crop' => array( 'center', 'center' )
						)
					)
	    		),
	    		'title' => array(
	    			'type' => 'text',
	    			'label' => 'Title'
	    		),
	    		'url' => array(
	    			'type' => 'url',
	    			'label' => 'Link',
	    			'class' => 'input-block-level'
	    		)
	    	)
		);

		return $fields;
	}

	// TODO: Should really be using templates here
	function template_html() {
		$profiles = $this->get_data();
		if ( empty( $profiles ) ) {
			return;
		}
		?>
		<div id="testfield" class="span16">
			<div class="flyout-wrap">
				<a class="flyout-btn" href="#" title="Toggle" data-lf-edit="addon-social-media"><span>Flyout Menu Toggle</span></a>
				<ul class="flyout flyout-init">
					<?php
					foreach ( $profiles as $profile ) : 
						$icon_url = '';
						if ( !empty( $profile['icon']['path'] ) ) {
							if ( empty( $profile['icon']['in_addon'] ) ) {
								$icon_url = $this->router->get_uploads_url( $profile['icon']['path'] );
							}
							else {
								$icon_url = $this->router->get_addon_url( $profile['icon']['in_addon'], $profile['icon']['path'] );	
							}
						}
						?>
						<li>
							<a href="<?php echo $profile['url']; ?>">
								<span title="<?php echo htmlentities( $profile['title'] ); ?>" <?php echo ( $icon_url ) ? 'style="background-image: url(' . $icon_url . ');"' : ''; ?>>
									<?php echo $profile['title']; ?>
								</span>
							</a>
						</li>
						<?php
					endforeach; 
					?>
				</ul><!-- .flyout -->
			</div><!-- .flyout-wrap -->
		</div>
		<?php
	}
}
