<?php

/**
 * Global ConneXion - Advanced Custom Fields - Address Field
 *
 * This addon to Advanced Custom Fields adds the capability for
 * a multi-component address input. It has the ability to customize the
 * individual components and the layout of the address block.
 *
 * @author Brian Zoetewey <brian.zoetewey@ccci.org>
 * @version 1.0.2
 */

class acf_field_address extends acf_field {
	/**
	 * Absolute Uri
	 *
	 * This is used to create urls to CSS and JavaScript files.
	 * @var string
	 */
	private $base_uri_abs;

	/**
	* WordPress Localization Text Domain
	*
	* The textdomain for the field is controlled by the helper class.
	* @var string
	*/
	private $l10n_domain;

	/**
	 * Class Constructor - Instantiates a new address field
	 */
	public function __construct() {

		//Get the textdomain from the Plugin class
		$this->l10n_domain = ACF_Address_Field_Plugin::L10N_DOMAIN;

		$this->name  = 'address-field';
		$this->label = __( 'Address', $this->l10n_domain );
		$this->category = __( 'Layout', 'acf' );

		$this->base_uri_abs = plugin_dir_url( __FILE__ );

		parent::__construct();

		// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.2'
		);

	}

	/**
	 * Registers and enqueues necessary CSS and javascript
	 *
	 * Called by ACF when creating a field
	 */
	public function field_group_admin_enqueue_scripts() {
		wp_register_style( 'acf-address-field', $this->base_uri_abs . '/address-field.css' );
		wp_register_script( 'acf-address-field', $this->base_uri_abs . '/address-field.js', array( 'jquery-ui-sortable' ) );

		wp_enqueue_style( 'acf-address-field' );
		wp_enqueue_script( 'acf-address-field' );
	}

	/**
	* Populates the fields array with defaults for this field type
	*
	* @param array $field
	* @return array
	*/
	private function set_field_defaults( &$field ) {
		$component_defaults = array(
			'address1'    => array(
				'label'         => __( 'Address 1', $this->l10n_domain ),
				'default_value' => '',
				'enabled'       => 1,
				'class'         => 'address1',
				'separator'     => '',
			),
			'address2'    => array(
				'label'         => __( 'Address 2', $this->l10n_domain ),
				'default_value' => '',
				'enabled'       => 1,
				'class'         => 'address2',
				'separator'     => '',
			),
			'address3'    => array(
				'label'         => __( 'Address 3', $this->l10n_domain ),
				'default_value' => '',
				'enabled'       => 1,
				'class'         => 'address3',
				'separator'     => '',
			),
			'city'        => array(
				'label'         => __( 'City', $this->l10n_domain ),
				'default_value' => '',
				'enabled'       => 1,
				'class'         => 'city',
				'separator'     => ',',
			),
			'state'       => array(
				'label'         => __( 'State', $this->l10n_domain ),
				'default_value' => '',
				'enabled'       => 1,
				'class'         => 'state',
				'separator'     => '',
			),
			'postal_code' => array(
				'label'         => __( 'Postal Code', $this->l10n_domain ),
				'default_value' => '',
				'enabled'       => 1,
				'class'         => 'postal_code',
				'separator'     => '',
			),
			'country'     => array(
				'label'         => __( 'Country', $this->l10n_domain ),
				'default_value' => '',
				'enabled'       => 1,
				'class'         => 'country',
				'separator'     => '',
			),
		);

		$layout_defaults = array(
			0 => array( 0 => 'address1' ),
			1 => array( 0 => 'address2' ),
			2 => array( 0 => 'address3' ),
			3 => array( 0 => 'city', 1 => 'state', 2 => 'postal_code', 3 => 'country' ),
		);

		$field[ 'address_components' ] = ( array_key_exists( 'address_components' , $field ) && is_array( $field[ 'address_components' ] ) ) ?
			wp_parse_args( (array) $field[ 'address_components' ], $component_defaults ) :
			$component_defaults;

		$field[ 'address_layout' ] = ( array_key_exists( 'address_layout', $field ) && is_array( $field[ 'address_layout' ] ) ) ?
			(array) $field[ 'address_layout' ] : $layout_defaults;

		return $field;
	}

	/**
	 * Creates the address field for inside post metaboxes
	 *
	 * @see acf_Field::create_field()
	 */
	public function create_field( $field ) {
		$this->set_field_defaults( $field );

		$components = $field[ 'address_components' ];
		$layout = $field[ 'address_layout' ];
		$values = (array) $field[ 'value' ];

		?>
		<div class="address">
		<?php foreach( $layout as $layout_row ) : if( empty($layout_row) ) continue; ?>
			<div class="address_row">
			<?php foreach( $layout_row as $name ) : if( empty( $name ) || !$components[ $name ][ 'enabled' ] ) continue; ?>
				<label class="<?php echo $components[ $name ][ 'class' ]; ?>">
					<?php echo $components[ $name ][ 'label' ]; ?>
					<input type="text" id="<?php echo $field[ 'name' ]; ?>[<?php echo $name; ?>]" name="<?php echo $field[ 'name' ]; ?>[<?php echo $name; ?>]" value="<?php echo esc_attr( $values[ $name ] ); ?>" />
				</label>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
		</div>
		<div class="clear"></div>
		<?php
	}

	/**
	 * Builds the field options
	 *
	 * @see acf_field::create_options()
	 * @param array $field
	 */
	public function create_options( $field ) {
		$this->set_field_defaults( $field );

		$key = $field['name'];
		$components = $field[ 'address_components' ];
		$layout = $field[ 'address_layout' ];
		$missing = array_keys( $components );

		?>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Address Components' , $this->l10n_domain ); ?></label>
					<p class="description">
						<strong><?php _e( 'Enabled', $this->l10n_domain ); ?></strong>: <?php _e( 'Is this component used.', $this->l10n_domain ); ?><br />
						<strong><?php _e( 'Label', $this->l10n_domain ); ?></strong>: <?php _e( 'Used on the add or edit a post screen.', $this->l10n_domain ); ?><br />
						<strong><?php _e( 'Default Value', $this->l10n_domain ); ?></strong>: <?php _e( 'Default value for this component.', $this->l10n_domain ); ?><br />
						<strong><?php _e( 'CSS Class', $this->l10n_domain ); ?></strong>: <?php _e( 'Class added to the component when using the api.', $this->l10n_domain ); ?><br />
						<strong><?php _e( 'Separator', $this->l10n_domain ); ?></strong>: <?php _e( 'Text placed after the component when using the api.', $this->l10n_domain ); ?><br />
					</p>
				</td>
				<td>
					<table>
						<thead>
							<tr>
								<th><?php _e( 'Field', $this->l10n_domain ); ?></th>
								<th><?php _e( 'Enabled', $this->l10n_domain ); ?></th>
								<th><?php _e( 'Label', $this->l10n_domain ); ?></th>
								<th><?php _e( 'Default Value', $this->l10n_domain ); ?></th>
								<th><?php _e( 'CSS Class', $this->l10n_domain ); ?></th>
								<th><?php _e( 'Separator', $this->l10n_domain ); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php _e( 'Field', $this->l10n_domain ); ?></th>
								<th><?php _e( 'Enabled', $this->l10n_domain ); ?></th>
								<th><?php _e( 'Label', $this->l10n_domain ); ?></th>
								<th><?php _e( 'Default Value', $this->l10n_domain ); ?></th>
								<th><?php _e( 'CSS Class', $this->l10n_domain ); ?></th>
								<th><?php _e( 'Separator', $this->l10n_domain ); ?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php foreach( $components as $name => $settings ) : ?>
								<tr>
									<td><?php echo $name; ?></td>
									<td>
										<?php
											create_field( array(
												'type'  => 'true_false',
												'name'  => "fields[{$key}][address_components][{$name}][enabled]",
												'value' => $settings[ 'enabled' ],
												'class' => 'address_enabled',
											) );
										?>
									</td>
									<td>
										<?php
											create_field( array(
												'type'  => 'text',
												'name'  => "fields[{$key}][address_components][{$name}][label]",
												'value' => $settings[ 'label' ],
												'class' => 'address_label',
											) );
										?>
									</td>
									<td>
										<?php
											create_field( array(
												'type'  => 'text',
												'name'  => "fields[{$key}][address_components][{$name}][default_value]",
												'value' => $settings[ 'default_value' ],
												'class' => 'address_default_value',
											) );
										?>
									</td>
									<td>
										<?php
											create_field( array(
												'type'  => 'text',
												'name'  => "fields[{$key}][address_components][{$name}][class]",
												'value' => $settings[ 'class' ],
												'class' => 'address_class',
											) );
										?>
									</td>
									<td>
										<?php
											create_field( array(
												'type'  => 'text',
												'name'  => "fields[{$key}][address_components][{$name}][separator]",
												'value' => $settings[ 'separator' ],
												'class' => 'address_separator',
											) );
										?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Address Layout' , $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( 'Drag address components to the desired location. This controls the layout of the address in post metaboxes and the get_field() api method.', $this->l10n_domain ); ?></p>
					<input type="hidden" name="address_layout_key" value="<?php echo $key; ?>" />
				</td>
				<td>
					<div class="address_layout">
						<?php
							$row = 0;
							foreach( $layout as $layout_row ) :
								if( count( $layout_row ) <= 0 ) continue;
						?>
							<label><?php printf( __( 'Line %d:', $this->l10n_domain ), $row + 1 ); ?></label>
							<ul class="row">
								<?php
									$col = 0;
									foreach( $layout_row as $name ) :
										if( empty( $name ) ) continue;
										if( !$components[ $name ][ 'enabled' ] ) continue;

										if( ( $index = array_search( $name, $missing, true ) ) !== false )
											array_splice( $missing, $index, 1 );
								?>
									<li class="item" name="<?php echo $name; ?>">
										<?php echo $components[ $name ][ 'label' ]; ?>
										<input type="hidden" name="<?php echo "fields[{$key}][address_layout][{$row}][{$col}]"?>" value="<?php echo $name; ?>" />
									</li>
								<?php
										$col++;
									endforeach;
								?>
							</ul>
						<?php
								$row++;
							endforeach;
							for( ; $row < 4; $row++ ) :
						?>
							<label><?php printf( __( 'Line %d:', $this->l10n_domain ), $row + 1 ); ?></label>
							<ul class="row">
							</ul>
						<?php endfor; ?>
						<label><?php _e( 'Not Displayed:', $this->l10n_domain ); ?></label>
						<ul class="row missing">
							<?php foreach( $missing as $name ) : ?>
								<li class="item <?php echo $components[ $name ][ 'enabled' ] ? '' : 'disabled'; ?>" name="<?php echo $name; ?>">
									<?php echo $components[ $name ][ 'label' ]; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</td>
			</tr>
		<?php
	}

	/**
	 * Returns the values of the field
	 *
	 * @see acf_Field::get_value()
	 * @param array $value
	 * @param int $post_id
	 * @param array $field
	 * @return array
	 */
	public function load_value( $value, $post_id, $field ) {
		$this->set_field_defaults( $field );

		$components = $field[ 'address_components' ];

		$defaults = array();
		foreach( $components as $name => $settings )
			$defaults[ $name ] = $settings[ 'default_value' ];

		$value = (array) $value;
		$value = wp_parse_args($value, $defaults);

		return $value;
	}

	/**
	 * Returns the value of the field for the advanced custom fields API
	 *
	 * @see acf_field::format_value_for_api()
	 * @param array $values
	 * @param int $post_id
	 * @param array $field
	 * @return string
	 */
	public function format_value_for_api( $values, $post_id, $field ) {
		$this->set_field_defaults( $field );

		$components = $field[ 'address_components' ];
		$layout = $field[ 'address_layout' ];

		$output = '';
		foreach( $layout as $layout_row ) {
			if( empty( $layout_row ) ) continue;
			$output .= '<div class="address_row">';
			foreach( $layout_row as $name ) {
				if( empty( $name ) || !$components[ $name ][ 'enabled' ] ) continue;
					$output .= sprintf(
						'<span %2$s>%1$s%3$s </span>',
						$values[ $name ],
						$components[ $name ][ 'class' ] ? 'class="' . esc_attr( $components[ $name ][ 'class' ] ) . '"' : '',
						$components[ $name ][ 'separator' ] ? esc_html( $components[ $name ][ 'separator' ] ) : ''
					);
			}
			$output .= '</div>';
		}

		return $output;
	}
}

// create field
new acf_field_address();
