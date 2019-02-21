<?php

/**
 * Helper functions for registering / rendering settings
 */
class Propeller_Ads_Settings_Helper
{
	// Field types
	const FIELD_TYPE_CHECKBOX = 'checkbox';
	const FIELD_TYPE_INPUT_INTEGER = 'input_integer';
	const FIELD_TYPE_INPUT_TEXT = 'input_text';
	const FIELD_TYPE_DROPDOWN = 'dropdown';

	/**
	 * @var string $settings_page The slug-name of the settings page
	 */
	private $settings_page;

	/**
	 * @var string $settings_prefix Unique options prefix for plugin
	 */
	private $settings_prefix;

	public function __construct($settings_page)
	{
		$this->settings_page = $settings_page;
		$this->settings_prefix = str_replace('-', '_', $this->settings_page);
	}

	/**
	 * Gets publisher Anti Ad Block token
	 *
	 * @return string
	 */
	public function get_anti_adblock_token()
	{
		return $this->get_field_value('general', 'token');
	}

	/**
	 * Get field (option) value
	 *
	 * @param int $section_id
	 * @param int $field_id
	 *
	 * @return mixed    Option value
	 */
	public function get_field_value($section_id, $field_id)
	{
		return get_option($this->get_field_id($section_id, $field_id));
	}

	/**
	 * Delete field (option)
	 *
	 * @param int $section_id
	 * @param int $field_id
	 *
	 * @return mixed    Option value
	 */
	public function delete_field($section_id, $field_id)
	{
		return delete_option($this->get_field_id($section_id, $field_id));
	}

	public function get_field_id($section_id, $field_id)
	{
		return sprintf('%s_%s_%s', $this->settings_prefix, $section_id, $field_id);
	}

	/**
	 * Add settings section to plugin settings page
	 *
	 * @param array $config Key-value config (id, title)
	 */
	public function add_section($config)
	{
		add_settings_section(
			$this->get_section_id($config['id']),
			__($config['title'], $this->settings_page),   // TODO: is it ok for i18n tools?
			array($this, 'render_section'),   // TODO: Do we need to configure callback?
			$this->settings_page
		);
	}

	private function get_section_id($id)
	{
		return sprintf('%s_%s', $this->settings_prefix, $id);
	}

	/**
	 * Register setting and setup field rendering / sanitization
	 *
	 * @param array $config Key-value config (type, id, title, section)
	 */
	public function add_field($config)
	{
		$field_id = $this->get_field_id($config['section'], $config['id']);
		$renderer_name = 'render_' . $config['type'];
		$args = array_merge($config, array(
			'id' => $field_id,
			'label_for' => $field_id,
			'value' => $this->get_field_value($config['section'], $config['id']),
		));

		add_settings_field(
			$field_id,
			__($config['title'], $this->settings_page),
			array($this, $renderer_name),
			$this->settings_page,
			$this->get_section_id($config['section']),
			$args
		);

		register_setting(
			$this->settings_page,
			$field_id,
			$this->get_sanitize_callback($config['type'])
		);

		if (isset($config['validate']) && $config['validate'] === true) {
			register_setting(
				$this->settings_page,
				$field_id,
				array($this, 'validate_' . $field_id)
			);
		}
	}

	private function get_sanitize_callback($type)
	{
		if ($type === self::FIELD_TYPE_CHECKBOX) {
			return 'intval';
		}

		return '';
	}

	/**
	 * Render WP setting sections
	 * Callback function
	 *
	 * @param $page
	 */
	public function do_settings_sections($page)
	{
		global $wp_settings_sections, $wp_settings_fields;

		if (!isset($wp_settings_sections[$page])) {
			return;
		}

		foreach ((array)$wp_settings_sections[$page] as $section) {
			echo '<div class="card">';
			if ($section['title']) {
				echo "<h2>{$section['title']}</h2>\n";
			}

			if ($section['callback']) {
				call_user_func($section['callback'], $section);
			}

			if (!isset($wp_settings_fields[$page][$section['id']]) || $wp_settings_fields === null) {
				continue;
			}

			echo '<table class="form-table">';
			$this->do_settings_fields($page, $section['id']);
			echo '</table></div>';
		}
	}

	/**
	 * Render WP settings field
	 * Callback function
	 *
	 * @param $page
	 * @param $section
	 */
	function do_settings_fields($page, $section)
	{
		global $wp_settings_fields;

		if (!isset($wp_settings_fields[$page][$section])) {
			return;
		}

		foreach ((array)$wp_settings_fields[$page][$section] as $field) {
			$class = '';

			if (!empty($field['args']['class'])) {
				$class = ' class="' . esc_attr($field['args']['class']) . '"';
			}

			echo "<tr id='row_{$field['id']}'{$class}>";

			if (!empty($field['title'])) {
				if (!empty($field['args']['label_for'])) {
					echo '<th scope="row"><label for="' . esc_attr($field['args']['label_for']) . '">' . $field['title'] . '</label></th>';
				} else {
					echo '<th scope="row">' . $field['title'] . '</th>';
				}

				echo '<td>';
				call_user_func($field['callback'], $field['args']);
				echo '</td>';
			} else {
				echo '<th colspan="2">';
				call_user_func($field['callback'], $field['args']);
				echo '</th>';
			}

			echo '</tr>';
		}
	}

	public function render_section()
	{
		echo '';
	}

	public function render_checkbox($args)
	{
		?>
		<label>
			<input type="checkbox"
				   name="<?php echo $args['id']; ?>"
				   id="<?php echo $args['id']; ?>"
				<?php checked($args['value'], 1); ?>
				   value="1"
			/>
			<?php _e($args['checkbox_label'], 'propeller-ads') ?>
		</label>
		<?php $this->print_field_description($args); ?>
		<?php
	}

	private function print_field_description($args)
	{
		if (array_key_exists('description', $args)) {
			?>
			<p class="description"><?php echo $args['description']; ?></p>
			<?php
		}
	}

	public function render_input_integer($args)
	{
		$size = array_key_exists('size', $args) ? $args['size'] : 10;
		?>
		<input type="number"
			   name="<?php echo $args['id']; ?>"
			   id="<?php echo $args['id']; ?>"
			   value="<?php echo $args['value']; ?>"
			   size="<?php echo $size; ?>"
			   step="1"
			   min="1"
		/>
		<?php $this->print_field_description($args); ?>
		<?php
	}

	public function render_dropdown($args)
	{
		?>
		<select
			id="<?php echo $args['id']; ?>"
			name="<?php echo $args['id']; ?>"
			style="width: 100%"
		>
			<?php foreach ($args['options'] as $option): ?>
				<option
					value="<?php echo $option['value'] ?>"
					<?php selected($args['value'], $option['value']); ?>
				><?php echo $option['title'] ?></option>
			<?php endforeach; ?>
		</select>

		<?php $this->print_field_description($args); ?>
		<?php
	}

	public function render_input_text($args)
	{
		$size = array_key_exists('size', $args) ? $args['size'] : 10;
		?>
		<input type="text"
			   name="<?php echo $args['id']; ?>"
			   id="<?php echo $args['id']; ?>"
			   value="<?php echo $args['value']; ?>"
			   size="<?php echo $size; ?>"
		/>
		<?php $this->print_field_description($args); ?>
		<?php
	}

	/**
	 * Set field (option) value
	 *
	 * @param int    $section_id
	 * @param int    $field_id
	 * @param string $value
	 */
	public function set_field_value($section_id, $field_id, $value)
	{
		update_option($this->get_field_id($section_id, $field_id), $value);
	}

	/**
	 * Delete options after update token
	 */
	public function clear_settings()
	{
		$this->delete_field(Propeller_Ads_Zone_Helper::DIRECTION_PUSHNOTIFICATION, 'enabled');
		$this->delete_field(Propeller_Ads_Zone_Helper::DIRECTION_ONCLICK, 'enabled');
		$this->delete_field(Propeller_Ads_Zone_Helper::DIRECTION_INTERSTITIAL, 'enabled');

		$this->delete_field(Propeller_Ads_Zone_Helper::DIRECTION_PUSHNOTIFICATION, 'zone_id');
		$this->delete_field(Propeller_Ads_Zone_Helper::DIRECTION_ONCLICK, 'zone_id');
		$this->delete_field(Propeller_Ads_Zone_Helper::DIRECTION_INTERSTITIAL, 'zone_id');
	}

	/*** FORM VALIDATION ***/

	public function validate_propeller_ads_general_token($input)
	{

		if ($input !== '' && !preg_match('/^[a-f0-9]{40}$/', $input)) {
			Propeller_Ads_Messages::add_message('Error. Incorrect API token. Maybe you wrote zone id instead?', Propeller_Ads_Messages::TYPE_ERROR);

			return $this->get_anti_adblock_token();
		}

		return $input;
	}
}
