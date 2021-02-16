<?php
/**
 * Select field
 *
 * Setup attributes example:
 *
 *   'select_field_name' => array(
 *     'type'         => 'select',
 *     'label'        => esc_html__( 'Select Field', 'fl-builder' ),
 *     'default'      => 'option-1',
 *     'className'    => '',
 *     'multi-select' => false,
 *     'options'      => array(
 *       'option-1' => esc_html__( 'Option 1', 'fl-builder' ),
 *       'option-2' => array(
 *         'label'   => esc_html__( 'Premium Option 2', 'fl-builder' ),
 *         'premium' => true,
 *       ),
 *       'optgroup-1' => array(
 *         'label'   => esc_html__( 'Optgroup 1', 'fl-builder' ),
 *         'options' => array( *
 *           'option-3' => esc_html__( 'Option 3', 'fl-builder' ),
 *           'option-4' => array(
 *             'label'   => esc_html__( 'Premium Option 4', 'fl-builder' ),
 *             'premium' => true,
 *           ),
 *         ),
 *         'premium' => false,
 *       ),
 *     ),
 *     'toggle' => array(
 *       'option-1' => array(
 *         'fields'   => array( 'my_field_1', 'my_field_2' ),
 *         'sections' => array( 'my_section' ),
 *         'tabs'     => array( 'my_tab' ),
 *       ),
 *       'option-2' => array(),
 *     ),
 *     'hide'    => '', @todo Write example setup attribute value
 *     'trigger' => '', @todo Write example setup attribute value
 *   );
 *
 */
?>
<select name="{{data.name}}" class="st-bb-select-post" data-postTypeField="{{data.field.post_type_field}}" data-value="{{data.value}}">
</select>
